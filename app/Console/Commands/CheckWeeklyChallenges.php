<?php

namespace App\Console\Commands;

use App\WeeklyChallenge;
use Illuminate\Console\Command;

class CheckWeeklyChallenges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenge:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if weekly challenges are failed';

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
        $weeklyChallenges = WeeklyChallenge::where('failed', '!=', true)->get();

        collect($weeklyChallenges)->each(function($challenge) {
            if ($challenge->goal > $challenge->count) {
                $challenge->update([
                    'failed' => true
                ]);
            }
        });
    }
}
