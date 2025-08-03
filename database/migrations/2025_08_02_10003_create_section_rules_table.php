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
        Schema::create('section_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blueprint_section_id')->constrained('blueprint_sections')->onDelete('cascade');
            $table->enum('question_type', ['mcq', 'short', 'long', 'numerical', 'fill_blank', 'true_false', 'match']);
            $table->integer('marks_per_question');
            $table->integer('number_of_questions_to_select'); // e.g., 8 for "Attempt any 8 out of 12"
            $table->integer('total_questions_to_display')->nullable(); // e.g., 12 for "Attempt any 8 out of 12"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_rules');
    }
};
