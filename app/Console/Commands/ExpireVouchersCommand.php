<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VoucherQueue;
use App\Http\Controllers\GetVoucherController;
use Carbon\Carbon;

class ExpireVouchersCommand extends Command
{
    protected $signature = 'vouchers:expire';
    protected $description = 'Expire vouchers and free WAN slots';

    public function handle()
    {
        $now = Carbon::now();

        $expired = VoucherQueue::where('expiry_time', '<=', $now)->get();

        foreach ($expired as $item) {

            // decrement WAN counter
            GetVoucherController::decrementWanCounterByPort(
                $item->reseller_id,
                $item->wan_port
            );

            // delete from queue
            $item->delete();
        }

        return 0;
    }
}
