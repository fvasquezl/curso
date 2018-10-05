<?php

namespace Tests\Feature\Admin;

use App\Models\Profession;
use App\Models\Skill;
use App\Models\UserProfile;
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
        'profession_id' => '',
        'bio' => 'Programador de laravel',
        'twitter' => 'https://twitter.com/fvasquezl',
        'role' => 'user'
    ];

    /** @test */
    function it_loads_the_edit_user_page()
    {

        $user = factory(User::class)->create();

        $this->get(route('users.edit', $user))
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertViewHas('user', function ($viewUser) use ($user) {
                return $viewUser->id === $user->id;
            });
    }

    /** @test * */
    public function it_updated_a_user()
    {

        $user = factory(User::class)->create();
        $oldProfession = factory(Profession::class)->create();
        $user->profile()->save(factory(UserProfile::class)->make([
            'profession_id' => $oldProfession->id
        ]));

        $oldSkill1 = factory(Skill::class)->create();
        $oldSkill2 = factory(Skill::class)->create();
        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);

        $newProfession = factory(Profession::class)->create();
        $newSkill1 = factory(Skill::class)->create();
        $newSkill2 = factory(Skill::class)->create();

        $this->put(route('users.update', $user), [
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' => 'secret',
            'bio' => 'Programador de laravel',
            'twitter' => 'https://twitter.com/fvasquezl',
            'role' => 'admin',
            'profession_id' => $newProfession->id,
            'skills' => [$newSkill1->id, $newSkill2->id]
        ])->assertRedirect(route('users.show', $user));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' => 'secret',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'bio' => 'Programador de laravel',
            'twitter' => 'https://twitter.com/fvasquezl',
            'profession_id' => $newProfession->id,
        ]);

        $this->assertDatabaseCount('user_skill', 2);
        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill1->id
        ]);
        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill2->id
        ]);
    }

    /** @test * */
    public function it_detaches_the_skills_if_none_is_checked()
    {
        $user = factory(User::class)->create();

        $oldSkill1 = factory(Skill::class)->create();
        $oldSkill2 = factory(Skill::class)->create();
        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);


        $this->put(route('users.update', $user), $this->withData())
            ->assertRedirect(route('users.show', $user));

        $this->assertDatabaseEmpty('user_skill');

    }

    /** @test * */
    public function the_name_field_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'name' => '',
            ]))->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', [
            'email' => 'fvasquez@local.com'
        ]);
    }


    /** @test * */
    public function the_email_field_is_required()
    {
        $this->handleValidationExceptions();
        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), [
                'name' => 'Faustino Vasquez',
                'email' => '',
                'password' => 'secret'
            ])->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users', [
            'email' => 'fvasquez@local.com'
        ]);
    }

    /** @test * */
    public function the_email_field_must_be_valid()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'email' => 'the-email-must-be-valid'
            ]))->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users', [
            'email' => 'fvasquez@local.com'
        ]);
    }

    /** @test * */
    public function the_email_field_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $user = factory(User::class)->create([
            'email' => 'other@local.com'
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'email' => 'fvasquez@local.com',
            ]))->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('users', [
            'email' => 'other@local.com'
        ]);
    }

    /** @test * */
    public function the_password_field_is_optional()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('123456')
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'password' => '',
            ]))->assertRedirect(route('users.show', $user));

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' => '123456'
        ]);
    }


    /** @test * */
    public function the_users_email_can_stay_the_same()
    {
        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'email' => 'fvasquez@local.com',
            ]))->assertRedirect(route('users.show', $user));

        $this->assertDatabaseHas('users', [
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
        ]);
    }

    /** @test * */
    public function the_password_field_must_have_at_least_6_characters()
    {

        $user = factory(User::class)->create([
            'email' => 'fvasquez@local.com'
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'password' => '123456'
            ]))->assertRedirect(route('users.show', $user));
        $this->assertDatabaseHas('users', [
            'email' => 'fvasquez@local.com'
        ]);
    }

    /** @test * */
    public function the_role_field_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'role' => '',
            ]))->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', [
            'email' => 'fvasquez@local.com'
        ]);
    }


    /** @test * */
    public function the_bio_field_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'bio' => '',
            ]))->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['bio']);

        $this->assertDatabaseMissing('users', [
            'email' => 'fvasquez@local.com'
        ]);
    }

    /** @test * */
    public function the_twitter_field_is_optional()
    {
        $user = factory(User::class)->create();

        factory(UserProfile::class)->create([
            'twitter' => 'https://twitter.com/fvasquezl',
            'user_id' => $user->id
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'twitter' => 'https://twitter.com/fvasquezl',
            'user_id' => $user->id
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'twitter' => null,
            ]))->assertRedirect(route('users.show', $user));

        $this->assertDatabaseHas('user_profiles', [
            'twitter' => null,
            'user_id' => $user->id
        ]);
    }

    /** @test * */
    public function the_twitter_field_needs_to_be_an_url()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'twitter' => 'https://twitter.com/fvasquezl',
            'user_id' => $user->id
        ]);

        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'twitter' => 'esta-es-una-cadena',
            ]))->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['twitter']);
    }

    /** @test * */
    public function the_twitter_field_need_to_exist()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();
        factory(UserProfile::class)->create([
            'twitter' => 'https://twitter.com/fvasquezl',
            'user_id' => $user->id
        ]);

        $userUpdate = array_filter($this->withData([
            'twitter' => null,
        ]));

        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user),$userUpdate)
            ->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['twitter']);
    }

    /** @test * */
    public function the_profession_id_field_is_optional()
    {
        $user = factory(User::class)->create();
        $profession = factory(Profession::class)->create();

        factory(UserProfile::class)->create([
            'profession_id' => $profession->id,
            'user_id' => $user->id
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'profession_id' => $profession->id,
            'user_id' => $user->id
        ]);
        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user), $this->withData([
                'profession_id' => null,
            ]))->assertRedirect(route('users.show', $user));

        $this->assertDatabaseHas('user_profiles', [
            'profession_id' => null,
            'user_id' => $user->id
        ]);
    }


    /** @test * */
    public function the_profession_id_field_need_to_exist()
    {
        $this->handleValidationExceptions();
        $user = factory(User::class)->create();
        $profession = factory(Profession::class)->create();

        factory(UserProfile::class)->create([
            'profession_id' => $profession->id,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'profession_id' => $profession->id,
            'user_id' => $user->id
        ]);
        $professionUpdate = array_filter($this->withData([
            'profession_id' => null,
        ]));


        $this->from(route('users.edit', $user))
            ->put(route('users.update', $user),$professionUpdate)
            ->assertRedirect(route('users.edit', $user))
            ->assertSessionHasErrors(['profession_id']);
    }

}
