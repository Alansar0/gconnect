<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
        'prev_balance',
        'new_balance',
        'cashback_balance',
        'voucher_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function credit($amount, $description = 'Manual Credit')
    {
        $prev = $this->balance;
        $this->balance += $amount;
        $this->prev_balance = $prev;
        $this->new_balance = $this->balance;
        $this->save();

        Transaction::create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
             'reference' => strtoupper(uniqid('TXN')),
            'status' => 'success',
        ]);
    }


    public function debit($amount, $description = 'Manual Debit')
    {
        $prev = $this->balance;
        $this->balance -= $amount;
        $this->prev_balance = $prev;
        $this->new_balance = $this->balance;
        $this->save();

        Transaction::create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description,
            'reference' => strtoupper(uniqid('TXN')),
            'status' => 'success',
        ]);
    }
     /**
     * Add cashback from quiz or reward.
     */
    public function addCashback($amount)
    {
        $this->cashback_balance += $amount;

        // 👇 Don't deduct cashback, just sync voucher equivalence
        $this->voucher_balance = floor($this->cashback_balance / 200);

        $this->save();
    }

    /**
     * User purchases something using vouchers.
     * Deduct voucher + equivalent cashback.
     */
    public function useVoucher($count = 1)
    {
        $voucherValue = 200 * $count;

        // Ensure user has enough balance
        if ($this->cashback_balance >= $voucherValue && $this->voucher_balance >= $count) {
            $this->cashback_balance -= $voucherValue;
            $this->voucher_balance -= $count;
            $this->save();
            return true;
        }

        return false;
    }

}
