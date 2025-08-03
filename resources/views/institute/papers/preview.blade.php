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
        /* --- General Page Styles --- */
        body {
            font-family: "Times New Roman", Times, serif; /* Professional serif font */
            font-size: 16px;
            line-height: 1.6;
            background-color: #e9e9e9; /* Light grey background to highlight the paper */
        }
        .container {
            max-width: 8.5in; /* Standard paper width */
            min-height: 11in; /* Standard paper height */
            margin: 30px auto;
            padding: 50px 60px; /* Generous padding */
            background-color: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border: 1px solid #ccc;
        }

        /* --- Header Section --- */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid black;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .header h2 {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .header-details {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-weight: bold;
        }

        /* --- Instructions --- */
        .instructions {
            margin-bottom: 30px;
            font-style: italic;
        }
        .instructions p {
            margin: 5px 0 0 0;
        }

        /* --- Question Formatting --- */
        .question {
            margin-bottom: 25px;
        }
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .question-text {
            flex-grow: 1; /* Allows question text to take up available space */
        }
        .marks {
            white-space: nowrap; /* Prevents marks from wrapping */
            margin-left: 20px;
        }
        .options {
            margin-left: 30px;
            margin-top: 10px;
        }
        .options p {
            margin: 8px 0;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin-top: 25px;
        }

        /* --- Print-Specific Styles --- */
        .print-button-container {
            text-align: center;
            padding: 20px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white;
            }
            .container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button-container no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px; border: 1px solid #ccc; background-color: #f0f0f0;">
            Print or Save as PDF
        </button>
    </div>

    <div class="container">
        {{-- Header Section --}}
        <div class="header">
            <h1>{{ $paper->institute->institute_name ?? 'Sample Institute' }}</h1>
            <h2>{{ $paper->title }}</h2>
            <div class="header-details">
                <span>Time: {{ $paper->time_allowed }}</span>
                <span>Subject: {{ $paper->subject->name }} ({{ $paper->academicClass->name }})</span>
                <span>Marks: {{ $paper->total_marks }}</span>
            </div>
        </div>

        {{-- Instructions Section --}}
        @if($paper->instructions)
            <div class="instructions">
                <strong>Instructions:</strong>
                <p>{!! nl2br(e($paper->instructions)) !!}</p>
            </div>
        @endif

        {{-- Questions Loop --}}
        @foreach($paper->questions as $index => $question)
            <div class="question">
                <div class="question-header">
                    <div class="question-text">Q.{{ $index + 1 }}) {!! $question->question_text !!}</div>
                    <div class="marks">[{{ $question->pivot->marks }}]</div>
                </div>
                
                @if($question->question_type === 'mcq')
                    @php $optionsArray = json_decode($question->options, true); @endphp
                    @if(is_array($optionsArray))
                        <div class="options">
                            @foreach($optionsArray as $optionIndex => $option)
                                <p>({{ chr(65 + $optionIndex) }}) {!! $option !!}</p>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
            @if(!$loop->last)
                <hr>
            @endif
        @endforeach

        <div style="text-align: center; font-weight: bold; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ccc;">
            <p>****** All the Best ******</p>
        </div>

    </div>
</body>
</html>
