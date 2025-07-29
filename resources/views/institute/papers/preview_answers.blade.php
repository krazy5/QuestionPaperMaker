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
            
            {{-- Print Button --}}
            <div class="mb-4 text-center no-print">
                <button onclick="window.print()" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Print or Save as PDF
                </button>
            </div>

            {{-- Answer Sheet --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-12 text-gray-900">
                    <div class="text-center mb-10">
                        <h1 class="text-2xl font-bold">Answer Key</h1>
                        <h2 class="text-xl">{{ $paper->title }}</h2>
                    </div>

                    <div class="space-y-6">
                        @foreach($paper->questions as $index => $question)
                            <div class="flex items-start space-x-4">
                                <span class="font-bold">Q{{ $index + 1 }}.</span>
                                <div class="flex-1">
                                    @if($question->question_type === 'mcq')
                                        <p><strong>Correct Answer:</strong> {{ $question->correct_answer }}</p>
                                        {{-- Optionally display the full option text --}}
                                        @php
                                            // Convert 'A' to index 0, 'B' to 1, etc.
                                            $correctIndex = ord($question->correct_answer) - ord('A');
                                        @endphp
                                        @if(is_array($question->options) && isset($question->options[$correctIndex]))
                                            <p class="text-gray-600">({!! $question->options[$correctIndex] !!})</p>
                                        @endif
                                    @else
                                        {{-- For long, short, etc. type questions --}}
                                        <p>{!! $question->answer_text !!}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS to hide button when printing --}}
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
