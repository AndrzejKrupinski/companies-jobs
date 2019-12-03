<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTableCompanyRequirements extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('company_requirements');
    }

    public function down(): void
    {
        Schema::create('company_requirements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id');
            $table->bigInteger('requirement_id');
            $table->timestamps();
        });
    }
}
