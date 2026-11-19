<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Empty store with only the owner account — use this when setting up a real client:
 *   php artisan migrate:fresh --seeder=CleanStoreSeeder
 * Change the password right after the first login (Admin → Staff).
 */
class CleanStoreSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@store.test')],
            ['name' => 'Store Owner', 'password' => env('ADMIN_PASSWORD', 'admin12345'), 'role' => 'admin'],
        );
    }
}
