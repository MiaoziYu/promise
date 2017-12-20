<?php

namespace Tests\Unit;

use App\Checklist;
use App\Promise;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PromisesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_delete_promise_and_related_checklists()
    {
        // Arrange
        /** @var User $user */
        $user = factory(User::class)->create();

        $promise = $user->createPromise([
            'title' => 'KFC hot wings',
            'description' => '18 kfc hot wings',
        ],
            [
                ['text' => 'go to gym'], ['text' => 'read books']
            ]
        );

        $checklists = $promise->checklists()->get();

        // Act
        $user->deletePromise($promise->id);

        // Assert
        $this->assertNull($user->promises()->find($promise->id));
        $this->assertNull(Checklist::find($checklists[0]->id));
    }
}
