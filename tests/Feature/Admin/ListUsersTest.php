<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_the_users_list()
    {
        factory(User::class)->create([
            'name' => 'Faustino',
            'website' => 'algo.com'
        ]);
        factory(User::class)->create([
            'name' => 'Sebastian'
        ]);


        $this->get(route('users.index'))
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Faustino')
            ->assertSee('Sebastian');
    }

    /** @test */
    public function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get(route('users.index'))
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }
}
