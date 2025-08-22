<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Building Paper: <span class="italic">{{ $paper->title }}</span>
        </h2>
    </x-slot>

    {{-- MathJax Configuration and Library --}}
    <script>
      window.MathJax = {
        tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] }
      };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Top controls: responsive --}}
            <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Blueprint: {{ $blueprint->name }}</h3>
                    <p class="text-sm text-gray-600">Fill each section below to complete your paper.</p>
                    {{-- Global chapter selection summary (filled by JS) --}}
                    <p id="chapter-summary" class="text-sm text-indigo-700 mt-1 hidden"></p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    {{-- Open Chapter Selector --}}
                    <button type="button"
                            id="open-chapter-modal-btn"
                            class="px-4 py-2 bg-white border border-indigo-600 text-indigo-700 rounded-md hover:bg-indigo-50">
                        Choose Chapters
                    </button>

                    {{-- Auto-fill form (hidden inputs for selected chapters injected by JS) --}}
                    <form id="auto-fill-form"
                          action="{{ route('institute.papers.auto_fill', $paper) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('This will replace all currently selected questions. Are you sure?')">
                        @csrf
                        <div id="selected-chapters-inputs"></div>

                        <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                            Auto-fill Paper
                        </button>
                    </form>

                    <a href="{{ route('institute.dashboard') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-center">
                        Finish & Go to Dashboard
                    </a>
                </div>
            </div>

            {{-- Sections + Rules --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-8">
                    @foreach($blueprint->sections as $section)
                        <section class="p-6 border rounded-lg">
                            <header class="border-b pb-4 mb-4">
                                <h3 class="text-xl font-semibold">{{ $section->name }}</h3>
                                @if($section->instructions)
                                    <p class="text-sm text-gray-600 italic mt-1">"{{ $section->instructions }}"</p>
                                @endif
                            </header>

                            <div class="space-y-4">
                                @foreach($section->rules as $rule)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-md">
                                        <div class="pr-3">
                                            <p class="font-medium">
                                                Select
                                                <strong>{{ $rule->number_of_questions_to_select }}</strong>
                                                <strong>{{ strtoupper($rule->question_type) }}</strong>
                                                questions, each worth
                                                <strong>{{ $rule->marks_per_question }}</strong> marks.
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-gray-800" id="rule-count-{{$rule->id}}">
                                                {{ $ruleCounts[$rule->id] ?? 0 }} / {{ $rule->number_of_questions_to_select }}
                                            </p>

                                            <button type="button"
                                                    class="mt-1 text-sm text-indigo-600 hover:underline open-modal-btn"
                                                    data-rule-id="{{ $rule->id }}">
                                                Add / View Questions
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- GLOBAL Chapter Selection Modal --}}
    <div id="chapter-modal"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-2/3 max-h-[90vh] flex flex-col">
        <header class="p-4 border-b flex justify-between items-center flex-shrink-0">
          <h3 class="text-lg font-medium">Choose Chapters</h3>
          <button id="close-chapter-modal-btn" class="text-2xl">&times;</button>
        </header>

        <main class="flex-grow overflow-y-auto p-4 space-y-3">
          <div class="flex items-center justify-between gap-3">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" id="chapter-select-all" class="rounded" checked>
              <span class="font-medium">Select all chapters</span>
            </label>
            <input id="chapter-search"
                   type="text"
                   placeholder="Search chapters…"
                   class="border rounded-md px-3 py-1 w-48 md:w-64">
          </div>

          <div id="chapter-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($chapters as $chapter)
              <label class="inline-flex items-start gap-2 p-2 border rounded-md">
                <input type="checkbox"
                       class="chapter-choice rounded"
                       value="{{ $chapter->id }}">
                <span class="text-sm">{{ $chapter->name }}</span>
              </label>
            @endforeach
          </div>
        </main>

        <footer class="p-4 border-t flex justify-end gap-2">
          <button id="chapter-clear-btn" class="px-3 py-2 bg-white border rounded-md hover:bg-gray-50">
            Clear
          </button>
          <button id="chapter-apply-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Apply
          </button>
        </footer>
      </div>
    </div>

    {{-- Rule Question Picker Modal --}}
    <div id="question-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-2/3 max-h-[90vh] flex flex-col">
            <header class="p-4 border-b flex justify-between items-center flex-shrink-0">
                <h3 class="text-lg font-medium">Select Questions</h3>
                <button id="close-modal-btn" class="text-2xl">&times;</button>
            </header>

            {{-- responsive: stack on mobile --}}
            <main class="flex-grow flex flex-col md:flex-row overflow-hidden">
                {{-- Left Column: Chapter Filter (syncs with global selection) --}}
                <div class="w-full md:w-1/3 border-r overflow-y-auto p-4">
                    <h4 class="font-semibold mb-2">Filter by Chapter</h4>
                    <div class="space-y-2">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="chapter-filter-checkbox rounded" value="all" checked>
                                <span class="ml-2 font-medium">All Chapters</span>
                            </label>
                        </div>
                        @foreach($chapters as $chapter)
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="chapter-filter-checkbox rounded" value="{{ $chapter->id }}">
                                    <span class="ml-2">{{ $chapter->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column: Question List --}}
                <div class="w-full md:w-2/3 overflow-y-auto p-6 space-y-4" id="modal-question-list">
                    {{-- Questions will be loaded here by JavaScript --}}
                </div>
            </main>

            <footer class="p-4 border-t text-right flex-shrink-0">
                <button id="modal-done-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md">Done</button>
            </footer>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // ====== Common DOM refs ======
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Rule modal elements
        const modal               = document.getElementById('question-modal');
        const openModalButtons    = document.querySelectorAll('.open-modal-btn');
        const closeModalBtn       = document.getElementById('close-modal-btn');
        const modalDoneBtn        = document.getElementById('modal-done-btn');
        const modalQuestionList   = document.getElementById('modal-question-list');

        // Global chapter modal elements
        const chapterModal          = document.getElementById('chapter-modal');
        const openChapterModalBtn   = document.getElementById('open-chapter-modal-btn');
        const closeChapterModalBtn  = document.getElementById('close-chapter-modal-btn');
        const chapterApplyBtn       = document.getElementById('chapter-apply-btn');
        const chapterClearBtn       = document.getElementById('chapter-clear-btn');
        const chapterSelectAll      = document.getElementById('chapter-select-all');
        const chapterSearchInput    = document.getElementById('chapter-search');
        const chapterChoices        = document.querySelectorAll('.chapter-choice');
        const selectedChaptersInputs= document.getElementById('selected-chapters-inputs');
        const chapterSummaryEl      = document.getElementById('chapter-summary');

        let currentRuleId = null;

        // ====== Global Chapter Selection State & Helpers ======
        // Empty array => All chapters
        let selectedChapterIds = [];

        function renderChapterSummary() {
          if (!chapterSummaryEl) return;
          const label = selectedChapterIds.length === 0
              ? 'All chapters'
              : `${selectedChapterIds.length} chapter${selectedChapterIds.length > 1 ? 's' : ''} selected`;
          chapterSummaryEl.textContent = label;
          chapterSummaryEl.classList.remove('hidden');
        }

        function syncHiddenInputsForAutoFill() {
          selectedChaptersInputs.innerHTML = '';
          if (selectedChapterIds.length === 0) return; // All -> send nothing
          selectedChaptersInputs.innerHTML = selectedChapterIds
            .map(id => `<input type="hidden" name="chapters[]" value="${id}">`)
            .join('');
        }

        function openChapterModal() {
          // Reflect current selection in checkboxes
          if (selectedChapterIds.length === 0) {
            chapterSelectAll.checked = true;
            chapterChoices.forEach(cb => cb.checked = false);
          } else {
            chapterSelectAll.checked = false;
            const set = new Set(selectedChapterIds.map(String));
            chapterChoices.forEach(cb => cb.checked = set.has(cb.value));
          }
          chapterModal.classList.remove('hidden');
        }

        function closeChapterModal() {
          chapterModal.classList.add('hidden');
        }

        function applyChapterFiltersToRuleModal() {
          const allCb = document.querySelector('.chapter-filter-checkbox[value="all"]');
          const itemCbs = document.querySelectorAll('.chapter-filter-checkbox:not([value="all"])');

          if (selectedChapterIds.length === 0) {
            if (allCb) allCb.checked = true;
            itemCbs.forEach(cb => cb.checked = false);
          } else {
            if (allCb) allCb.checked = false;
            const set = new Set(selectedChapterIds.map(String));
            itemCbs.forEach(cb => cb.checked = set.has(cb.value));
          }
        }

        // Search filter in chapter modal
        chapterSearchInput?.addEventListener('input', function() {
          const q = this.value.toLowerCase();
          document.querySelectorAll('#chapter-list label').forEach(lbl => {
            const txt = lbl.innerText.toLowerCase();
            lbl.style.display = txt.includes(q) ? '' : 'none';
          });
        });

        // Select all toggle
        chapterSelectAll?.addEventListener('change', function() {
          const allOn = this.checked;
          // When "Select all" is checked, individual boxes are visually unchecked (meaning: we will treat it as "All")
          chapterChoices.forEach(cb => cb.checked = false);
        });

        // Clear -> All
        chapterClearBtn?.addEventListener('click', function() {
          selectedChapterIds = [];
          chapterSelectAll.checked = true;
          chapterChoices.forEach(cb => cb.checked = false);
          renderChapterSummary();
          syncHiddenInputsForAutoFill();
          closeChapterModal();
        });

        // Apply -> gather selections; if none => All
        chapterApplyBtn?.addEventListener('click', function() {
          const picked = [];
          chapterChoices.forEach(cb => { if (cb.checked) picked.push(parseInt(cb.value)); });
          selectedChapterIds = picked.length ? picked : [];
          renderChapterSummary();
          syncHiddenInputsForAutoFill();
          closeChapterModal();
        });

        // Open/Close chapter modal
        openChapterModalBtn?.addEventListener('click', openChapterModal);
        closeChapterModalBtn?.addEventListener('click', closeChapterModal);

        // Initial summary on load
        renderChapterSummary();
        syncHiddenInputsForAutoFill();

        // ====== Rule modal & data fetch ======
        function updateAllRuleCounts() {
          fetch(`{{ url('/institute/api/papers/'.$paper->id.'/stats') }}`)
            .then(r => r.json())
            .then(list => {
              list.forEach(({ rule_id, cnt }) => {
                const el = document.getElementById(`rule-count-${rule_id}`);
                if (!el) return;
                const required = el.textContent.split('/')[1].trim();
                el.textContent = `${cnt} / ${required}`;
              });
            })
            .catch(() => { /* could log */ });
        }

        function getSelectedChapterIdsFromLeftPane() {
          const chapterCheckboxes = document.querySelectorAll('.chapter-filter-checkbox:checked');
          return Array.from(chapterCheckboxes)
                      .map(cb => cb.value)
                      .filter(v => v !== 'all');
        }

        function fetchAndDisplayQuestions() {
          if (!currentRuleId) return;
          modalQuestionList.innerHTML = '<p>Loading questions...</p>';

          const chapterIds = getSelectedChapterIdsFromLeftPane();
          const params = new URLSearchParams();
          chapterIds.forEach(id => params.append('chapters[]', id));
          const apiUrl = `{{ url('/institute/api/papers/'.$paper->id.'/questions-for-rule') }}/${currentRuleId}?${params.toString()}`;

          fetch(apiUrl, { headers: { 'Accept': 'application/json' }})
            .then(r => r.json())
            .then(data => {
              modalQuestionList.innerHTML = '';
              if (!data.available_questions || data.available_questions.length === 0) {
                modalQuestionList.innerHTML = '<p>No questions found for the selected chapters and rule.</p>';
                return;
              }

              data.available_questions.forEach(q => {
                const isChecked = (data.selected_ids || []).includes(q.id);
                const div = document.createElement('div');
                div.className = 'p-3 border rounded-md';
                div.innerHTML = `
                  <label class="flex items-start space-x-3">
                    <input type="checkbox" class="modal-checkbox mt-1" value="${q.id}" ${isChecked ? 'checked' : ''}>
                    <div class="flex-1">
                      <div class="font-semibold">${q.question_text}</div>
                    </div>
                  </label>
                `;
                modalQuestionList.appendChild(div);
              });

              if (window.MathJax) window.MathJax.typesetPromise();
            });
        }

        function openModal(ruleId) {
          currentRuleId = ruleId;
          modal.classList.remove('hidden');

          // Use global selection by default
          applyChapterFiltersToRuleModal();

          fetchAndDisplayQuestions();
        }

        function closeModal() {
          modal.classList.add('hidden');
          updateAllRuleCounts();
        }

        // Left pane "All" toggle
        document.querySelector('.chapter-filter-checkbox[value="all"]')?.addEventListener('change', function() {
          if (this.checked) {
            document.querySelectorAll('.chapter-filter-checkbox:not([value="all"])').forEach(cb => cb.checked = false);
          }
          fetchAndDisplayQuestions();
        });

        // Left pane individual chapter toggles
        document.querySelectorAll('.chapter-filter-checkbox:not([value="all"])').forEach(checkbox => {
          checkbox.addEventListener('change', function() {
            if (this.checked) {
              const allCb = document.querySelector('.chapter-filter-checkbox[value="all"]');
              if (allCb) allCb.checked = false;
            }
            fetchAndDisplayQuestions();
          });
        });

        // Wire rule modal buttons
        openModalButtons.forEach(btn => btn.addEventListener('click', () => openModal(btn.dataset.ruleId)));
        closeModalBtn?.addEventListener('click', closeModal);
        modalDoneBtn?.addEventListener('click', closeModal);

        // Handle attach/detach in right pane
        modalQuestionList.addEventListener('change', function(e) {
          if (!e.target.classList.contains('modal-checkbox')) return;

          const questionId = e.target.value;
          const isChecked  = e.target.checked;

          const attachUrl = "{{ route('institute.papers.questions.attach', $paper) }}";
          const detachUrl = "{{ route('institute.papers.questions.detach', $paper) }}";
          const url = isChecked ? attachUrl : detachUrl;

          const payload = { question_id: questionId, rule_id: currentRuleId };

          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
          }).catch(err => console.error('Error attaching/detaching question:', err));
        });

        // First load
        updateAllRuleCounts();
      });
    </script>
</x-app-layout>
