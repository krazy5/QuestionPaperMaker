<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Answer Key: {{ $paper->title }}</title>

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
        <a href="{{ route('institute.papers.preview', $paper) }}" class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 no-print">
            Back to Paper Preview
        </a>
    </div>

    <div class="max-w-4xl min-h-[11in] mx-auto my-8 p-12 bg-white shadow-lg border printable-container">

        <div class="mb-4 border-b-2 border-black pb-4 text-center">
            <h1 class="text-2xl font-bold">ANSWER KEY</h1>
            <h2 class="text-xl font-bold my-1">{{ $paper->institute->institute_name ?? 'Name of University' }}</h2>
            <h3 class="text-md font-semibold">{{ $paper->title }}</h3>
        </div>

        @php $qNo = 1; @endphp

        {{-- CASE 1: Paper was generated from a Blueprint --}}
        @if ($blueprint && $paper->questions->isNotEmpty())
            @foreach ($blueprint->sections as $section)
                <div class="mb-8 @if(!$loop->first) pt-4 @endif">
                    <h2 class="text-lg font-bold text-center mt-6 mb-4">{{ $section->name }}</h2>

                    @foreach ($section->rules as $rule)
                        @php
                            $ruleQuestions = $paper->questions->where('pivot.section_rule_id', $rule->id)->values();
                            $displayCount = $rule->total_questions_to_display ?: $rule->number_of_questions_to_select;
                        @endphp

                        @if($ruleQuestions->isNotEmpty())
                            <div class="mb-3 font-semibold">
                                Answers for {{ $displayCount }} {{ strtoupper($rule->question_type) }} questions ({{ $rule->marks_per_question }} marks each)
                            </div>

                            @foreach ($ruleQuestions->take($displayCount) as $question)
                                <div class="mb-4">
                                    <div class="font-bold mb-1">Q.{{ $qNo++ }}) {!! $question->question_text !!}</div>

                                    <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                                        <strong class="text-green-800">Answer:</strong>
                                        @if($question->question_type === 'mcq')
                                            @php
                                                $optionsArray = is_array($question->options) ? $question->options : json_decode($question->options, true);
                                                $answer = $question->correct_answer ?? null;
                                                $map = ['A'=>0,'B'=>1,'C'=>2,'D'=>3];
                                                $answerText = (is_array($optionsArray) && isset($map[strtoupper((string)$answer)]))
                                                    ? ($optionsArray[$map[strtoupper((string)$answer)]] ?? $answer)
                                                    : ($answer ?? 'N/A');
                                            @endphp
                                            <span class="font-semibold">{!! $answerText !!}</span>
                                        @else
                                            <div class="prose max-w-none">{!! $question->solution_text ?? $question->answer_text ?? '—' !!}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @endforeach

        {{-- ✨ NEW: CASE 2: Paper was created manually without a blueprint --}}
        @elseif ($paper->questions->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-bold text-center mt-6 mb-4">Questions & Answers</h2>
                @foreach ($paper->questions as $question)
                    <div class="mb-4">
                        <div class="font-bold mb-1">Q.{{ $qNo++ }}) {!! $question->question_text !!}</div>

                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                            <strong class="text-green-800">Answer:</strong>
                            @if($question->question_type === 'mcq')
                                @php
                                    $optionsArray = is_array($question->options) ? $question->options : json_decode($question->options, true);
                                    $answer = $question->correct_answer ?? null;
                                    $map = ['A'=>0,'B'=>1,'C'=>2,'D'=>3];
                                    $answerText = (is_array($optionsArray) && isset($map[strtoupper((string)$answer)]))
                                        ? ($optionsArray[$map[strtoupper((string)$answer)]] ?? $answer)
                                        : ($answer ?? 'N/A');
                                @endphp
                                <span class="font-semibold">{!! $answerText !!}</span>
                            @else
                                <div class="prose max-w-none">{!! $question->solution_text ?? $question->answer_text ?? '—' !!}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        {{-- CASE 3: Paper has no questions yet --}}
        @else
            <div class="text-center text-gray-500 py-10">No questions have been added to this paper yet.</div>
        @endif

    </div>
</body>
</html>