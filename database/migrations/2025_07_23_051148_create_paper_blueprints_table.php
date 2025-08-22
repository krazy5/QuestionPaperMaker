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
        Schema::create('paper_blueprints', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "HSC Science - Physics Pattern"
            $table->foreignId('board_id')->constrained('boards')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('academic_class_models')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade')->after('class_id');
             $table->unsignedInteger('total_marks');
              $table->foreignId('institute_id')->nullable()->constrained('users')->onDelete('cascade');

             // ✅ ADD THIS FOR CHAPTER SELECTION
            $table->json('selected_chapters')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_blueprints');
    }
};
