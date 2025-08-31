<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\PaperBlueprint;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding demo users and HSC Science blueprints...');

        // 1) Users
        $admin = User::firstOrCreate(
            ['email' => 'mohsin.mohsin6@gmail.com'],
            ['name' => 'Mohsin Khan', 'password' => Hash::make('mmkmnkak'), 'role' => 'admin']
        );

        $institute = User::firstOrCreate(
            ['email' => 'aktr000@gmail.com'],
            ['name' => 'Azim Khan', 'institute_name' => 'Mak Tutorials', 'password' => Hash::make('mmkmnkak'), 'role' => 'institute']
        );

        $this->command->info('Admin and Institute users created.');
        $this->command->info('Admin: mohsin.mohsin6@gmail.com | Pass: mmkmnkak');
        $this->command->info('Institute: aktr000@gmail.com | Pass: mmkmnkak');

        // 2) Context (Board/Class/Subjects)
        $board = Board::where('name', 'Maharashtra State Board')->first();
        $class = AcademicClassModel::where('name', 'Class12sci')->first();

        if (!$board || !$class) {
            $this->command->error('❌ Maharashtra Board or Class12sci not found. Run MaharashtraBoardSeeder first.');
            return;
        }

        $physics   = Subject::where('name', 'Physics')->where('class_id', $class->id)->first();
        $chemistry = Subject::where('name', 'Chemistry')->where('class_id', $class->id)->first();
        $biology   = Subject::where('name', 'Biology')->where('class_id', $class->id)->first();
        $maths     = Subject::where('name', 'Maths')->where('class_id', $class->id)->first();

        // 3) PCB (Physics, Chemistry, Biology) patterns for 10,15,20,25,30,70 marks
        $pcbMarks = [10, 15, 20, 25, 30, 70];
        foreach ([$physics, $chemistry, $biology] as $subject) {
            if (!$subject) continue;
            foreach ($pcbMarks as $tm) {
                $this->seedBlueprintWithPattern(
                    subject: $subject,
                    boardId: $board->id,
                    classId: $class->id,
                    instituteId: optional($institute)->id,
                    totalMarks: $tm,
                    sectionsSpec: $this->pcbPattern($tm),
                    name: "HSC {$subject->name} Pattern - {$tm}"
                );
            }
            $this->command->info("✅ {$subject->name}: Blueprints for 10,15,20,25,30,70 created/updated.");
        }

        // 4) Maths patterns for 20,25,30,80 marks
        $mathsMarks = [20, 25, 30, 80];
        if ($maths) {
            foreach ($mathsMarks as $tm) {
                $this->seedBlueprintWithPattern(
                    subject: $maths,
                    boardId: $board->id,
                    classId: $class->id,
                    instituteId: optional($institute)->id,
                    totalMarks: $tm,
                    sectionsSpec: $this->mathsPattern($tm),
                    name: "HSC Maths Pattern - {$tm}"
                );
            }
            $this->command->info("✅ Maths: Blueprints for 20, 25, 30, 80 marks created/updated.");
        }

        $this->command->info('🎉 Demo data seeding completed successfully!');
    }

    /**
     * Create/update a blueprint and (re)create its sections & rules atomically.
     */
    private function seedBlueprintWithPattern(
        Subject $subject,
        int $boardId,
        int $classId,
        ?int $instituteId,
        int $totalMarks,
        array $sectionsSpec,
        string $name
    ): void {
        DB::transaction(function () use ($subject, $boardId, $classId, $instituteId, $totalMarks, $sectionsSpec, $name) {
            // Upsert blueprint
            $blueprint = PaperBlueprint::updateOrCreate(
                [
                    'name'       => $name,
                    'board_id'   => $boardId,
                    'class_id'   => $classId,
                    'subject_id' => $subject->id,
                ],
                [
                    'total_marks'     => $totalMarks,
                    'institute_id'    => $instituteId,
                    'selected_chapters' => null,
                ]
            );

            // Reset sections & rules to keep seeder idempotent
            $blueprint->sections()->delete();

            $sort = 1;
            foreach ($sectionsSpec as $section) {
                $sec = $blueprint->sections()->create([
                    'name'         => $section['name'],
                    'instructions' => $section['instructions'] ?? null,
                    'sort_order'   => $sort++,
                ]);

                foreach ($section['rules'] as $rule) {
                    $sec->rules()->create([
                        'question_type'                 => $rule['question_type'], // enum: mcq, short, long, numerical, fill_blank, true_false, match
                        'marks_per_question'            => (int)$rule['marks_per_question'],
                        'number_of_questions_to_select' => (int)$rule['number_of_questions_to_select'],
                        'total_questions_to_display'    => $rule['total_questions_to_display'] ?? null,
                    ]);
                }
            }

            // Optional sanity check: sum section marks = total_marks
            $calc = $this->sumMarks($sectionsSpec);
            if ($calc !== $totalMarks) {
                $this->command->warn("⚠️ Blueprint '{$name}': calculated {$calc} ≠ total {$totalMarks}. (Check pattern totals.)");
            }
        });
    }

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

    /**
     * PCB pattern by total marks (Class 12 – Maharashtra Board style).
     * Uses only allowed enums: mcq, short, long, numerical, fill_blank, true_false, match.
     */
    private function pcbPattern(int $total): array
    {
        switch ($total) {
            case 10:
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq',   'marks_per_question' => 1, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 5],
                            ['question_type' => 'short', 'marks_per_question' => 1, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 5],
                        ],
                    ],
                ];
            case 15:
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 5],
                        ],
                    ],
                    [
                        'name' => 'Section B (VSA/SA)',
                        'instructions' => 'Answer any 5.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 7],
                        ],
                    ],
                ];
            case 20:
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10],
                        ],
                    ],
                    [
                        'name' => 'Section B (VSA/SA)',
                        'instructions' => 'Answer any 5.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 8],
                        ],
                    ],
                ];
            case 25:
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10],
                        ],
                    ],
                    [
                        'name' => 'Section B (Short Answer)',
                        'instructions' => 'Answer any 5.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 8],
                        ],
                    ],
                    [
                        'name' => 'Section C (Long/Numerical)',
                        'instructions' => 'Answer any 1.',
                        'rules' => [
                            ['question_type' => 'long', 'marks_per_question' => 5, 'number_of_questions_to_select' => 1, 'total_questions_to_display' => 3],
                        ],
                    ],
                ];
            case 30:
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10],
                        ],
                    ],
                    [
                        'name' => 'Section B (Short Answer)',
                        'instructions' => 'Answer any 5.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 8],
                        ],
                    ],
                    [
                        'name' => 'Section C (Long/Numerical)',
                        'instructions' => 'Answer any 2.',
                        'rules' => [
                            ['question_type' => 'long', 'marks_per_question' => 5, 'number_of_questions_to_select' => 2, 'total_questions_to_display' => 4],
                        ],
                    ],
                ];
            case 70:
                // Full HSC style: objective + short + long + numericals
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq',   'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10], // 10
                            ['question_type' => 'short', 'marks_per_question' => 1, 'number_of_questions_to_select' => 8,  'total_questions_to_display' => 10], // 8
                        ],
                    ],
                    [
                        'name' => 'Section B (Short Answer)',
                        'instructions' => 'Attempt any 10 out of 12.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 12], // 20
                        ],
                    ],
                    [
                        'name' => 'Section C (Long Answer)',
                        'instructions' => 'Attempt any 6 out of 8.',
                        'rules' => [
                            ['question_type' => 'long', 'marks_per_question' => 4, 'number_of_questions_to_select' => 6, 'total_questions_to_display' => 8], // 24
                        ],
                    ],
                    [
                        'name' => 'Section D (Numericals)',
                        'instructions' => 'Attempt any 2 out of 4.',
                        'rules' => [
                            ['question_type' => 'numerical', 'marks_per_question' => 4, 'number_of_questions_to_select' => 2, 'total_questions_to_display' => 4], // 8
                        ],
                    ],
                ];
            default:
                return [];
        }
    }

    /**
     * Maths pattern by total marks.
     */
    private function mathsPattern(int $total): array
    {
        switch ($total) {
            case 20: // 10 (MCQ) + 10 (Short)
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10],
                        ],
                    ],
                    [
                        'name' => 'Section B (Short Answer)',
                        'instructions' => 'Attempt any 5 out of 7.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 7],
                        ],
                    ],
                ];
            case 25: // 10 (MCQ) + 10 (Short) + 5 (Long)
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10],
                        ],
                    ],
                    [
                        'name' => 'Section B (Short Answer)',
                        'instructions' => 'Attempt any 5 out of 7.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 7],
                        ],
                    ],
                    [
                        'name' => 'Section C (Long Answer)',
                        'instructions' => 'Attempt any 1 out of 2.',
                        'rules' => [
                            ['question_type' => 'long', 'marks_per_question' => 5, 'number_of_questions_to_select' => 1, 'total_questions_to_display' => 2],
                        ],
                    ],
                ];
            case 30: // 10 (MCQ) + 10 (Short) + 10 (Long)
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq', 'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10],
                        ],
                    ],
                    [
                        'name' => 'Section B (Short Answer)',
                        'instructions' => 'Attempt any 5 out of 8.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 5, 'total_questions_to_display' => 8],
                        ],
                    ],
                    [
                        'name' => 'Section C (Long Answer)',
                        'instructions' => 'Attempt any 2 out of 3.',
                        'rules' => [
                            ['question_type' => 'long', 'marks_per_question' => 5, 'number_of_questions_to_select' => 2, 'total_questions_to_display' => 3],
                        ],
                    ],
                ];
            case 80:
                return [
                    [
                        'name' => 'Section A (Objective)',
                        'instructions' => 'All questions compulsory.',
                        'rules' => [
                            ['question_type' => 'mcq',        'marks_per_question' => 1, 'number_of_questions_to_select' => 10, 'total_questions_to_display' => 10], // 10
                            ['question_type' => 'fill_blank', 'marks_per_question' => 1, 'number_of_questions_to_select' => 6,  'total_questions_to_display' => 8],  // +6 = 16
                        ],
                    ],
                    [
                        'name' => 'Section B (Short Answer)',
                        'instructions' => 'Attempt any 7 out of 9.',
                        'rules' => [
                            ['question_type' => 'short', 'marks_per_question' => 2, 'number_of_questions_to_select' => 7, 'total_questions_to_display' => 9], // +14 = 30
                        ],
                    ],
                    [
                        'name' => 'Section C (Long Answer I)',
                        'instructions' => 'Attempt any 6 out of 8.',
                        'rules' => [
                            ['question_type' => 'long', 'marks_per_question' => 3, 'number_of_questions_to_select' => 6, 'total_questions_to_display' => 8], // +18 = 48
                        ],
                    ],
                    [
                        'name' => 'Section D (Long Answer II)',
                        'instructions' => 'Attempt any 4 out of 6.',
                        'rules' => [
                            ['question_type' => 'long', 'marks_per_question' => 4, 'number_of_questions_to_select' => 4, 'total_questions_to_display' => 6], // +16 = 64
                        ],
                    ],
                    [
                        'name' => 'Section E (Applied/Numericals)',
                        'instructions' => 'Attempt any 2 out of 4.',
                        'rules' => [
                            ['question_type' => 'numerical', 'marks_per_question' => 8, 'number_of_questions_to_select' => 2, 'total_questions_to_display' => 4], // +16 = 80
                        ],
                    ],
                ];
            default:
                return [];
        }
    }
}