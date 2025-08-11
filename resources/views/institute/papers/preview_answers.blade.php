<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Answer Key: <span class="italic">{{ $paper->title }}</span>
        </h2>
    </x-slot>

    {{-- MathJax Configuration and Library --}}
    <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']]
      }
    };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 text-center no-print">
                <button onclick="window.print()" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Print or Save as PDF
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-12 text-gray-900"> 
                    <div class="text-center mb-10">
                        <h1 class="text-2xl font-bold">Answer Key</h1>
                        <h2 class="text-xl">{{ $paper->title }}</h2>
                    </div>

                    <div class="space-y-8">
                        @php $questionCounter = 1; @endphp

                        @if($blueprint)
                            {{-- If a blueprint exists, render answers section by section --}}
                            @foreach($blueprint->sections as $section)
                                <div>
                                    <h3 class="text-lg font-semibold border-b pb-2 mb-4">{{ $section->name }}</h3>
                                    <div class="space-y-6">
                                        @foreach($section->rules as $rule)
                                            @php
                                                $sectionQuestions = $paper->questions->filter(function ($question) use ($rule) {
                                                    return $question->question_type == $rule->question_type && $question->pivot->marks == $rule->marks_per_question;
                                                });
                                            @endphp
                                            @foreach($sectionQuestions as $question)
                                                <div class="flex items-start space-x-4">
                                                    <span class="font-bold">Q{{ $questionCounter++ }}.</span>
                                                    <div class="flex-1">
                                                        <p class="text-gray-600 text-sm italic mb-2">{!! $question->question_text !!}</p>
                                                        <div class="font-semibold">
                                                            <strong>Answer: </strong>
                                                            @if($question->question_type === 'mcq')
                                                                <span>{{ $question->correct_answer }}</span>
                                                            @else
                                                                <span>{!! $question->answer_text !!}</span>
                                                            @endif
                                                            
                                                            {{-- THIS IS THE FIX: Display Answer Image --}}
                                                            @if($question->answer_image_path)
                                                                <div class="mt-2">
                                                                    <img src="{{ asset('storage/' . $question->answer_image_path) }}" alt="Answer Diagram" style="max-width: 300px; height: auto; border: 1px solid #ddd;">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Fallback for papers without a blueprint --}}
                            @foreach($paper->questions as $question)
                                <div class="flex items-start space-x-4">
                                    <span class="font-bold">Q{{ $questionCounter++ }}.</span>
                                    <div class="flex-1">
                                        <p class="text-gray-600 text-sm italic mb-2">{!! $question->question_text !!}</p>
                                        <div class="font-semibold">
                                            <strong>Answer: </strong>
                                            @if($question->question_type === 'mcq')
                                                <span>{{ $question->correct_answer }}</span>
                                            @else
                                                <span>{!! $question->answer_text !!}</span>
                                            @endif

                                            {{-- THIS IS THE FIX: Display Answer Image --}}
                                            @if($question->answer_image_path)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $question->answer_image_path) }}" alt="Answer Diagram" style="max-width: 300px; height: auto; border: 1px solid #ddd;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
