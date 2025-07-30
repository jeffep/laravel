<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run(): void
{
    $json = File::get(database_path('seeders/data/users.json'));
    $users = json_decode($json, true);


    foreach ($users as $user) {
        // Skip id to let DB auto-increment
        unset($user['id']);
         unset($user['is_touch_panel']);

        // Hash password again if not already hashed
        if (!Str::startsWith($user['password'], '$2y$')) {
            $user['password'] = Hash::make($user['password']);
        }

        \App\Models\User::create($user);
    }
}

}
