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
        $this->get('/usuarios/create')
            ->assertStatus(200)
            ->assertSee('Crear usuario');
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
        $this->assertEquals(0,User::count());
    }

    /** @test **/
    public function the_email_is_required()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',[
                'name' => 'Faustino',
                'email' => '',
                'password' => 'secret'
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(0,User::count());
    }

    /** @test **/
    public function the_email_must_be_valid()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',[
                'name' => 'Faustino',
                'email' => 'correo-no-valido',
                'password' => 'secret'
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(0,User::count());
    }

    /** @test **/
    public function the_email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',[
                'name' => 'Faustino',
                'email' => 'fvasquez@local.com',
                'password' => 'secret'
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(1,User::count());
    }

    /** @test **/
    public function the_password_is_required()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',[
                'name' => 'Faustino',
                'email' => 'fvasquez@local.com',
                'password' => ''
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password']);
        $this->assertEquals(0,User::count());
    }

    /** @test **/
    public function the_password_must_have_at_least_6_characters()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',[
                'name' => 'Faustino',
                'email' => 'fvasquez@local.com',
                'password' => '12345'
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password']);
        $this->assertEquals(0,User::count());
    }

    /** @test */
    function it_loads_the_edit_user_page()
    {
        $this->withoutExceptionHandling();
        $user =factory(User::class)->create();

        $this->get(route('users.edit',$user))
            ->assertStatus(200)
           ->assertViewIs('users.edit')
            ->assertViewHas('user', function($viewUser) use($user){
                return $viewUser->id === $user->id;
            });
    }

    /** @test **/
    public function it_updated_a_user()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->put(route('users.update',$user),[
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret'
        ])->assertRedirect(route('users.show',$user));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret'
        ]);
    }

    /** @test **/
    public function the_name_is_required_when_updating_a_user()
    {
        $user = factory(User::class)->create([
            'name'=>'Faustino Vasquez'
        ]);
        $this->from(route('users.edit',$user))
            ->put(route('users.update',$user),[
            'name' => '',
            'email' => 'fvasquez@local.com',
            'password' =>'secret'
        ])->assertRedirect(route('users.edit',$user))
            ->assertSessionHasErrors(['name']);
        $this->assertDatabaseHas('users',[
            'name' => 'Faustino Vasquez'
        ]);
    }


    /** @test **/
    public function the_email_is_required_when_updating_the_user()
    {
        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit',$user))
            ->put(route('users.update',$user),[
                'name' => 'Faustino Vasquez',
                'email' => '',
                'password' =>'secret'
            ])->assertRedirect(route('users.edit',$user))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users',[
            'email' => 'fvasquez@local.com'
        ]);
    }

    /** @test **/
    public function the_email_must_be_valid_when_updating_the_user()
    {
        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit',$user))
            ->put(route('users.update',$user),[
                'name' => 'Faustino Vasquez',
                'email' => 'the-email',
                'password' =>'secret'
            ])->assertRedirect(route('users.edit',$user))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users',[
            'email' => 'fvasquez@local.com'
        ]);
    }

    /** @test **/
    public function the_email_must_be_unique_when_updating_the_user()
    {
        factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $user = factory(User::class)->create([
            'email' => 'other@local.com'
        ]);
        $this->from(route('users.edit',$user))
            ->put(route('users.update',$user),[
                'name' => 'Faustino Vasquez',
                'email' => 'fvasquez@local.com',
                'password' =>'secret'
            ])->assertRedirect(route('users.edit',$user))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users',[
            'email' => 'other@local.com'
        ]);
    }

    /** @test **/
    public function the_password_is_optional_when_updating_the_user()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('123456')
        ]);
        $this->from(route('users.edit',$user))
            ->put(route('users.update',$user),[
                'name' => 'Faustino Vasquez',
                'email' => 'fvasquez@local.com',
                'password' =>''
            ])->assertRedirect(route('users.show',$user));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' => '123456'
        ]);
    }


    /** @test **/
    public function the_users_email_can_stay_the_same_when_updating_the_user()
    {
        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit',$user))
            ->put(route('users.update',$user),[
                'name' => 'Faustino Vasquez',
                'email' => 'fvasquez@local.com',
                'password' =>'123456'
            ])->assertRedirect(route('users.show',$user));

        $this->assertDatabaseHas('users',[
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
        ]);
    }

    /** @test **/
    public function the_password_must_have_at_least_6_characters_when_updating_the_user()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit',$user))
            ->put(route('users.update',$user),[
                'name' => 'Faustino Vasquez',
                'email' => 'fvasquez@local.com',
                'password' =>'123456'
            ])->assertRedirect(route('users.show',$user));
        $this->assertDatabaseHas('users',[
            'email' => 'fvasquez@local.com'
        ]);
    }

}
