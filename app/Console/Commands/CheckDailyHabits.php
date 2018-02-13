<?php

namespace App\Console\Commands;

use App\Habit;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDailyHabits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'habits:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if daily habits are finished';

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
     * @return mixed
     */
    public function handle()
    {
        $habits = Habit::all();

        collect($habits)->each(function($habit) {
            if ($habit->frozen) {
                $habit->update([
                    'frozen' => false
                ]);

                return true;
            }

            if ($habit->checked_at === null) {
                return $habit;
            }

            if (!Carbon::parse($habit->checked_at)->isToday() and !Carbon::parse($habit->checked_at)->isYesterday()) {
                $habit->update([
                    'streak' => 0
                ]);
            }
        });
    }
}
