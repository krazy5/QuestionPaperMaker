<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            // CORRECT
$table->foreignId('class_id')->constrained('academic_class_models')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('instructions')->nullable();
            $table->string('time_allowed')->default('3 Hrs');
            $table->integer('total_marks')->default(0);
            $table->enum('status', ['draft', 'final'])->default('draft');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('papers');
    }
};
