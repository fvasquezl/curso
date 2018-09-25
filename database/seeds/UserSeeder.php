<?php

use App\Models\Profession;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $professionId = Profession::where('title','Desarrollador back-end')->value('id');

        factory(User::class)->create([
           'name'=> 'Faustino Vasquez',
           'email'=> ' fvasquez@local.com',
           'password'=> bcrypt('secret'),
           'profession_id' => $professionId,
        ]);

        factory(User::class)->create([
            'profession_id'=> $professionId
        ]);

        factory(User::class,48)->create();
    }
}
