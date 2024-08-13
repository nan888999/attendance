<?php

namespace App\Console;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // 退勤の打刻を忘れた場合に自動入力
        $schedule->command('command:EndWork')->dailyAt('00:00');

        // メール認証がされていないユーザーを削除
        $schedule->call(function() {
            $expiredUsers = User::whereNull('name')
            ->where('created_at', '<', Carbon::now()->subMinutes(60))
            ->delete();
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
