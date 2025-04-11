<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Stats\CustomerStats;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role2 = Role::create(['guard_name' => 'customers', 'name' => 'customer']);
        $customer = Customer::factory()->create([
            'first_name' => 'Customer',
            'last_name' => 'ONE',
            'mobile' => '956031740',
        ]);   
        $customer->assignRole($role2);  
        CustomerStats::increase(1, $customer->created_at);

        $customers = Customer::factory(100)->create();
        foreach($customers as $customer){
            $customer->assignRole($role2);         
            CustomerStats::increase(1, $customer->created_at);
        }
    }
}
