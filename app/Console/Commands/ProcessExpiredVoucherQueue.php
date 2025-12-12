<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VoucherQueue;
use App\Models\Waitlist;
use App\Models\RouterSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\WaitlistAvailable;
use Illuminate\Support\Facades\Log;

class ProcessExpiredVoucherQueue extends Command
{
    protected $signature = 'process:expired-voucher-queue';
    protected $description = 'Process expired voucher_queue entries, free counters and notify waitlist';

    public function handle()
    {
        $now = Carbon::now();

        $expired = VoucherQueue::where('expiry_time', '<=', $now)->get();

        foreach ($expired as $q) {
            DB::transaction(function () use ($q, $now) {
                
                $settings = RouterSetting::where('reseller_id', $q->reseller_id)->first();

                // Decrement WAN counter safely
                if ($settings) {
                    $wan = $q->wan_port;

                    if ($wan === 'ether1' && $settings->wan1_current_count > 0) {
                        $settings->decrement('wan1_current_count');

                    } elseif ($wan === 'ether2' && $settings->wan2_current_count > 0) {
                        $settings->decrement('wan2_current_count');

                    } else {
                        Log::warning("WAN decrement skipped: {$wan} is zero or invalid for reseller {$q->reseller_id}");
                    }

                } else {
                    Log::warning("RouterSetting not found for reseller {$q->reseller_id}");
                }

                // Delete expired queue entry
                $q->delete();

                // Notify next user in waitlist (FIFO)
                $next = Waitlist::where('reseller_id', $q->reseller_id)
                    ->where('status', 'waiting')
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($next) {
                    $next->update([
                        'status' => 'notified',
                        'notified_at' => $now,
                        'expected_available_at' => $now
                    ]);

                    if ($next->user) {
                        $next->user->notify(
                            new WaitlistAvailable($next, config('waitlist.reservation_minutes', 5))
                        );
                    }
                }

                Log::info("Expired queue processed: ID {$q->id} WAN: {$q->wan_port}");
            });
        }

        $this->info("Processed {$expired->count()} expired voucher_queue entries.");
        return 0;
    }
}
