<?php

namespace Tests\Feature\Admin;

use App\Models\Skill;
use App\Models\UserProfile;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_send_a_user_to_the_trash()
    {
        $this->withExceptionHandling();

        $skill1 = factory(Skill::class)->create();
        $skill2 = factory(Skill::class)->create();

        $user = factory(User::class)->create();

        $user->skills()->attach([$skill1->id,$skill2->id]);

        $this->patch("usuarios/{$user->id}/papelera")
            ->assertRedirect(route('users.index'));

        //option1
        $this->assertSoftDeleted('users',[
            'id' => $user->id
        ]);
        $this->assertSoftDeleted('user_profiles',[
            'user_id' => $user->id
        ]);


//        $this->assertSoftDeleted('user_skill',[
//            'user_id' => $user->id
//        ]);

        //option2
        $user->refresh();
        $this->assertTrue($user->trashed());
    }

    /** @test **/
    public function it_completely_deletes_a_user()
    {
        $skill1 = factory(Skill::class)->create();
        $skill2 = factory(Skill::class)->create();

        $user = factory(User::class)->create([
            'deleted_at' => now()
        ]);

        $user->skills()->attach([$skill1->id,$skill2->id]);

        $this->delete(route('users.destroy',$user))
            ->assertRedirect(route('users.trashed'));

        $this->assertDatabaseEmpty('users');

    }

    /** @test **/
    public function it_cannot_delete_a_user_that_is_not_in_the_trash()
    {
        $this->withExceptionHandling();

        $skill1 = factory(Skill::class)->create();
        $skill2 = factory(Skill::class)->create();

        $user = factory(User::class)->create([
            'deleted_at' => null
        ]);

        $user->skills()->attach([$skill1->id,$skill2->id]);

        $this->delete(route('users.destroy',$user))
            ->assertStatus(404);

        $this->assertDatabaseHas('users',[
            'id' => $user->id,
            'deleted_at' => null
        ]);
    }

    /** @test **/
    public function it_undelete_a_user_from_trash()
    {
        $user = factory(User::class)->create([
            'deleted_at' => now()
        ]);

        $this->patch("usuarios/{$user->id}/restaurar")
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users',[
            'id' => $user->id,
        ]);

        $this->assertDatabaseHas('user_profiles',[
            'user_id' => $user->id
        ]);
    }
}
