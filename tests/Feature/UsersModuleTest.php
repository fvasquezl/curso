<?php

namespace Tests\Feature;

use App\Models\Profession;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;
    protected $profession;

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
       // $this->withoutExceptionHandling();
        $this->post(route('users.store'), $this->getValidData())->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret',
        ]);
        $this->assertDatabaseHas('user_profiles',[
            'bio' => 'Programador de laravel',
            'twitter' => 'https://twitter.com/fvasquezl',
            'user_id' => User::findByEmail('fvasquez@local.com')->id,
            'profession_id' => $this->profession->id,
        ]);
    }

    /** @test **/
    public function the_twitter_test_is_optional()
    {
       // $this->withoutExceptionHandling();
        $this->from(route('users.create'))
        ->post('/usuarios/',$this->getValidData([
            'twitter'=> null
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret'
        ]);
        $this->assertDatabaseHas('user_profiles',[
            'bio' => 'Programador de laravel',
            'twitter' => null,
            'user_id' => User::first()->id,
        ]);
    }

    /** @test **/
    public function the_profession_id_field_is_optional()
    {
       // $this->withoutExceptionHandling();
        $this->post(route('users.store'),$this->getValidData([
            'profession_id' => ''
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret',
        ]);
//        $this->assertDatabaseHas('user_profiles',[
//            'bio' => 'Programador de laravel',
//            'user_id' => User::findByEmail('fvasquez@local.com')->id,
//            'profession_id' => null
//        ]);
    }

    /** @test */
    function it_loads_the_new_users_page()
    {
        $profession= factory(Profession::class)->create();

        $this->get(route('users.create'))
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function($professions) use ($profession){
                return $professions->contains($profession);
            });
    }
    
    /** @test **/
    public function the_name_is_required()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post(route('users.store'),$this->getValidData([
                'name' => ''
            ]))->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['name']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test **/
    public function the_email_is_required()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',$this->getValidData([
                'email' => ''
            ]))->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseEmpty('users');

    }

    /** @test **/
    public function the_email_must_be_valid()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',$this->getValidData([
                'email' => 'correo-no-valido'
            ]))->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test **/
    public function the_email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',$this->getValidData([
                'email' => 'fvasquez@local.com'
            ]))->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(1,User::count());
    }

    /** @test **/
    public function the_password_is_required()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',$this->getValidData([
                'password'=>''
            ]))->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test **/
    public function the_password_must_have_at_least_6_characters()
    {
        //$this->withoutExceptionHandling();
        $this->from(route('users.create'))
            ->post('/usuarios/',$this->getValidData([
                'password' => '12345'
            ]))->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test **/
    public function the_profession_must_be_valid()
    {
       // $this->withExceptionHandling();
        $this->from(route('users.create'))
            ->post(route('users.store'),$this->getValidData([
                'profession_id' => '999'
            ]))->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');

    }


    /** @test **/
    public function only_non_delete_professions_are_valid()
    {
        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' =>now()->format('Y-m-d'),
        ]);
        // $this->withExceptionHandling();
        $this->from(route('users.create'))
            ->post(route('users.store'),$this->getValidData([
                'profession_id' => $deletedProfession->id
            ]))->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');

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

    /** @test **/
    public function it_deletes_a_user()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->delete(route('users.delete',$user))
            ->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users',[
            'id' => $user->id,
        ]);

    }

    /**
     * @param array $custom
     * @return array
     */
    protected function getValidData(array $custom=[])
    {
        $this->profession = factory(Profession::class)->create();

        return array_merge([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' => 'secret',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de laravel',
            'twitter' => 'https://twitter.com/fvasquezl'
        ], $custom);
    }

}
