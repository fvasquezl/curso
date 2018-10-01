<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_display_the_users_details()
    {
        $user = factory(User::class)->create([
            'name' => 'Faustino Vasquez',
        ]);

        $this->get(route('users.show',$user))
            ->assertStatus(200)
            ->assertSee('Faustino Vasquez');
    }
    /** @test **/
    public function it_displays_a_404_if_the_user_is_not_found()
    {
        $this->withExceptionHandling();

        $this->get(route('users.show','999'))
            ->assertStatus(404)
            ->assertSee('Pagina no encontrada');

    }
}
