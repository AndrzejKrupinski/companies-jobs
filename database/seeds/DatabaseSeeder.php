<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CompanySeeder::class);
        $this->call(RequirementSeeder::class);
        $this->call(CompanyRequirementsPivotSeeder::class);
    }
}
