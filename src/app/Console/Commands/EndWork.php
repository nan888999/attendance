<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Attendance;

class EndWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:endWork';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update updated_at to next day 0am if created_at and updated_at are the same';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $attendances = Attendance::whereColumn('created_at', 'updated_at')->whereDate('created_at', $today)->get();

        foreach($attendances as $attendance) {
            $attendance->updated_at = $today->copy()->addDay()->startOfDay();
            $attendance->save();
        }

        $this->info('Updated ' . $attendances->count() . ' records.');
        return 0;
    }
}
