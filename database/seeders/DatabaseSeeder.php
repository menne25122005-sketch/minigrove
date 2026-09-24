<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CustomerSeeder::class,
            PaymentSettingSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            StudioProductSeeder::class,
            VoucherSeeder::class,
            CareRuleSeeder::class,
        ]);
    }
}