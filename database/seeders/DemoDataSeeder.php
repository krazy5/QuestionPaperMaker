<?php

namespace Database\Seeders;

use App\Models\AcademicClassModel;
use App\Models\Board;
use App\Models\PaperBlueprint;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding demo users...');
        $this->seedUsers();

        $this->command->info('Seeding blueprints from JSON files...');
        $this->seedBlueprintsFromJson();

        $this->command->info('🎉 Demo data seeding completed successfully!');
    }

    private function seedUsers(): void
    {
        User::firstOrCreate(
            ['email' => 'mohsin.mohsin6@gmail.com'],
            ['name' => 'Mohsin Khan', 'password' => Hash::make('mmkmnkak'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['email' => 'aktr000@gmail.com'],
            ['name' => 'Azim Khan', 'institute_name' => 'Mak Tutorials', 'password' => Hash::make('mmkmnkak'), 'role' => 'institute']
        );

        $this->command->info('Admin and Institute users created.');
        $this->command->info('Admin: mohsin.mohsin6@gmail.com | Pass: mmkmnkak');
        $this->command->info('Institute: aktr000@gmail.com | Pass: mmkmnkak');
    }

    private function seedBlueprintsFromJson(): void
    {
        // FIX 1: Pointing to the new, correct directory in `storage`.
        $path = storage_path('data/blueprints/*.json');
        
        // Create the directory if it doesn't exist to prevent errors.
        if (!File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        $files = File::glob($path);
        
        if (empty($files)) {
            $this->command->warn('⚠️ No blueprint JSON files found in storage/data/blueprints/. Skipping blueprint seeding.');
            return;
        }

        $institute = User::where('role', 'institute')->first();

        foreach ($files as $file) {
            $data = json_decode(File::get($file), true);
            $this->command->info("Processing blueprint file: " . basename($file));

            $board = Board::where('name', $data['board_name'])->first();
            // FIX 2: Using the 'class_name' from the JSON file dynamically.
            $class = AcademicClassModel::where('name', $data['class_name'])->first();

            if (!$board || !$class) {
                $this->command->error("❌ Board '{$data['board_name']}' or Class '{$data['class_name']}' not found. Skipping file.");
                continue;
            }

            foreach ($data['applies_to_subjects'] as $subjectName) {
                $subject = Subject::where('name', $subjectName)->where('class_id', $class->id)->first();
                if (!$subject) {
                    $this->command->warn("⚠️ Subject '{$subjectName}' not found for Class '{$class->name}'. Skipping.");
                    continue;
                }

                foreach ($data['patterns'] as $pattern) {
                    $blueprintName = str_replace(
                        ['{subject_name}', '{total_marks}'],
                        [$subject->name, $pattern['total_marks']],
                        $pattern['name_template']
                    );

                    $this->seedBlueprintWithPattern(
                        subject: $subject,
                        boardId: $board->id,
                        classId: $class->id,
                        instituteId: optional($institute)->id,
                        totalMarks: $pattern['total_marks'],
                        sectionsSpec: $pattern['sections'],
                        name: $blueprintName
                    );
                }
                 $this->command->info("✅ Blueprints created/updated for: {$subject->name}");
            }
        }
    }

    /**
     * Create/update a blueprint and (re)create its sections & rules atomically.
     */
    private function seedBlueprintWithPattern(Subject $subject, int $boardId, int $classId, ?int $instituteId, int $totalMarks, array $sectionsSpec, string $name): void
    {
        DB::transaction(function () use ($subject, $boardId, $classId, $instituteId, $totalMarks, $sectionsSpec, $name) {
            $blueprint = PaperBlueprint::updateOrCreate(
                [
                    'name'       => $name,
                    'board_id'   => $boardId,
                    'class_id'   => $classId,
                    'subject_id' => $subject->id,
                ],
                [
                    'total_marks'       => $totalMarks,
                    'institute_id'      => $instituteId,
                    'selected_chapters' => null,
                ]
            );

            $blueprint->sections()->delete();

            $sort = 1;
            foreach ($sectionsSpec as $section) {
                $sec = $blueprint->sections()->create([
                    'name'         => $section['name'],
                    'instructions' => $section['instructions'] ?? null,
                    'sort_order'   => $sort++,
                ]);

                foreach ($section['rules'] as $rule) {
                    $sec->rules()->create($rule);
                }
            }

            $calc = $this->sumMarks($sectionsSpec);
            if ($calc !== $totalMarks) {
                $this->command->warn("⚠️ Blueprint '{$name}': calculated {$calc} ≠ total {$totalMarks}.");
            }
        });
    }

    /**
     * Calculates the total marks for a given pattern.
     */
    private function sumMarks(array $sectionsSpec): int
    {
        $sum = 0;
        foreach ($sectionsSpec as $section) {
            foreach ($section['rules'] as $r) {
                $sum += ((int)$r['marks_per_question']) * ((int)$r['number_of_questions_to_select']);
            }
        }
        return $sum;
    }
}