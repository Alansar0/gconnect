<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voucher;
use App\Models\VoucherQueue;
use App\Models\Waitlist;
use App\Models\RouterSetting;
use App\Http\Controllers\GetVoucherController;
use App\Notifications\WaitlistAvailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExpireVouchers extends Command
{
    protected $signature = 'vouchers:expire';
    protected $description = 'Expire vouchers, free WAN slots, and process waitlist';

    
    public function handle()
{
    DB::transaction(function () {

        $now = now();

        /* ===============================
         | 1. Expire vouchers & free WAN
         =============================== */
        $expiredQueues = VoucherQueue::where('expiry_time', '<=', $now)
            ->lockForUpdate()
            ->get();

        foreach ($expiredQueues as $queue) {

            if ($queue->voucher_id) {
                Voucher::where('id', $queue->voucher_id)
                    ->where('status', '!=', 'expired')
                    ->update(['status' => 'expired']);
            }

            $settings = RouterSetting::where('reseller_id', $queue->reseller_id)
                ->lockForUpdate()
                ->first();

            if ($settings) {
                if ($queue->wan_port === 'ether1' && $settings->wan1_current_count > 0) {
                    $settings->decrement('wan1_current_count');
                }

                if ($queue->wan_port === 'ether2' && $settings->wan2_current_count > 0) {
                    $settings->decrement('wan2_current_count');
                }
            }

            $queue->delete();
        }

        /* ===============================
         | 2. Expire stale waitlist holds
         =============================== */
        Waitlist::where('status', 'notified')
            ->where('expected_available_at', '<=', $now)
            ->update(['status' => 'expired']);

        /* ===============================
         | 3. Promote next waiting user
         =============================== */
        $resellerIds = $expiredQueues->pluck('reseller_id')->unique();

        foreach ($resellerIds as $resellerId) {

            $settings = RouterSetting::where('reseller_id', $resellerId)->first();
            if (!$settings) continue;

            $hasFreeSlot =
                $settings->wan1_current_count < $settings->wan1_limit ||
                $settings->wan2_current_count < $settings->wan2_limit;

            if (!$hasFreeSlot) continue;

            $next = Waitlist::where('reseller_id', $resellerId)
                ->where('status', 'waiting')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->first();

            if ($next) {
                $minutes = config('waitlist.reservation_minutes', 10);

                $next->update([
                    'status' => 'notified',
                    'notified_at' => $now,
                    'expected_available_at' => $now->copy()->addMinutes($minutes),
                ]);

                optional($next->user)->notify(
                    new WaitlistAvailable($next, $minutes)
                );
            }
        }
    });

    $this->info('Voucher expiry & waitlist processing completed.');
}

}
