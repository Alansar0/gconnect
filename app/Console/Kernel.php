<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\VoucherQueue;

class Kernel extends ConsoleKernel
{
    
    protected function schedule(Schedule $schedule)
        {
            $schedule->command('vouchers:expire')->everyMinute();

            $schedule->command('voucher:expiry-reminder')->everyMinute();

            $schedule->command('waitlist:snapshot')->dailyAt('00:05');

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
