<?php

namespace Database\Seeders;

use App\Models\Customers\Followup;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class FollowupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (App::isProduction()) return;

        $user = User::first(); // Assuming there's at least one user in the users table

        Followup::create([
            'creator_id' => $user->id,
            'called_type' => 'App\Models\Customers\Customer',  // Example polymorphic type
            'called_id' => 1,  // Example polymorphic ID
            'title' => 'Initial follow-up for customer inquiry',
            'status' => Followup::STATUS_NEW,
            'call_time' => Carbon::now()->addDays(2),  // Set a future call time
            'action_time' => Carbon::now()->addDays(3),  // Set a future action time
            'desc' => 'Follow-up call for customer to check on product interest',
            'caller_note' => 'Customer seemed interested in the product but wants more details.',
        ]);

        Followup::create([
            'creator_id' => $user->id,
            'called_type' => 'App\Models\Customers\Customer',
            'called_id' => 2,
            'title' => 'Product issue follow-up',
            'status' => Followup::STATUS_CALLED,
            'call_time' => Carbon::now()->addDay(),
            'action_time' => Carbon::now()->addDays(5),
            'desc' => 'Customer reported an issue with the product. Need to follow up.',
            'caller_note' => 'Ensure the customer received the replacement product.',
        ]);
    }
}
