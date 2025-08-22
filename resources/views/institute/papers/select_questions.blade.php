<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Select Questions for: <span class="italic">{{ $paper->title }}</span>
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
  {{-- NEW: Floating Marks Counter --}}
    {{-- UPDATED: Added z-50 class to ensure it's on top --}}
    <div id="marks-counter" class="fixed bottom-5 right-5 z-50 bg-gray-800 text-white py-3 px-5 rounded-lg shadow-lg text-lg">
        Selected Marks: <span id="selected-marks-count" class="font-bold">{{ $currentMarks }}</span> / {{ $paper->total_marks }}
    </div>
    <div id="toast"
        class="fixed bottom-24 right-5 z-50 bg-gray-900 text-white text-sm px-4 py-2 rounded-md shadow-lg hidden">
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col md:flex-row md:space-x-6">

                    {{-- Left Column: Filters --}}
                    <div class="w-full md:w-1/4 border-b md:border-b-0 md:border-r pb-6 md:pb-0 md:pr-6 mb-6 md:mb-0">
                        <form id="filter-form" method="GET" action="{{ route('institute.papers.questions.select', $paper) }}">
                            <h3 class="text-lg font-medium mb-4">Filters</h3>
                            
                            <div class="mb-6">
                                <h4 class="font-medium mb-2 text-sm text-gray-600">Chapter</h4>
                                <select name="chapter" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="all" @selected($currentChapter == 'all')>All Chapters</option>
                                    @foreach($chapters as $chapter)
                                        <option value="{{ $chapter->id }}" @selected($currentChapter == $chapter->id)>{{ $chapter->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <h4 class="font-medium mb-2 text-sm text-gray-600">Question Type</h4>
                                <div class="space-y-2">
                                    <div><label class="inline-flex items-center"><input type="checkbox" name="types[]" class="type-filter-checkbox rounded" value="mcq" @checked(in_array('mcq', $currentTypes))> <span class="ml-2">MCQ</span></label></div>
                                    <div><label class="inline-flex items-center"><input type="checkbox" name="types[]" class="type-filter-checkbox rounded" value="long" @checked(in_array('long', $currentTypes))> <span class="ml-2">Long Answer</span></label></div>
                                    <div><label class="inline-flex items-center"><input type="checkbox" name="types[]" class="type-filter-checkbox rounded" value="short" @checked(in_array('short', $currentTypes))> <span class="ml-2">Short Answer</span></label></div>
                                    <div><label class="inline-flex items-center"><input type="checkbox" name="types[]" class="type-filter-checkbox rounded" value="true_false" @checked(in_array('true_false', $currentTypes))> <span class="ml-2">True/False</span></label></div>
                                    <button type="submit" class="mt-2 w-full px-4 py-2 bg-red-600 text-white rounded-md text-sm">Apply Filters</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Right Column: Question List --}}
                    <div class="w-full md:w-3/4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Available Questions</h3>
                            <div class="flex items-center gap-2">
        {{-- Quick check after selecting --}}
        <a href="{{ route('institute.papers.preview', $paper) }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
           Preview Paper
        </a>

        <a href="{{ route('institute.dashboard') }}"
           class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">
           Go to Dashboard
        </a>
    </div>
                        </div>
                        
                        <div id="question-list-wrapper" class="space-y-4 min-h-[60vh]">
                            @forelse ($questions as $question)
                                <div class="question-item p-4 border rounded-lg">
                                    <label class="flex items-start space-x-3">
                                        <input 
                                            type="checkbox" 
                                            class="question-checkbox mt-1 rounded"
                                            value="{{ $question->id }}"
                                            data-marks="{{ $question->marks }}" {{-- Add marks data --}}
                                            @checked($existingQuestionIds->contains($question->id))
                                        >
                                        <div class="flex-1">
                                            <div class="font-semibold">{!! $question->question_text !!}</div>
                                            @if($question->question_type === 'mcq')
                                                @php $optionsArray = json_decode($question->options, true); @endphp
                                                @if(is_array($optionsArray))
                                                    <div class="mt-2 pl-4 text-sm text-gray-700 space-y-1">
                                                        @foreach($optionsArray as $index => $option)
                                                            <div><span class="font-semibold">{{ chr(65 + $index) }})</span> {!! $option !!}</div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endif
                                            <small class="text-gray-500 mt-2 block">
                                               Type: {{ strtoupper($question->question_type) }} | Marks: {{ $question->marks }} | Difficulty: {{ ucfirst($question->difficulty) }}
                                            </small>
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <div class="text-center py-12 text-gray-500">
                                    <p>No questions found matching your current filters.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-4">
                            {{ $questions->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

  

   <script>
        document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.question-checkbox');
        const csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        const selectedMarksSpan = document.getElementById('selected-marks-count');
        const totalAllowed = {{ (int) $paper->total_marks }};
        const attachUrl = "{{ route('institute.papers.questions.attach', $paper) }}";
        const detachUrl = "{{ route('institute.papers.questions.detach', $paper) }}";

        // (Optional) auto-submit when any type filter changes
        const filterForm = document.getElementById('filter-form');
        document.querySelectorAll('.type-filter-checkbox').forEach(cb => {
            cb.addEventListener('change', () => filterForm.submit());
        });

        function showToast(msg) {
            const el = document.getElementById('toast');
            if (!el) { alert(msg); return; }
            el.textContent = msg;
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 1800);
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', async function () {
            const questionId = this.value;
            const isChecked = this.checked;
            const marks = parseInt(this.getAttribute('data-marks'), 10) || 0;

            const before = parseInt(selectedMarksSpan.textContent, 10) || 0;
            const next = isChecked ? (before + marks) : (before - marks);

            // Client-side cap to avoid a roundtrip if exceeding
            if (isChecked && next > totalAllowed) {
                this.checked = false;
                showToast('Total marks limit reached');
                return;
            }

            // optimistic UI
            selectedMarksSpan.textContent = String(next);
            this.disabled = true;
            this.classList.add('opacity-60', 'cursor-not-allowed');

            try {
                    const res = await fetch(isChecked ? attachUrl : detachUrl, {
                        method: 'POST',
                        headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ question_id: questionId }),
                    });

                    let data = null;
                    try { data = await res.json(); } catch (_) {}

                    if (!res.ok) {
                        let msg = 'An error occurred. Please try again.';
                        if (res.status === 422 && data && data.error) {
                        msg = data.error;
                        if (typeof data.current === 'number') {
                            selectedMarksSpan.textContent = String(data.current);
                        } else {
                            selectedMarksSpan.textContent = String(before);
                        }
                        } else {
                        selectedMarksSpan.textContent = String(before);
                        }
                        this.checked = !isChecked;
                        showToast(msg);
                    } else {
                        // Success: if API returned current, trust it
                        if (data && typeof data.current === 'number') {
                        selectedMarksSpan.textContent = String(data.current);
                        }
                    }
                    } catch (e) {
                    selectedMarksSpan.textContent = String(before);
                    this.checked = !isChecked;
                    showToast('Network error. Please try again.');
                    } finally {
                    this.disabled = false;
                    this.classList.remove('opacity-60', 'cursor-not-allowed');
                    }

            });
        });
        });
        </script>


</x-app-layout>
