<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableConditionRequirements extends Migration
{
    public function up(): void
    {
        Schema::create('condition_requirements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('condition_id')->index();
            $table->bigInteger('requirement_id')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condition_requirements');
    }
}
