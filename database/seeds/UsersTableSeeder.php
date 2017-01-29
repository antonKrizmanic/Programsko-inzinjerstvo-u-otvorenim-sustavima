<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*DB::table('users')->insert([
            'name'=>str_rand(10),
            'email'=>str_rand(10).'@gmail.com',
            'password'=>bcrypt('Lozinka123'),
        ]);*/
        factory(App\User::class, 50)->create();

    }
}
