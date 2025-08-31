<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preview: {{ $paper->title }}</title>

    <script>
    window.MathJax = { tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] } };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            body { background-color: white !important; }
            .no-print { display: none !important; }
            .printable-container { margin: 0 !important; padding: 0 !important; box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-serif leading-normal">
    <div class="text-center py-4 no-print">
        <button onclick="window.print()" class="px-6 py-2 bg-gray-700 text-white rounded-md shadow-md hover:bg-gray-800">
            Print or Save as PDF
        </button>
         {{-- Show "Back to Select Questions" ONLY if the paper was made WITHOUT a blueprint --}}
            @unless($blueprint)
                <a href="{{ route('institute.papers.questions.select', $paper) }}"
                class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 no-print">
                    Back to Select Questions
                </a>
            @endunless
    </div>

    <div class="max-w-4xl min-h-[11in] mx-auto my-8 p-12 bg-white shadow-lg border printable-container">

        {{-- Header --}}
        <div class="mb-4 border-b-2 border-black pb-4">
            <div class="flex justify-between items-start">
                <div class="w-1/5"></div>
                <div class="w-3/5 text-center">
                    <h1 class="text-lg font-bold">{{ $paper->institute->department ?? 'DEPARTMENT OF EXAMINATIONS' }}</h1>
                    <h2 class="text-xl font-bold my-1">{{ $paper->institute->institute_name ?? 'Name of University' }}</h2>
                    <h3 class="text-md font-semibold">{{ $paper->title }}</h3>
                </div>
                <div class="w-1/5 text-right"></div>
            </div>
            <div class="flex justify-between items-end text-sm font-bold mt-4">
                <span>Time: {{ $paper->time_allowed }}</span>
                <span class="text-center flex-grow">Subject: {{ $paper->subject->name ?? 'N/A' }} ({{ $paper->academicClass->name ?? 'N/A' }})</span>
                <div class="text-right">
                    @if($paper->exam_date)
                        <div>Date: {{ \Carbon\Carbon::parse($paper->exam_date)->format('d/m/Y') }}</div>
                    @endif
                    <div>Maximum Marks: {{ $paper->total_marks }}</div>
                </div>
            </div>
        </div>

        @if($paper->instructions)
            <div class="mb-8 italic">
                <strong class="not-italic">Instructions:</strong>
                <p class="m-0">{!! nl2br(e($paper->instructions)) !!}</p>
            </div>
        @endif

        @php $qNo = 1; @endphp

        {{-- CASE 1: Paper was generated from a Blueprint --}}
        @if ($blueprint && $paper->questions->isNotEmpty())
            @foreach ($blueprint->sections as $section)
                <div class="mb-8 @if(!$loop->first) pt-4 @endif">
                    <h2 class="text-lg font-bold text-center mt-2 mb-2">{{ $section->name }}</h2>
                    @if($section->instructions)
                        <div class="mb-4 italic"><p><em>{{ $section->instructions }}</em></p></div>
                    @endif

                    @foreach ($section->rules as $rule)
                        @php
                            $ruleQuestions = $paper->questions->where('pivot.section_rule_id', $rule->id)->values();
                            $displayCount = $rule->total_questions_to_display ?: $rule->number_of_questions_to_select;
                        @endphp

                        @if($ruleQuestions->isNotEmpty())
                            <div class="mb-3 font-semibold">
                                @if($displayCount > $rule->number_of_questions_to_select)
                                    Attempt any {{ $rule->number_of_questions_to_select }} of the following {{ $displayCount }} questions.
                                @else
                                    Attempt all of the following {{ $displayCount }} questions.
                                @endif
                                <span class="ml-2 text-xs text-gray-600">({{ strtoupper($rule->question_type) }}, {{ $rule->marks_per_question }} marks each)</span>
                            </div>

                            @foreach ($ruleQuestions->take($displayCount) as $question)
                                <div class="mb-2">
                                    <div class="flex justify-between items-start font-bold mb-1">
                                        <div class="flex-grow pr-4">Q.{{ $qNo++ }}) {!! $question->question_text !!}</div>
                                        <div class="flex-shrink-0">[{{ $question->pivot->marks }}]</div>
                                    </div>

                                    @if($question->question_image_path)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $question->question_image_path) }}" alt="Question Diagram" style="max-width: 100%; height: auto;">
                                        </div>
                                    @endif

                                    @if($question->question_type === 'mcq')
                                        @php $optionsArray = is_array($question->options) ? $question->options : json_decode($question->options, true); @endphp
                                        @if(is_array($optionsArray))
                                            {{-- AFTER --}}
                                                <div class="ml-8 mt-1">
                                                    @foreach($optionsArray as $optionIndex => $option)
                                                        <span class="mr-6">({{ chr(65 + $optionIndex) }}) {!! $option !!}</span>
                                                    @endforeach
                                                </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @endforeach

        {{-- ✨ NEW: CASE 2: Paper was created manually without a blueprint --}}
        @elseif ($paper->questions->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-bold text-center mt-6 mb-4">Questions</h2>

                @foreach ($paper->questions as $question)
                    <div class="mb-2">
                        <div class="flex justify-between items-start font-bold mb-1">
                            <div class="flex-grow pr-4">Q.{{ $qNo++ }}) {!! $question->question_text !!}</div>
                            <div class="flex-shrink-0">[{{ $question->pivot->marks }}]</div>
                        </div>

                        @if($question->question_image_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $question->question_image_path) }}" alt="Question Diagram" style="max-width: 100%; height: auto;">
                            </div>
                        @endif

                        @if($question->question_type === 'mcq')
                            @php $optionsArray = is_array($question->options) ? $question->options : json_decode($question->options, true); @endphp
                            @if(is_array($optionsArray))
                                <div class="ml-8 mt-1">
                                    @foreach($optionsArray as $optionIndex => $option)
                                        <p class="my-1">({{ chr(65 + $optionIndex) }}) {!! $option !!}</p>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

        {{-- CASE 3: Paper has no questions yet --}}
        @else
            <div class="text-center text-gray-500 py-10">No questions have been added to this paper yet.</div>
        @endif

        <div class="text-center font-bold mt-10 pt-5 border-t"><p>****** All the Best ******</p></div>
    </div>
</body>
</html>