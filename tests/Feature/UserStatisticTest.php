<?php

namespace Tests\Feature;

use App\Habit;
use App\Promise;
use App\UserActivity;
use App\UserProfile;
use App\WeeklyChallenge;
use App\Wish;
use Tests\TestCase;

class UserStatisticTest extends TestCase
{
    /** @test */
    public function can_view_user_statistic()
    {
        // Arrange
        factory(UserProfile::class)->create([
            'user_id' => $this->user->id,
            'credits' => 0
        ]);

        $habit = factory(Habit::class)->create([
            'user_id' => $this->user->id,
            'credits' => 5,
            'count' => 8,
            'streak' => 8,
            'max_streak' => 20
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $habit->id,
            'subject_type' => Habit::class,
            'name' => 'habit_checked',
            'value' => $habit->credits
        ]);

        $challenge = factory(WeeklyChallenge::class)->create([
            'user_id' => $this->user->id,
            'credits' => 20,
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $challenge->id,
            'subject_type' => WeeklyChallenge::class,
            'name' => 'weekly_challenge_checked',
            'value' => $challenge->credits
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $challenge->id,
            'subject_type' => WeeklyChallenge::class,
            'name' => 'weekly_challenge_finished',
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $challenge->id,
            'subject_type' => WeeklyChallenge::class,
            'name' => 'weekly_challenge_failed',
        ]);

        $promise = factory(Promise::class)->create([
            'user_id' => $this->user->id,
            'credits' => 500,
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $promise->id,
            'subject_type' => Promise::class,
            'name' => 'promise_finished',
            'value' => $promise->credits
        ]);

        $wish = $this->createWish([
            'owner' => $this->user->id,
            'credits' => 500,
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $wish->id,
            'subject_type' => Wish::class,
            'name' => 'wish_purchased',
            'value' => $wish->credits
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $wish->id,
            'subject_type' => Wish::class,
            'name' => 'credits_contributed',
            'value' => 100
        ]);

        $wishTicket = $this->createWishTicket($wish);

        // Act
        $response = $this->get('/api/statistic/?api_token=' . $this->user->api_token);

        // Assert
        $response->assertStatus(200);

        $creditsEarned = $habit->credits + $challenge->credits + $promise->credits;
        $response->assertJson([
            'credits_earned' => $creditsEarned,
            'credits_contributed' => 100,
            'habits_checked' => 8,
            'max_streak' => 20,
            'weekly_challenges_checked' => 1,
            'weekly_challenges_finished' => 1,
            'weekly_challenges_failed' => 1,
            'promises_finished' => 1,
            'wishes_purchased' => 1,
        ]);
    }
}
