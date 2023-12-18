<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("domain");
            $table->string("city")->nullable();
            $table->string("industry")->nullable();
            $table->string("address")->nullable();
            $table->string("phone")->nullable();
            $table->string("state")->nullable();
            $table->string("lifecyclestage")->nullable();
            $table->string("company_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
