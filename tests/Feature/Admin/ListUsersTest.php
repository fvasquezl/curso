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
    public function it_paginates_the_users()
    {
        factory(User::class,12)->create([
            'created_at' => now()->subDays(4)
        ]);


        factory(User::class)->create([
            'name' => 'Tercer usuario',
            'created_at' => now()->subDays(5)
        ]);


        factory(User::class)->create([
            'name' => 'Decimoseptimo usuario',
            'created_at' => now()->subDays(2)
        ]);

        factory(User::class)->create([
            'name' => 'Decimosexto usuario',
            'created_at' => now()->subDays(3)
        ]);

        factory(User::class)->create([
            'name' => 'Primer usuario',
            'created_at' => now()->subWeek()
        ]);

        factory(User::class)->create([
            'name' => 'Segundo usuario',
            'created_at' => now()->subDays(6)
        ]);

        $this->get(route('users.index'))
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Decimoseptimo usuario',
                'Decimosexto usuario',
                'Tercer usuario',
            ])
            ->assertDontSee('Segundo usuario')
            ->assertDontSee('Primer usuario');

        $this->get('/usuarios?page=2')
            ->assertSeeInOrder([
                'Segundo usuario',
                'Primer usuario'
            ])
            ->assertDontSee('Tercer usuario');
    }

    /** @test */
    public function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get(route('users.index'))
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    public function it_shows_the_deleted_users()
    {
        factory(User::class)->create([
            'name' => 'Faustino',
            'deleted_at' => now(),
        ]);
        factory(User::class)->create([
            'name' => 'Sebastian',
        ]);

        $this->get('/usuarios/papelera')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios en papelera')
            ->assertSee('Faustino')
            ->assertDontSee('Sebastian');
    }
}
