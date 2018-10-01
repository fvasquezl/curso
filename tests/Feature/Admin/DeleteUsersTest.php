<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUsersTest extends TestCase
{
    use RefreshDatabase;
    /** @test **/
    public function it_deletes_a_user()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->delete(route('users.delete',$user))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseEmpty('users');

    }
}
