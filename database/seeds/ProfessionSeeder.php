<?php

use App\Models\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Profession::create(['title' => 'Desarrollador back-end']);
        Profession::create(['title' => 'Desarrollador front-end']);
        Profession::create(['title' => 'Disenador Web']);

    }
}
