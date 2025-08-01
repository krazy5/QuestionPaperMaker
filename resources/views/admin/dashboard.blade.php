    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <h3 class="text-lg font-medium text-gray-900">Application Stats</h3>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <div class="p-6 bg-gray-100 rounded-lg">
                                <div class="text-2xl font-bold">{{ $stats['institutes'] }}</div>
                                <p class="text-sm text-gray-600">Institutes Registered</p>
                            </div>
                            <div class="p-6 bg-gray-100 rounded-lg">
                                <div class="text-2xl font-bold">{{ $stats['questions'] }}</div>
                                <p class="text-sm text-gray-600">Questions in Bank</p>
                            </div>
                            <div class="p-6 bg-gray-100 rounded-lg">
                                <div class="text-2xl font-bold">{{ $stats['papers'] }}</div>
                                <p class="text-sm text-gray-600">Papers Generated</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Quick Links</h3>
                            <div class="mt-4 flex space-x-4">
                                <a href="{{ route('admin.questions.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Manage Questions</a>
                                <a href="{{ route('admin.subjects.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Manage Subjects</a>
                                <a href="{{ route('admin.chapters.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Manage Chapters</a>
                                <a href="{{ route('admin.institutes.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-green-700">Manage Institutes</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>