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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('position');
            $table->text('job_description');
            $table->text('job_url')->nullable();
            $table->enum('status', ['saved', 'applied', 'interview', 'offer', 'rejected'])->default('saved');
            $table->integer('match_score')->nullable();
            $table->json('extracted_skills')->nullable();
            $table->json('watson_analysis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
