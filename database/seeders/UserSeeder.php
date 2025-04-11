<?php

namespace Database\Seeders;

use App\Models\User;
use App\Stats\UserStats;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        activity()->disableLogging();
        $role1 = Role::create(['name' => 'admin']);
        $user = User::factory()->create([
            'first_name' => 'Maher',
            'last_name' => 'Salama',
            'email' => 'test@example.com',
            'password' => "12121212"
        ]);
        $user->assignRole($role1);
        UserStats::increase(1, $user->created_at);
        sleep(1);
        $users = User::factory(100)->create();
        foreach ($users as $user) {
            $user->assignRole($role1);
            UserStats::increase(1, $user->created_at);
        }
    }
}
