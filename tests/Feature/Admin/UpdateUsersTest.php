<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{
    use RefreshDatabase;
    protected $defaultData = [
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
        'password' => 'secret',
        'bio' => 'Programador de laravel',
        'profession_id' => '',
        'twitter' => 'https://twitter.com/fvasquezl',
        'role' => 'user'
    ];

    /** @test */
    function it_loads_the_edit_user_page()
    {

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
        $this->handleValidationExceptions();

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
    public function the_email_is_required()
    {
        $this->handleValidationExceptions();
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
    public function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

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
    public function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

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
    public function the_password_is_optional()
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
    public function the_users_email_can_stay_the_same()
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
    public function the_password_must_have_at_least_6_characters()
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
            'email' => 'fvasquez@local.com'
        ]);
    }
}
