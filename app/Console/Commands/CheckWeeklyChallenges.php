<?php

namespace App\Console\Commands;

use App\Events\UserActed;
use App\UserProfile;
use App\WeeklyChallenge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckWeeklyChallenges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challenges:check';

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
        $weeklyChallenges = WeeklyChallenge::where('failed', '!=', 1)->get();

        collect($weeklyChallenges)->each(function($challenge) {
            if ($challenge->goal > $challenge->count) {
                DB::transaction(function() use ($challenge) {
                    $challenge->update([
                        'count' => 0,
                        'failed' => 1
                    ]);

                    $userProfile = UserProfile::where('user_id', $challenge->user_id)->first();
                    $userProfile->update([
                        'credits' => $userProfile->first()->credits - floor($challenge->credits / 2),
                        'weekly_challenges_failed' => $userProfile->weekly_challenges_finished + 1
                    ]);

                    event(new UserActed([
                        'user_id' => $challenge->user_id,
                        'subject_id' => $challenge->id,
                        'subject_type' => WeeklyChallenge::class,
                        'name' => 'weekly_challenge_failed',
                        'value' => floor($challenge->credits / 2)
                    ]));
                });
            } else {
                DB::transaction(function() use ($challenge) {
                    $challenge->update([
                        'count' => 0
                    ]);
                    $userProfile = UserProfile::where('user_id', $challenge->user_id)->first();
                    $userProfile->update([
                        'weekly_challenges_finished' => $userProfile->weekly_challenges_failed + 1
                    ]);

                    event(new UserActed([
                        'user_id' => $challenge->user_id,
                        'subject_id' => $challenge->id,
                        'subject_type' => WeeklyChallenge::class,
                        'name' => 'weekly_challenge_finished',
                    ]));
                });
            }
        });
    }
}
