<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Blueprint: <span class="italic">{{ $blueprint->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Section 1: Add a New Section --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Add New Section</h2>
                        <p class="mt-1 text-sm text-gray-600">Add a new section like "Section A" or "Section B" to this blueprint.</p>
                    </header>

                    <form method="POST" action="{{ route('admin.blueprints.sections.store', $blueprint) }}" class="mt-6 space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="section_name" class="block text-sm font-medium text-gray-700">Section Name</label>
                                <input type="text" name="name" id="section_name" placeholder="e.g., Section A" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label for="section_instructions" class="block text-sm font-medium text-gray-700">Instructions (Optional)</label>
                                <input type="text" name="instructions" id="section_instructions" placeholder="e.g., Answer all questions." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">Add Section</button>
                        </div>
                    </form>
                </section>
            </div>

            {{-- Section 2: Existing Sections and Rules --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Blueprint Structure</h2>
                    </header>

                    <div class="mt-6 space-y-6">
                        @forelse ($blueprint->sections as $section)
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <h3 class="font-semibold text-gray-800">{{ $section->name }}</h3>
                                @if($section->instructions)
                                    <p class="text-sm text-gray-600 italic">"{{ $section->instructions }}"</p>
                                @endif
                                
                                {{-- Display Existing Rules --}}
                                <div class="mt-4 pl-4 border-l-2 border-gray-300 space-y-2">
                                    <h4 class="text-sm font-medium text-gray-600">Rules:</h4>
                                    @forelse($section->rules as $rule)
                                        <div class="flex items-center justify-between text-sm text-gray-800 p-2 bg-white rounded border">
                                            <span>
                                                - Select <strong>{{ $rule->number_of_questions_to_select }}</strong> 
                                                @if($rule->total_questions_to_display) out of <strong>{{ $rule->total_questions_to_display }}</strong> @endif
                                                <strong>{{ strtoupper($rule->question_type) }}</strong> questions, each worth <strong>{{ $rule->marks_per_question }}</strong> marks.
                                            </span>
                                            <form method="POST" action="{{ route('admin.blueprints.rules.destroy', $rule) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-semibold" onclick="return confirm('Are you sure you want to delete this rule?')">DELETE</button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">No rules defined for this section yet.</p>
                                    @endforelse
                                </div>

                                {{-- Add New Rule Form --}}
                                <div class="mt-4 pt-4 border-t">
                                    <h4 class="text-sm font-medium text-gray-600 mb-2">Add New Rule to {{ $section->name }}</h4>
                                    <form method="POST" action="{{ route('admin.blueprints.sections.rules.store', $section) }}">
                                        @csrf
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <select name="question_type" class="rounded-md border-gray-300 shadow-sm">
                                                <option value="mcq">MCQ</option>
                                                <option value="short">Short Answer</option>
                                                <option value="long">Long Answer</option>
                                                <option value="true_false">True/False</option>
                                            </select>
                                            <input type="number" name="marks_per_question" placeholder="Marks per Question" required class="rounded-md border-gray-300 shadow-sm">
                                            <input type="number" name="number_of_questions_to_select" placeholder="# to Select" required class="rounded-md border-gray-300 shadow-sm">
                                            <input type="number" name="total_questions_to_display" placeholder="# to Display (Optional)" class="rounded-md border-gray-300 shadow-sm">
                                        </div>
                                        <div class="flex justify-end mt-2">
                                            <button type="submit" class="px-3 py-1 bg-gray-500 text-white rounded-md text-sm hover:bg-gray-700">Add Rule</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">No sections have been added to this blueprint yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>

        </div>
    </div>
</x-app-layout>
