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

     public function virtualAccounts()
    {
        return $this->hasMany(VirtualAccount::class);
    }


    public function credit($amount, $reference = null, $description = 'Manual Credit')
        {
            $prev = $this->balance;
            $this->balance += $amount;
            $this->prev_balance = $prev;
            $this->new_balance = $this->balance;
            $this->save();

            Transaction::create([
                'user_id'      => $this->user_id,
                'amount'       => $amount,
                'type'         => 'credit',
                'description'  => $description,
                'reference'    => $reference ?? strtoupper(uniqid('TXN_')),
                'status'       => 'success',
                'prev_balance' => $prev,
                'new_balance'  => $this->balance,
            ]);
        }



    public function debit($amount, $description = 'Manual Debit')
        {
            if ($this->balance < $amount) {
                throw new \Exception('Insufficient wallet balance.');
            }

            $prev = $this->balance;
            $this->balance -= $amount;
            $this->prev_balance = $prev;
            $this->new_balance = $this->balance;
            $this->save();

            Transaction::create([
                'user_id'      => $this->user_id,
                'amount'       => $amount,
                'type'         => 'debit',
                'description'  => $description,
                'reference'    => strtoupper(uniqid('TXN_')),
                'status'       => 'success',
                'prev_balance' => $prev,
                'new_balance'  => $this->balance,
            ]);
        }

     /**
     * Add cashback from quiz or reward.
     */
    public function addCashback($amount, $voucherRate = 200)
        {
            $this->cashback_balance += $amount;

            // use dynamic voucher rate
            $this->voucher_balance = floor($this->cashback_balance / $voucherRate);

            $this->save();
        }
    public function debitCashback($amount, $description = 'Cashback used')
        {
            if ($this->cashback_balance < $amount) {
                throw new \Exception('Insufficient cashback balance.');
            }

            $prev = $this->cashback_balance;
            $this->cashback_balance -= $amount;

            $this->prev_balance = $prev;
            $this->new_balance  = $this->cashback_balance;
            $this->save();

            Transaction::create([
                'user_id'      => $this->user_id,
                'amount'       => $amount,
                'type'         => 'debit',
                'description'  => $description,
                'reference'    => strtoupper(uniqid('CB_')),
                'status'       => 'success',
                'prev_balance' => $prev,
                'new_balance'  => $this->cashback_balance,
            ]);
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
