<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Waitlist;
use App\Models\WaitlistDailySnapshot;
use Carbon\Carbon;

class SnapshotWaitlistDaily extends Command
{
    protected $signature = 'waitlist:snapshot';
    protected $description = 'Daily snapshot of unserved waitlist users';

    public function handle()
    {
        $yesterday = Carbon::yesterday();

        // get all resellers who have waiting users
        $resellers = Waitlist::where('status', 'waiting')
            ->select('reseller_id')
            ->distinct()
            ->pluck('reseller_id');

        foreach ($resellers as $resellerId) {

            $waiting = Waitlist::where('reseller_id', $resellerId)
                ->where('status', 'waiting')
                ->get();

            $count24 = 0;
            $count48 = 0;
            $count72 = 0;

            foreach ($waiting as $w) {
                $hours = $w->waiting_since->diffInHours(now());

                if ($hours >= 24) $count24++;
                if ($hours >= 48) $count48++;
                if ($hours >= 72) $count72++;
            }

            WaitlistDailySnapshot::updateOrCreate(
                [
                    'reseller_id' => $resellerId,
                    'snapshot_date' => $yesterday,
                ],
                [
                    'waiting_count' => $waiting->count(),
                    'waiting_24h_plus' => $count24,
                    'waiting_48h_plus' => $count48,
                    'waiting_72h_plus' => $count72,
                ]
            );
        }

        $this->info('Waitlist daily snapshot saved.');
    }
}
