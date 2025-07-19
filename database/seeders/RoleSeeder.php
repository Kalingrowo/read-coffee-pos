<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            'owner',
            'cashier'
        ];

        foreach ($datas as $value) {
            Role::firstOrCreate([
                'name' => $value
            ]);
        }
    }
}
