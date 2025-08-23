<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Select Questions for: <span class="italic">{{ $paper->title }}</span>
        </h2>
    </x-slot>

    {{-- MathJax Configuration and Library --}}
    <script>
      window.MathJax = { tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] } };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    {{-- Floating Marks Counter (IDs preserved) --}}
    <div id="marks-counter"
         class="fixed bottom-5 right-5 z-50 bg-gray-900 text-white py-3 px-5 rounded-xl shadow-lg text-base sm:text-lg ring-1 ring-gray-700/50">
        Selected Marks:
        <span id="selected-marks-count" class="font-bold">{{ $currentMarks }}</span>
        / {{ $paper->total_marks }}
        <div class="mt-2 h-1.5 w-full rounded-full bg-gray-700 overflow-hidden">
            <div id="marks-progress" class="h-1.5 bg-indigo-500" style="width: 0%"></div>
        </div>
    </div>

    <div id="toast"
         class="fixed bottom-24 right-5 z-50 bg-gray-900 text-white text-sm px-4 py-2 rounded-md shadow-lg hidden"
         role="status" aria-live="polite"></div>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100 flex flex-col lg:flex-row lg:space-x-6">

                    {{-- Left Column: Filters (IDs/names preserved) --}}
                    <aside class="w-full lg:w-1/4 lg:sticky lg:top-16 lg:self-start mb-6 lg:mb-0">
                        <form id="filter-form" method="GET" action="{{ route('institute.papers.questions.select', $paper) }}"
                              class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 p-4 sm:p-5 space-y-6">
                            <div>
                                <h3 class="text-base font-semibold">Filters</h3>
                            </div>

                            <div>
                                <h4 class="font-medium mb-2 text-sm text-gray-600 dark:text-gray-400">Chapter</h4>
                                <select name="chapter" onchange="this.form.submit()"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500">
                                    <option value="all" @selected($currentChapter == 'all')>All Chapters</option>
                                    @foreach($chapters as $chapter)
                                        <option value="{{ $chapter->id }}" @selected($currentChapter == $chapter->id)>{{ $chapter->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <h4 class="font-medium mb-2 text-sm text-gray-600 dark:text-gray-400">Question Type</h4>
                                <div class="grid grid-cols-2 gap-y-2 text-sm">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="types[]" class="type-filter-checkbox rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                               value="mcq" @checked(in_array('mcq', $currentTypes))>
                                        <span class="ml-2">MCQ</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="types[]" class="type-filter-checkbox rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                               value="long" @checked(in_array('long', $currentTypes))>
                                        <span class="ml-2">Long Answer</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="types[]" class="type-filter-checkbox rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                               value="short" @checked(in_array('short', $currentTypes))>
                                        <span class="ml-2">Short Answer</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="types[]" class="type-filter-checkbox rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                               value="true_false" @checked(in_array('true_false', $currentTypes))>
                                        <span class="ml-2">True/False</span>
                                    </label>
                                </div>
                                <button type="submit"
                                        class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 rounded-lg bg-rose-600 text-white hover:bg-rose-700 shadow-sm">
                                    Apply Filters
                                </button>
                            </div>

                            @if(request()->hasAny(['chapter','types']))
                                <div class="pt-2">
                                    <a href="{{ route('institute.papers.questions.select', $paper) }}"
                                       class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Reset filters</a>
                                </div>
                            @endif
                        </form>
                    </aside>

                    {{-- Right Column: Question List --}}
                    <section class="w-full lg:w-3/4">
                        <div class="rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60">
                            <header class="p-4 sm:p-5 border-b border-gray-200 dark:border-gray-800">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div>
                                        <h3 class="text-lg font-semibold">Available Questions</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            @if($questions->total() > 0)
                                                Showing <span class="font-medium">{{ $questions->firstItem() }}</span>–<span class="font-medium">{{ $questions->lastItem() }}</span>
                                                of <span class="font-medium">{{ $questions->total() }}</span>
                                            @else
                                                No results
                                            @endif
                                            <span class="mx-2">•</span>
                                            Selected questions: <span id="selected-questions-count" class="font-medium">0</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('institute.papers.preview', $paper) }}"
                                           class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                                            Preview Paper
                                        </a>
                                        <a href="{{ route('institute.dashboard') }}"
                                           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm">
                                           Go to Dashboard
                                        </a>
                                    </div>
                                </div>
                            </header>

                            <div id="question-list-wrapper" class="p-4 sm:p-5 space-y-4 min-h-[60vh]">
                                @forelse ($questions as $question)
                                    <div class="question-item p-4 sm:p-5 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900/60 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                        <label class="flex items-start gap-3">
                                            <input
                                                type="checkbox"
                                                class="question-checkbox mt-1 rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500"
                                                value="{{ $question->id }}"
                                                data-marks="{{ $question->marks }}"
                                                @checked($existingQuestionIds->contains($question->id))
                                            >
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 dark:text-gray-100 prose prose-sm max-w-none dark:prose-invert">
                                                    {!! $question->question_text !!}
                                                </div>

                                                @if($question->question_type === 'mcq')
                                                    @php $optionsArray = json_decode($question->options, true); @endphp
                                                    @if(is_array($optionsArray))
                                                        <div class="mt-3 pl-4 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                                            @foreach($optionsArray as $index => $option)
                                                                <div class="flex">
                                                                    <span class="font-semibold mr-2">{{ chr(65 + $index) }})</span>
                                                                    <span class="prose prose-sm max-w-none dark:prose-invert">{!! $option !!}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif

                                                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                        {{ strtoupper($question->question_type) }}
                                                    </span>
                                                    <span>Marks: <span class="font-medium">{{ $question->marks }}</span></span>
                                                    <span>Difficulty: <span class="font-medium">{{ ucfirst($question->difficulty) }}</span></span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                        No questions found matching your current filters.
                                    </div>
                                @endforelse
                            </div>

                            <footer class="p-3 border-t border-gray-200 dark:border-gray-800">
                                {{ $questions->links() }}
                            </footer>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

    {{-- Attach/Detach + UI logic (IDs and endpoints preserved) --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.question-checkbox');
        const csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
        const selectedMarksSpan = document.getElementById('selected-marks-count');
        const selectedQuestionsSpan = document.getElementById('selected-questions-count');
        const progress = document.getElementById('marks-progress');
        const totalAllowed = {{ (int) $paper->total_marks }};
        const attachUrl = "{{ route('institute.papers.questions.attach', $paper) }}";
        const detachUrl = "{{ route('institute.papers.questions.detach', $paper) }}";

        // Auto-submit when any type filter changes
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

        function updateCountsFromDOM() {
          const checked = document.querySelectorAll('.question-checkbox:checked');
          let marks = 0;
          checked.forEach(cb => { marks += parseInt(cb.getAttribute('data-marks') || '0', 10); });
          selectedMarksSpan.textContent = String(marks);
          selectedQuestionsSpan.textContent = String(checked.length);
          const pct = Math.min(100, Math.max(0, (marks / totalAllowed) * 100));
          if (progress) progress.style.width = pct + '%';
        }

        // Initialize counts/progress on load
        updateCountsFromDOM();

        checkboxes.forEach(checkbox => {
          checkbox.addEventListener('change', async function () {
            const questionId = this.value;
            const isChecked = this.checked;
            const marks = parseInt(this.getAttribute('data-marks'), 10) || 0;

            const beforeMarks = parseInt(selectedMarksSpan.textContent, 10) || 0;
            const nextMarks = isChecked ? (beforeMarks + marks) : (beforeMarks - marks);

            // Client-side cap to avoid a roundtrip if exceeding
            if (isChecked && nextMarks > totalAllowed) {
              this.checked = false;
              showToast('Total marks limit reached');
              return;
            }

            // Optimistic UI
            selectedMarksSpan.textContent = String(nextMarks);
            this.disabled = true;
            this.classList.add('opacity-60', 'cursor-not-allowed');
            updateCountsFromDOM();

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
                    selectedMarksSpan.textContent = String(beforeMarks);
                  }
                } else {
                  selectedMarksSpan.textContent = String(beforeMarks);
                }
                this.checked = !isChecked;
                showToast(msg);
              }

            } catch (e) {
              selectedMarksSpan.textContent = String(beforeMarks);
              this.checked = !isChecked;
              showToast('Network error. Please try again.');
            } finally {
              this.disabled = false;
              this.classList.remove('opacity-60', 'cursor-not-allowed');
              updateCountsFromDOM();
            }
          });
        });
      });
    </script>
</x-app-layout>
