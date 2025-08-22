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
        Schema::create('blueprint_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_blueprint_id')->constrained('paper_blueprints')->onDelete('cascade');
            $table->string('name'); // e.g., "Section A"
            $table->text('instructions')->nullable(); // e.g., "Answer all questions."
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blueprint_sections');
    }
};
