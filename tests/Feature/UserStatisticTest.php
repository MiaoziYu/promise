<?php

namespace Tests\Feature;

use App\Habit;
use App\Promise;
use App\User;
use App\UserActivity;
use App\UserProfile;
use App\WeeklyChallenge;
use App\Wish;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserStatisticTest extends TestCase
{
    use DatabaseMigrations;

    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();

        $this->user = factory(User::class)->create();
    }

    private function createWish($data)
    {
        $wish = factory(Wish::class)->create($data);

        $this->user->wishes()->attach($wish);

        return $wish;
    }

    private function createWishTicket($wish, $claimedAt = null)
    {
        $wishTicket = $wish->wishTickets()->create([
            'claimed_at' => $claimedAt
        ]);

        $this->user->wishTickets()->attach($wishTicket);

        return $wishTicket;
    }

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
            'streak' => 8
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
            'name' => 'workout at gym',
            'description' => 'at least 2 time',
            'credits' => 20,
            'goal' => 2,
            'count' => 2,
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $challenge->id,
            'subject_type' => WeeklyChallenge::class,
            'name' => 'weekly_challenge_checked',
            'value' => $challenge->credits
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
            'name' => 'potato chip',
            'description' => 'buy a package of potato chip',
            'credits' => 500,
            'image_link' =>'example_link'
        ]);

        factory(UserActivity::class)->create([
            'user_id' => $this->user->id,
            'subject_id' => $wish->id,
            'subject_type' => Wish::class,
            'name' => 'wish_purchased',
            'value' => $wish->credits
        ]);

        $wishTicket = $this->createWishTicket($wish);

        // Act
        $response = $this->get('/api/statistic/?api_token=' . $this->user->api_token);

        // Assert
        $response->assertStatus(200);

        $creditsEarned = $habit->credits + $challenge->credits + $promise->credits;
        $response->assertJson([
            'credits_earned' => $creditsEarned
        ]);
    }
}
