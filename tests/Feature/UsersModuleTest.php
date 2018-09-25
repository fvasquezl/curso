<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
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


        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Faustino')
            ->assertSee('Sebastian');
    }

    /** @test */
    public function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_display_the_users_details()
    {
        $user = factory(User::class)->create([
            'name' => 'Faustino Vasquez',
        ]);

        $this->get('/usuarios/' . $user->id)
            ->assertStatus(200)
            ->assertSee('Faustino Vasquez');
    }
    /** @test **/
    public function it_displays_a_404_if_the_user_is_not_found()
    {
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Pagina no encontrada');
        
    }

    /** @test **/
    public function it_creates_a_new_user()
    {
        $this->post('/usuarios/',[
                'name' => 'Faustino Vasquez',
                'email' => 'fvasquez@local.com',
                'password' => 'secret'
            ])->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret'
        ]);
    }


    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear Usuario');
    }
    
    /** @test **/
    public function the_name_is_required()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',[
            'name' => '',
            'email' => 'fvasquez@local.com',
            'password' => 'secret'
        ])->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users',[
            'email' => 'fvasquez@local.com',
        ]);
    }
    
}
