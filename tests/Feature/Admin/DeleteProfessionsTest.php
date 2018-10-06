<?php

namespace Tests\Feature\Admin;

use App\Models\Profession;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteProfessionsTest extends TestCase
{
    use RefreshDatabase;

   /** @test **/
   public function it_deletes_a_professions()
   {
        $professions = factory(Profession::class)->create();
        $response = $this->delete("professions/'{$professions->id}");
        $response->assertRedirect();
        $this->assertDatabaseEmpty('professions');

   }
   
}
