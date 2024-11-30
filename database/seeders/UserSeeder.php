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
        User::newUser("dev", "Dev", "User", User::TYPE_ADMIN, "dev@genuine");
        
        User::newUser("lydia.nasr", "Lydia", "Nasr", User::TYPE_ADMIN, "lydia.nasr@123");

        User::newUser("mariam.maher", "Mariam", "Maher", User::TYPE_SALES, "lydia.nasr@123");
        User::newUser("maria.malak", "Maria", "Malak", User::TYPE_SALES, "maria.malak@123");
        User::newUser("safaa.george", "Safaa", "George", User::TYPE_SALES, "safaa.george@123");
        User::newUser("nada.salah", "Nada", "Salah", User::TYPE_SALES, "nada.salah@123");
        User::newUser("amira.sayed", "Amira", "Sayed", User::TYPE_SALES, "amira.sayed@123");

        User::newUser("emad.maher", "Emad", "Maher", User::TYPE_DRIVER, "emad.maher@123");
        User::newUser("salah.aly", "Salah", "Aly", User::TYPE_DRIVER, "salah.aly@123");
        User::newUser("ashraf.mansor", "Ashraf", "Mansor", User::TYPE_DRIVER, "ashraf.mansor@123");
        User::newUser("ehab.mamdoh", "Ehab", "Mamdoh", User::TYPE_DRIVER, "ehab.mamdoh@123");
        User::newUser("ramez", "Ramez", "M", User::TYPE_DRIVER, "ramez@123");
        User::newUser("ayman", "Ayman", "M", User::TYPE_DRIVER, "ayman@123");

        




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
