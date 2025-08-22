<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paper_question', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paper_id')
                ->constrained('papers')
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();

            // Nullable; if the rule is deleted, keep the row but null the reference
            $table->foreignId('section_rule_id')
                ->nullable()
                ->constrained('section_rules')
                ->nullOnDelete();

            $table->unsignedInteger('marks')->default(1);
            $table->unsignedInteger('sort_order')->default(1);

            // Prevent duplicate question on the same paper
            $table->unique(['paper_id', 'question_id']);

            // Helpful indexes
            $table->index(['paper_id', 'section_rule_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_question');
    }
};
