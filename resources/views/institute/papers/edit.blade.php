<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Paper Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    
                    {{-- This is the smart button section --}}
                    <div class="mb-6 pb-6 border-b">
                        <h3 class="text-lg font-medium text-gray-900">Manage Questions</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            @if($blueprint)
                                This paper follows the '{{ $blueprint->name }}' blueprint. Click below to manage its questions.
                            @else
                                This paper does not have a blueprint. Click below to freely select questions.
                            @endif
                        </p>
                        <div class="mt-4">
                            @if($blueprint)
                                <a href="{{ route('institute.papers.fulfill_blueprint', $paper) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Edit Questions (Blueprint Mode)
                                </a>
                            @else
                                <a href="{{ route('institute.papers.questions.select', $paper) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Edit Questions (Free-form Mode)
                                </a>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('institute.papers.update', $paper) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Paper Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $paper->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="board_id" class="block text-sm font-medium text-gray-700">Board</label>
                                <select name="board_id" id="board_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach($boards as $board)
                                        <option value="{{ $board->id }}" @selected($paper->board_id == $board->id)>{{ $board->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                                <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected($paper->class_id == $class->id)>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                            <select name="subject_id" id="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected($paper->subject_id == $subject->id)>{{ $subject->name }} ({{ $subject->academicClass->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700">Total Marks</label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $paper->total_marks) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="time_allowed" class="block text-sm font-medium text-gray-700">Time Allowed (e.g., "3 Hrs", "90 Mins")</label>
                                <input type="text" name="time_allowed" id="time_allowed" value="{{ old('time_allowed', $paper->time_allowed) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="exam_date" class="block text-sm font-medium text-gray-700">Exam Date</label>
                                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', \Carbon\Carbon::parse($paper->exam_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700">Instructions (Optional)</label>
                            <textarea name="instructions" id="instructions" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('instructions', $paper->instructions) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Paper Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
