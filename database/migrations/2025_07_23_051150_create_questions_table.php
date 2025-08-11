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
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->enum('source', ['admin', 'institute'])->default('institute');
                $table->foreignId('institute_id')->nullable()->constrained('users')->onDelete('cascade');

                $table->foreignId('board_id')->constrained()->onDelete('cascade');
                // CORRECT
                $table->foreignId('class_id')->constrained('academic_class_models')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained()->onDelete('cascade');
                $table->foreignId('chapter_id')->constrained()->onDelete('cascade');

                $table->text('question_text');
                $table->string('question_image_path')->nullable();
                $table->enum('question_type', ['mcq', 'short', 'long', 'numerical', 'fill_blank', 'true_false', 'match']);
                $table->json('options')->nullable();
                $table->string('correct_answer')->nullable();
                $table->text('answer_text')->nullable();
                $table->longText('solution_text')->nullable();
                $table->string('answer_image_path')->nullable();

                $table->integer('marks')->default(1);
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
                $table->boolean('approved')->default(false);

                $table->timestamps();
            });
        }


        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('questions');
        }
    };
