<?php

namespace Tests\Browser\Admin;

use App\Models\Profession;
use App\Models\Skill;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateUserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     * @test
     * @return void
     * @throws \Throwable
     */
    public function a_user_can_be_created()
    {
        $profession = factory(Profession::class)->create();
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->browse(function (Browser $browser) use ($profession, $skillA, $skillB) {
            $browser->visitRoute('users.create')
                ->type('name', 'Faustino Vasquez')
                ->type('email', 'fvasquez@local.com')
                ->type('password', 'secret')
                ->type('bio', 'Programador backend')
                ->select('profession_id', $profession->id)
                ->type('twitter', 'https://twitter.com/fvasquezl')
                ->check("skills[{$skillA->id}]")
                ->check("skills[{$skillB->id}]")
                ->radio('role', 'user')
                ->radio('state','active')
                ->press('Crear usuario')
                ->assertRouteIs('users.index')
                ->assertSee('Faustino Vasquez')
                ->assertSee('fvasquez@local.com');
        });

        $this->assertCredentials([
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' =>'secret',
            'role' => 'user',
            'active' => true
        ]);

        $user = User::findByEmail('fvasquez@local.com');

        $this->assertDatabaseHas('user_profiles',[
            'bio' => 'Programador backend',
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
    }
}
