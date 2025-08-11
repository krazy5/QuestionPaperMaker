<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preview: {{ $paper->title }}</title>
    
    {{-- MathJax Configuration and Library --}}
    <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']]
      }
    };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Add print-specific styles --}}
    <style>
        @media print {
            body {
                background-color: white !important;
            }
            .no-print {
                display: none !important;
            }
            .printable-container {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-serif leading-normal">
    <div class="text-center py-4 no-print">
        <button onclick="window.print()" class="px-6 py-2 bg-gray-700 text-white rounded-md shadow-md hover:bg-gray-800">
            Print or Save as PDF
        </button>
    </div>

    <div class="max-w-4xl min-h-[11in] mx-auto my-8 p-12 bg-white shadow-lg border printable-container">
        
        {{-- Professional Header Section --}}
        <div class="mb-4 border-b-2 border-black pb-4">
            <div class="flex justify-between items-start">
                <div class="w-1/5">
                    {{-- Logo Placeholder --}}
                </div>
                <div class="w-3/5 text-center">
                    <h1 class="text-lg font-bold">{{ $paper->institute->department ?? 'DEPARTMENT OF EXAMINATIONS' }}</h1>
                    <h2 class="text-xl font-bold my-1">{{ $paper->institute->institute_name ?? 'Name of University' }}</h2>
                    <h3 class="text-md font-semibold">{{ $paper->title }}</h3>
                </div>
                <div class="w-1/5 text-right">
                    {{-- Placeholder for a paper code or serial number --}}
                </div>
            </div>
            <div class="flex justify-between items-end text-sm font-bold mt-4">
                <span>Time: {{ $paper->time_allowed }}</span>
                <span class="text-center flex-grow">Subject: {{ $paper->subject->name }} ({{ $paper->academicClass->name }})</span>
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

        @php $questionCounter = 1; @endphp

        @if($blueprint)
            @foreach($blueprint->sections as $section)
                <div class="text-lg font-bold text-center mt-6 mb-4">{{ $section->name }}</div>
                @if($section->instructions)
                    <div class="mb-4 italic"><p><em>{{ $section->instructions }}</em></p></div>
                @endif

                @foreach($section->rules as $rule)
                    @php
                        $sectionQuestions = $paper->questions->filter(function ($question) use ($rule) {
                            return $question->question_type == $rule->question_type && $question->pivot->marks == $rule->marks_per_question;
                        });
                    @endphp
                    @foreach($sectionQuestions as $question)
                        <div class="mb-4">
                           
                            <div class="flex justify-between items-start font-bold mb-1">
                                <div class="flex-grow pr-4">Q.{{ $questionCounter++ }}) {!! $question->question_text !!}</div>
                                <div class="flex-shrink-0">[{{ $question->pivot->marks }}]</div>
                            </div>
                             @if($question->question_image_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $question->question_image_path) }}" alt="Question Diagram" style="max-width: 100%; height: auto;">
                                </div>
                            @endif
                            @if($question->question_type === 'mcq')
                                @php $optionsArray = json_decode($question->options, true); @endphp
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
                @endforeach
            @endforeach
        @else
            {{-- Fallback for papers created without a blueprint --}}
            @foreach($paper->questions as $question)
                <div class="mb-4">
                    
                    <div class="flex justify-between items-start font-bold mb-1">
                        <div class="flex-grow pr-4">Q.{{ $questionCounter++ }}) {!! $question->question_text !!}</div>
                        <div class="flex-shrink-0">[{{ $question->pivot->marks }}]</div>
                    </div>
                     @if($question->question_image_path)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $question->question_image_path) }}" alt="Question Diagram" style="max-width: 100%; height: auto;">
                        </div>
                    @endif
                    @if($question->question_type === 'mcq')
                        @php $optionsArray = json_decode($question->options, true); @endphp
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
        @endif

        <div class="text-center font-bold mt-10 pt-5 border-t"><p>****** All the Best ******</p></div>
    </div>
</body>
</html>
