<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        User::create([
            'nickname' => 'frezc',
            'email' => '504021398@qq.com',
            'password' => Hash::make('secret')
        ]);

        User::create([
            'nickname' => 'frezc',
            'email' => 'frezcw@gmail.com',
            'password' => Hash::make('secret')
        ]);

        factory(App\Todo::class, 1000)->create();

        Model::reguard();
    }
}
