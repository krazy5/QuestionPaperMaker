<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Blueprint: {{ $blueprint->name }}
            </h2>
            <a href="{{ route('institute.blueprints.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                &larr; Back to Blueprints
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Blueprint Details Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b">
                    <h3 class="text-lg font-medium mb-4">Blueprint Summary</h3>
                    {{-- ... your summary grid from before ... --}}
                </div>
            </div>

            {{-- Loop Through Existing Sections and Rules --}}
            @forelse ($blueprint->sections as $section)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium">{{ $section->name }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $section->instructions }}</p>
                        
                        {{-- Table of Rules --}}
                        <table class="min-w-full divide-y divide-gray-200 mt-4">
                            <thead class="bg-gray-50 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Type</th>
                                    <th class="px-4 py-2 text-left">Marks Each</th>
                                    <th class="px-4 py-2 text-left">Questions to Select</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                @foreach ($section->rules as $rule)
                                    <tr>
                                        <td class="px-4 py-2">{{ $rule->question_type }}</td>
                                        <td class="px-4 py-2">{{ $rule->marks_per_question }}</td>
                                        <td class="px-4 py-2">{{ $rule->number_of_questions_to_select }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Form to Add a New Rule to This Section --}}
                        <form action="{{ route('institute.blueprints.sections.rules.store', $section) }}" method="POST" class="mt-4 p-4 border-t space-y-4">
                            @csrf
                            <h4 class="font-semibold">Add New Rule</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- Rule fields --}}
                                <div>
                                    <label for="question_type_{{ $section->id }}" class="text-sm">Question Type</label>
                                    <select name="question_type" id="question_type_{{ $section->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="mcq">MCQ</option>
                                        <option value="short">Short Answer</option>
                                        <option value="long">Long Answer</option>
                                        <option value="true_false">True/False</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="marks_per_question_{{ $section->id }}" class="text-sm">Marks per Question</label>
                                    <input type="number" name="marks_per_question" id="marks_per_question_{{ $section->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="1">
                                </div>
                                <div>
                                    <label for="num_questions_{{ $section->id }}" class="text-sm">Number of Questions</label>
                                    <input type="number" name="number_of_questions_to_select" id="num_questions_{{ $section->id }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" min="1">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Add Rule</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                {{-- This message shows if no sections exist yet --}}
            @endforelse

            {{-- Form to Add a New Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                 <div class="p-6 text-gray-900">
                    <form action="{{ route('institute.blueprints.sections.store', $blueprint) }}" method="POST" class="space-y-4">
                        @csrf
                        <h3 class="text-lg font-medium">Add New Section</h3>
                        <div>
                            <label for="section_name" class="block text-sm font-medium text-gray-700">Section Name</label>
                            <input type="text" name="name" id="section_name" placeholder="e.g., Section A: Multiple Choice" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label for="section_instructions" class="block text-sm font-medium text-gray-700">Instructions (Optional)</label>
                            <textarea name="instructions" id="section_instructions" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Add Section</button>
                        </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>
</x-app-layout>