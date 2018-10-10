<?php

use App\User;
use App\Models\Skill;
use App\Models\Profession;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $professions = Profession::all();

        $skills = Skill::all();

        $user = factory(User::class)->create([
           'name'=> 'Faustino Vasquez',
           'email'=> 'fvasquez@local.com',
           'password'=> bcrypt('secret'),
            'role' => 'admin',
            'created_at' => now()->addDay()
        ]);

        $user->profile()->create([
            'bio' => 'Programador',
            'profession_id' => $professions->firstWhere('title','Desarrollador back-end')->id,
        ]);


        factory(User::class,99)->create()->each(function($user) use ($professions,$skills){
            $randomSkills = $skills->random(rand(0,7));

            $user->skills()->attach($randomSkills);

            factory(\App\Models\UserProfile::class)->create([
                'user_id' => $user->id,
                'profession_id' => rand(0,2) ? $professions->random()->id : null,
            ]);
        });
    }
}
