<?php

namespace Tests;

use App\Exceptions\Handler;
use App\User;
use App\Wish;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    use DatabaseMigrations;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->disableExceptionHandling();

        $this->user = factory(User::class)->create();
    }

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(Exception $e) {}

            public function render($request, Exception $e)
            {
                throw $e;
            }
        });
    }

    protected function createWish($data)
    {
        $wish = factory(Wish::class)->create($data);

        $this->user->wishes()->attach($wish);

        return $wish;
    }

    protected function createWishTicket($wish, $claimedAt = null)
    {
        $wishTicket = $wish->wishTickets()->create([
            'claimed_at' => $claimedAt
        ]);

        $this->user->wishTickets()->attach($wishTicket);

        return $wishTicket;
    }
}
