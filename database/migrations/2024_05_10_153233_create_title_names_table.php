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
        Schema::create('title_names', function (Blueprint $table) {
            $table->id();
            $table->string('project_title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'in_progress','incomplete'])->default('in_progress'); 
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_names');
    }
};
