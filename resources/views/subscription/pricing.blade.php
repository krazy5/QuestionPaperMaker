<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Our Plans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-12 text-gray-900">
                     {{-- Display Error/Success Messages --}}
                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded text-center">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($activeSubscription)
                        <div class="mb-6 p-4 bg-blue-100 text-blue-800 rounded text-center">
                            You are currently on the <strong>{{ $activeSubscription->plan_name }}</strong> plan.
                        </div>
                    @endif
                    <div class="text-center mb-12">
                        <h1 class="text-4xl font-bold">Choose the Plan That's Right For You</h1>
                        <p class="mt-4 text-lg text-gray-600">Simple, transparent pricing for institutes of all sizes.</p>
                    </div>

                    {{-- Pricing Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        {{-- Basic Plan --}}
                        <div class="border rounded-lg p-8 flex flex-col">
                            <h3 class="text-2xl font-semibold">Basic</h3>
                            <p class="mt-4 text-4xl font-bold">₹ 1,199 <span class="text-xl font-medium text-gray-500">/ mo</span></p>
                            <ul class="mt-6 space-y-4 text-gray-600 flex-grow">
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Single User Login</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>10 Papers / Month</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Access to Question Bank</li>
                            </ul>
                             @if($activeSubscription && $activeSubscription->plan_name == 'Basic')
                                <button class="w-full mt-8 py-3 px-6 bg-gray-300 text-gray-500 rounded-md font-semibold cursor-not-allowed" disabled>Current Plan</button>
                            @else
                                <form action="{{ route('subscription.subscribe') }}" method="POST" class="mt-8">
                                    @csrf
                                    <input type="hidden" name="plan" value="Basic">
                                    <button type="submit" class="w-full py-3 px-6 bg-gray-500 text-white rounded-md font-semibold">Choose Plan</button>
                                </form>
                            @endif
                        </div>

                        {{-- Professional Plan --}}
                        <div class="border-2 border-blue-600 rounded-lg p-8 flex flex-col relative">
                            <h3 class="text-2xl font-semibold">Professional</h3>
                            <p class="mt-4 text-4xl font-bold">₹ 2,499 <span class="text-xl font-medium text-gray-500">/ mo</span></p>
                            <ul class="mt-6 space-y-4 text-gray-600 flex-grow">
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>5 User Logins</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Unlimited Papers</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Add Your Own Questions</li>
                            </ul>
                            @if($activeSubscription && $activeSubscription->plan_name == 'Professional')
                                <button class="w-full mt-8 py-3 px-6 bg-gray-300 text-gray-500 rounded-md font-semibold cursor-not-allowed" disabled>Current Plan</button>
                            @else
                                <form action="{{ route('subscription.subscribe') }}" method="POST" class="mt-8">
                                    @csrf
                                    <input type="hidden" name="plan" value="Professional">
                                    <button type="submit" class="w-full py-3 px-6 bg-blue-500 text-white rounded-md font-semibold">
                                        @if($activeSubscription && $activeSubscription->plan_name == 'Basic')
                                            Upgrade to Professional
                                        @else
                                            Choose Plan
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </div>

                        {{-- Enterprise Plan --}}
                        <div class="border rounded-lg p-8 flex flex-col">
                            <h3 class="text-2xl font-semibold">Enterprise</h3>
                            <p class="mt-4 text-4xl font-bold">Contact Us</p>
                            <ul class="mt-6 space-y-4 text-gray-600 flex-grow">
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Unlimited Logins</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Custom Branding</li>
                                <li class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Dedicated Support</li>
                            </ul>
                            <a href="#" class="w-full mt-8 py-3 px-6 bg-gray-500 text-white rounded-md font-semibold text-center">Contact Sales</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
