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
            Schema::create('paper_questions', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('paper_id')->constrained()->onDelete('cascade');
                    $table->foreignId('question_id')->constrained()->onDelete('cascade');
                    $table->integer('marks')->default(1);
                    $table->integer('sort_order')->nullable();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_questions');
    }
};
