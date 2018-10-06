<?php

namespace Tests\Feature\Admin;

use App\Models\Profession;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListProfessionsTest extends TestCase
{
    use RefreshDatabase;
  /** @test **/
  public function it_shows_the_users_list()
  {
          factory(Profession::class)->create(['title'=> 'Disenador']);
          factory(Profession::class)->create(['title'=> 'Programador']);
          factory(Profession::class)->create(['title'=> 'Administrador']);

          $this->get('/profesiones')
              ->assertStatus(200)
              ->assertSeeInOrder([
                  'Administrador',
                  'Disenador',
                  'Programador'
              ]);
  }

}
