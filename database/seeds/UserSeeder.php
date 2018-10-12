<?php

use App\Team;
use App\User;
use App\Models\Skill;
use App\Models\Profession;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    protected $professions;

    protected $skills;

    protected $teams;


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->fetchRelations();

        $this->createAdmin();

        foreach (range(1,999) as $i){
            $this->createRandomUser();
        }
    }

    /**
     * @return void
     */
    protected function fetchRelations()
    {
        $this->professions = Profession::all();

        $this->skills = Skill::all();

        $this->teams = Team::all();

    }


    protected function createAdmin()
    {
        $admin = factory(User::class)->create([
            'team_id' => $this->teams->firstWhere('name', 'MiEmpresa'),
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
            'created_at' => now()->addDay(),
            'active' => true,
        ]);

        $admin->skills()->attach($this->skills);

        $admin->profile()->create([
            'bio' => 'Programador',
            'profession_id' => $this->professions->firstWhere('title', 'Desarrollador back-end')->id,
        ]);
    }

    protected function createRandomUser(): void
    {
        $user = factory(User::class)->create([
            'team_id' => rand(0, 2) ? null : $this->teams->random()->id,
            'active' => rand(0,3) ? true : false,
        ]);

        $user->skills()->attach($this->skills->random(rand(0, 7)));

        factory(\App\Models\UserProfile::class)->create([
            'user_id' => $user->id,
            'profession_id' => rand(0, 2) ? $this->professions->random()->id : null,
        ]);
    }
}
