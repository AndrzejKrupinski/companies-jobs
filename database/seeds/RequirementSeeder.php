<?php

use App\Models\Requirement;
use Illuminate\Database\Seeder;

class RequirementSeeder extends Seeder
{
    public function run(): void
    {
        factory(Requirement::class, 15)->create();
    }
}
