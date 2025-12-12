<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\VoucherQueue;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // expire vouchers every minute
        // $schedule->command('vouchers:expire')->everyMinute();

        // handle WAN decrement + waitlist notifications
        $schedule->command('process:expired-voucher-queue')->everyMinute();

        // daily stats (optional)
        $schedule->call(function () {
            \App\Models\WaitlistDailyStat::create([
                'count' => \App\Models\Waitlist::whereDate('created_at', today())->count()
            ]);
        })->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
