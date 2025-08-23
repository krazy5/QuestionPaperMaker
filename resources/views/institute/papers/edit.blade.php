<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Edit Paper Details') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Smart button section --}}
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                    <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold">Manage Questions</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @if($blueprint)
                                This paper follows the '<span class="font-medium">{{ $blueprint->name }}</span>' blueprint. Click below to manage its questions.
                            @else
                                This paper does not have a blueprint. Click below to freely select questions.
                            @endif
                        </p>
                        <div class="mt-4">
                            @if($blueprint)
                                <a href="{{ route('institute.papers.fulfill_blueprint', $paper) }}"
                                   class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                    Edit Questions (Blueprint Mode)
                                </a>
                            @else
                                <a href="{{ route('institute.papers.questions.select', $paper) }}"
                                   class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                    Edit Questions (Free-form Mode)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit form --}}
            <div class="bg-white dark:bg-gray-900/60 backdrop-blur overflow-hidden shadow-sm sm:rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('institute.papers.update', $paper) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paper Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $paper->title) }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="board_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Board</label>
                                <select name="board_id" id="board_id" required
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($boards as $board)
                                        <option value="{{ $board->id }}" @selected($paper->board_id == $board->id)>{{ $board->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                                <select name="class_id" id="class_id" required
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected($paper->class_id == $class->id)>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                            <select name="subject_id" id="subject_id" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected($paper->subject_id == $subject->id)>{{ $subject->name }} ({{ $subject->academicClass->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Marks</label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $paper->total_marks) }}" required
                                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="time_allowed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Allowed (e.g., "3 Hrs", "90 Mins")</label>
                                <input type="text" name="time_allowed" id="time_allowed" value="{{ old('time_allowed', $paper->time_allowed) }}" required
                                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="exam_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exam Date</label>
                                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', \Carbon\Carbon::parse($paper->exam_date)->format('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instructions (Optional)</label>
                            <textarea name="instructions" id="instructions" rows="5"
                                      class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('instructions', $paper->instructions) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                                Update Paper Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
