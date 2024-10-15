<?php

namespace Database\Seeders;

use App\Models\Users\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::newUser("mark", "Mark", "Mourad", User::TYPE_ADMIN, "mark@genuine");
        User::newUser("michael", "Michael", "Rafaillo", User::TYPE_ADMIN, "michael@genuine");
        User::newUser("mina", "Mina", "Nabil", User::TYPE_ADMIN, "mina@genuine");
        // for ($i = 51; $i <= 100; $i++) {
        //     User::newUser(
        //         "user{$i}",                       // Unique username
        //         "FirstName{$i}",                   // First name
        //         "LastName{$i}",                    // Last name
        //         User::TYPE_ADMIN,                  // Assuming all users are admin, change as needed
        //         "user{$i}@genuine.com"             // Unique email
        //     );
        // }
    }
}
