<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Preview: {{ $paper->title }}</title>
    
    {{-- MathJax Configuration --}}
    <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']]
      }
    };
    </script>
    {{-- MathJax Library --}}
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; background-color: #f0f0f0; }
        .container { max-width: 800px; margin: 20px auto; padding: 40px; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 20px; }
        .instructions { border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; }
        .question { margin-bottom: 20px; page-break-inside: avoid; }
        .question-text { font-weight: bold; }
        .options { margin-left: 20px; margin-top: 10px; }
        .marks { float: right; font-weight: bold; }
        .print-button-container { text-align: center; padding: 20px; }

        /* This CSS hides the button when printing */
        @media print {
            .no-print {
                display: none !important;
            }
            body { background-color: white; }
            .container { margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="print-button-container no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print or Save as PDF</button>
    </div>

    <div class="container">
        <div class="header">
            <h1>{{ $paper->institute->institute_name ?? 'Test Paper' }}</h1>
            <h2>{{ $paper->title }}</h2>
            <p><strong>Subject:</strong> {{ $paper->subject->name }} | <strong>Class:</strong> {{ $paper->academicClass->name }}</p>
            <p><strong>Total Marks:</strong> {{ $paper->total_marks }}</p>
        </div>

        @if($paper->instructions)
            <div class="instructions">
                <strong>Instructions:</strong>
                <p>{!! nl2br(e($paper->instructions)) !!}</p>
            </div>
        @endif

        @foreach($paper->questions as $index => $question)
            <div class="question">
                <p>
                    <span class="question-text">Q{{ $index + 1 }}. {!! $question->question_text !!}</span>
                    <span class="marks">[{{ $question->pivot->marks }} Marks]</span>
                </p>
                
                {{-- THIS IS THE NEW MANUAL DECODING LOGIC --}}
                @if($question->question_type === 'mcq')
                    @php
                        $optionsArray = json_decode($question->options, true);
                    @endphp
                    @if(is_array($optionsArray))
                        <div class="options">
                            @foreach($optionsArray as $optionIndex => $option)
                                <p style="margin: 5px 0;">({{ chr(65 + $optionIndex) }}) {!! $option !!}</p>
                            @endforeach
                        </div>
                    @endif
                @endif

                {{-- For True/False, we just show the question text, the answer is in the answer key --}}

            </div>
        @endforeach
    </div>
</body>
</html>
