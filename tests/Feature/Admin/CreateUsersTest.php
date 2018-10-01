<?php

namespace Tests\Feature\Admin;

use App\Models\Profession;
use App\Models\Skill;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUsersTest extends TestCase
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

    /** @test **/
    public function it_creates_a_new_user()
    {
        $profession = factory(Profession::class)->create();

        $skillA= factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $skillC = factory(Skill::class)->create();


        $this->post(route('users.store'),
            $this->withData([
                'skills' =>[$skillA->id,$skillB->id],
                'profession_id' => $profession->id,
            ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret',
            'role' => 'user'
        ]);

        $user = User::findByEmail('fvasquez@local.com');

        $this->assertDatabaseHas('user_profiles',[
            'bio' => 'Programador de laravel',
            'twitter' => 'https://twitter.com/fvasquezl',
            'user_id' => $user->id,
            'profession_id' => $profession->id,
        ]);

        $this->assertDatabaseHas('user_skill',[
            'user_id' => $user->id,
            'skill_id' => $skillA->id,
        ]);
        $this->assertDatabaseHas('user_skill',[
            'user_id' => $user->id,
            'skill_id' => $skillB->id,
        ]);
        $this->assertDatabaseMissing('user_skill',[
            'user_id' => $user->id,
            'skill_id' => $skillC->id,
        ]);
    }

    /** @test **/
    public function the_twitter_field_is_optional()
    {

        $this->from(route('users.create'))
            ->post(route('users.store'),$this->withData([
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
    public function the_role_field_is_optional()
    {
        $this->from(route('users.create'))
            ->post(route('users.store'),$this->withData([
                'role'=> null
            ]))->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users',[
            'email' => 'fvasquez@local.com',
            'role' =>'user'
        ]);
    }

    /** @test **/
    public function the_role_field_must_be_valid()
    {
        $this->handleValidationExceptions();
        $this->from(route('users.create'))
            ->post(route('users.store'),$this->withData([
                'role'=> 'invalid-role'
            ]))->assertSessionHasErrors('role');

        $this->assertDatabaseEmpty('users');
    }

//    /** @test **/
//    public function only_admin_can_create_admin_users()
//    {
//        $user = factory(User::class)->create(['role' => 'user']);
//        $response = $this->actingAs($user)
//            ->get(route('users.create'));
//        $response->assertViewMissing('input#role_admin');
//    }


    /** @test **/
    public function the_profession_id_field_is_optional()
    {
        $this->post(route('users.store'),$this->withData([
            'profession_id' => ''
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret',
        ]);
        $this->assertDatabaseHas('user_profiles',[
            'bio' => 'Programador de laravel',
            'user_id' => User::findByEmail('fvasquez@local.com')->id,
            'profession_id' => null
        ]);
    }

    /** @test */
    function it_loads_the_create_users_page()
    {
        $profession= factory(Profession::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->get(route('users.create'))
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function($professions) use ($profession){
                return $professions->contains($profession);
            })
            ->assertViewHas('skills',function($skills) use($skillA,$skillB){
                return $skills->contains($skillA) && $skills->contains($skillB);
            });

    }

    /** @test **/
    public function the_user_id_redirected_to_the_previous_page_whe_the_validation_fails()
    {
        $this->handleValidationExceptions();
        $this->from(route('users.create'))
            ->post(route('users.store'),[])
            ->assertRedirect(route('users.create'));

        $this->assertDatabaseEmpty('users');

    }

    /** @test **/
    public function the_name_is_required()
    {
        $this->handleValidationExceptions();
        $this->post(route('users.store'),$this->withData([
                'name' => ''
            ]))
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test **/
    public function the_email_is_required()
    {
        $this->handleValidationExceptions();
        $this->post(route('users.store'),$this->withData([
                'email' => ''
            ]))->assertSessionHasErrors(['email']);
        $this->assertDatabaseEmpty('users');

    }

    /** @test **/
    public function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();
        $this->post(route('users.store'),$this->withData([
                'email' => 'correo-no-valido'
            ]))->assertSessionHasErrors(['email']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test **/
    public function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);

        $this->post(route('users.store'),$this->withData([
                'email' => 'fvasquez@local.com'
            ]))->assertSessionHasErrors(['email']);
        $this->assertEquals(1,User::count());
    }

    /** @test **/
    public function the_password_is_required()
    {
        $this->handleValidationExceptions();
        $this->post(route('users.store'),$this->withData([
                'password'=>''
            ]))->assertSessionHasErrors(['password']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test **/
    public function the_password_must_have_at_least_6_characters()
    {
        $this->handleValidationExceptions();
        $this->post(route('users.store'),$this->withData([
                'password' => '12345'
            ]))->assertSessionHasErrors(['password']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test **/
    public function the_profession_must_be_valid()
    {
        $this->handleValidationExceptions();
        $this->post(route('users.store'),$this->withData([
                'profession_id' => '999'
            ]))->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');

    }


    /** @test **/
    public function only_non_delete_professions_are_valid()
    {
        $this->handleValidationExceptions();
        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' =>now()->format('Y-m-d'),
        ]);
        $this->post(route('users.store'),$this->withData([
                'profession_id' => $deletedProfession->id
            ]))->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');

    }


    /** @test **/
    public function the_skills_must_be_array()
    {
        $this->handleValidationExceptions();
        $this->post(route('users.store'),$this->withData([
                'skills' => 'PHP,JS,TDD'
            ]))->assertSessionHasErrors(['skills']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test **/
    public function the_skills_must_be_valid()
    {
        $this->handleValidationExceptions();
        $skillA= factory(Skill::class)->create();
        $skillB= factory(Skill::class)->create();

        $this->post(route('users.store'),$this->withData([
                'skills' => [$skillA->id,$skillB->id+1]
            ]))->assertSessionHasErrors(['skills']);
        $this->assertDatabaseEmpty('users');
    }
}
