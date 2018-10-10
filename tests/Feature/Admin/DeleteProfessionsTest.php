<?php

namespace Tests\Feature\Admin;

use App\Models\Profession;
use App\Models\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteProfessionsTest extends TestCase
{
    use RefreshDatabase;

   /** @test **/
   public function it_deletes_a_professions()
   {
        $profession = factory(Profession::class)->create();
        $response = $this->delete(route('professions.destroy',$profession));
        $response->assertRedirect();
        $this->assertDatabaseEmpty('professions');

   }

   /** @test **/
   public function a_profession_associate_to_a_profile_cannot_be_delete()
   {
       $this->withExceptionHandling();

       $profession = factory(Profession::class)->create();
       $profile = factory(UserProfile::class)->create([
           'profession_id' => $profession->id
       ]);

        $response = $this->delete(route('professions.destroy',$profession));
        $response->assertStatus(400);
        $this->assertDatabaseHas('professions',[
            'id' => $profession->id
        ]);

   }
   
}
