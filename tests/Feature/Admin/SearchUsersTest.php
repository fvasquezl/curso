<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function search_users_by_name()
    {
        $faustino =factory(User::class)->create([
            'name' => 'Faustino',
        ]);
        $sebastian =factory(User::class)->create([
            'name' => 'Sebastian'
        ]);


        $this->get(route('users.index','search=Faustino'))
            ->assertStatus(200)
            ->assertViewHas('users',function($users) use($faustino,$sebastian){
                return $users->contains($faustino) && !$users->contains($sebastian);
            });

    }

    /** @test */
    public function show_results_with_a_partial_search_by_name()
    {
        $faustino =factory(User::class)->create([
            'name' => 'Faustino',
        ]);
        $sebastian =factory(User::class)->create([
            'name' => 'Sebastian'
        ]);


        $this->get(route('users.index','search=Fau'))
            ->assertStatus(200)
            ->assertViewHas('users',function($users) use($faustino,$sebastian){
                return $users->contains($faustino) && !$users->contains($sebastian);
            });;
    }


    /** @test */
    public function search_users_by_email()
    {
        $faustino =factory(User::class)->create([
            'email' => 'faustino@test.com',
        ]);
        $sebastian =factory(User::class)->create([
            'email' => 'sebastian@test.net'
        ]);


        $this->get(route('users.index','search=faustino@test.com'))
            ->assertStatus(200)
            ->assertViewHas('users',function($users) use($faustino,$sebastian){
                return $users->contains($faustino) && !$users->contains($sebastian);
            });
    }


    /** @test */
    public function show_result_with_a_partial_search_by_email()
    {
        $faustino =factory(User::class)->create([
            'email' => 'faustino@test.com',
        ]);
        $sebastian =factory(User::class)->create([
            'email' => 'sebastian@test.net'
        ]);


        $this->get(route('users.index','search=faustino@test'))
            ->assertStatus(200)
            ->assertViewHas('users',function($users) use($faustino,$sebastian){
                return $users->contains($faustino) && !$users->contains($sebastian);
            });
    }
}
