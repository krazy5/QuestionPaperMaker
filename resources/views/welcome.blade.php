<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Question Paper Maker - For Maharashtra Board, CBSE, & More</title>
    <meta name="description" content="India's first AI-powered question paper maker for academic boards like Maharashtra State Board (HSC & SSC), CBSE, and more. Go beyond JEE & NEET preparation.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .testimonial-slider { position: relative; }
        .testimonial-slide { display: none; }
        .testimonial-slide.active { display: block; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <header class="sticky top-0 left-0 w-full z-50 p-4 bg-gray-900/70 backdrop-blur-sm shadow-md transition-all">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-white">
                AI <span class="text-indigo-400">QPMaker</span>
            </a>
            <nav class="flex items-center space-x-2">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white rounded-md hover:bg-white/10">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-transparent rounded-md shadow-sm hover:bg-gray-200">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main>
        <section class="relative h-screen flex items-center justify-center text-center overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/images/hero-1.jpg');"></div>
            <div class="absolute inset-0 bg-black/70"></div>
            
            <div class="relative z-10 px-4" x-data>
                <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-4">
                    The Future of Exam Creation is Here.
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Our AI intelligently drafts, formats, and generates complete, blueprint-accurate question papers in seconds. Save time, eliminate errors, and focus on what truly matters.
                </p>
                <a href="{{ route('register') }}" class="px-8 py-4 text-lg font-semibold text-white bg-indigo-600 rounded-lg shadow-lg hover:bg-indigo-700 transition-transform transform hover:scale-105">
                    Create Your First Paper with AI
                </a>
            </div>
        </section>

        <section id="how-it-works" class="py-20 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">Generate Papers in 3 Simple Steps</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-12">An effortless workflow powered by intelligent automation.</p>
                <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-16">
                    <div class="flex flex-col items-center">
                        <div class="flex items-center justify-center w-20 h-20 bg-indigo-100 dark:bg-indigo-900/50 rounded-full text-indigo-500 text-3xl font-bold mb-4">1</div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Select Your Blueprint</h3>
                        <p class="text-gray-600 dark:text-gray-400">Choose from official board patterns like Maharashtra HSC/SSC or define your own custom exam rules.</p>
                    </div>
                     <div class="flex flex-col items-center">
                        <div class="flex items-center justify-center w-20 h-20 bg-indigo-100 dark:bg-indigo-900/50 rounded-full text-indigo-500 text-3xl font-bold mb-4">2</div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Choose Chapters</h3>
                        <p class="text-gray-600 dark:text-gray-400">Simply pick the chapters you want to include. Our AI will handle the question distribution based on the blueprint.</p>
                    </div>
                     <div class="flex flex-col items-center">
                        <div class="flex items-center justify-center w-20 h-20 bg-indigo-100 dark:bg-indigo-900/50 rounded-full text-indigo-500 text-3xl font-bold mb-4">3</div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Generate with AI</h3>
                        <p class="text-gray-600 dark:text-gray-400">Click a button and let our AI generate a perfectly formatted, print-ready question paper in moments.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="for-india" class="py-20 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">India's First Platform for Academic Boards</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-12">Go beyond JEE & NEET. We provide unparalleled support for the syllabi that matter most.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-indigo-500 mb-3">Maharashtra State Board</h3>
                        <p class="text-gray-600 dark:text-gray-400">Deeply integrated blueprints and question banks for Class 10 (SSC) and Class 12 (HSC) Science, Commerce, and Arts streams.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-indigo-500 mb-3">Expanding Board Support</h3>
                        <p class="text-gray-600 dark:text-gray-400">We are continuously adding support for other major boards like CBSE and ICSE, for classes 9 through 12.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold text-indigo-500 mb-3">Coaching Institutes & Tutors</h3>
                        <p class="text-gray-600 dark:text-gray-400">Create unlimited chapter-wise tests, unit tests, and full-length mock papers with your own branding.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="developer" class="py-20 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-6 flex flex-col md:flex-row items-center text-center md:text-left gap-12">
                <div class="md:w-1/4">
                    <img src="/images/mohsin-khan.jpg" alt="Developer Mohsin Khan" class="w-48 h-48 rounded-full mx-auto border-4 border-indigo-500 object-cover shadow-lg">
                </div>
                <div class="md:w-3/4">
                    <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">Meet the Developer</h2>
                    <h3 class="text-2xl font-semibold text-indigo-500 dark:text-indigo-400 mb-4">Mohsin Khan</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed max-w-2xl mx-auto md:mx-0">
                        As a passionate developer with a deep interest in educational technology, I created AI QPMaker to solve a real-world problem for educators. My goal is to leverage artificial intelligence to simplify complex tasks, allowing teachers to dedicate more time to what truly matters: teaching and inspiring students.
                    </p>
                </div>
            </div>
        </section>

        <section id="testimonials" class="py-20 bg-gray-50 dark:bg-gray-900 overflow-hidden">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">Loved by Leading Institutes & Educators</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-12">Our AI is transforming exam preparation across Maharashtra.</p>
                
                <div x-data="testimonialSlider()" x-init="startSlider()" class="relative max-w-3xl mx-auto">
                    <div class="relative h-48">
                        <template x-for="(testimonial, index) in testimonials" :key="index">
                            <div x-show="active === index" 
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 transform translate-x-12"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100 transform translate-x-0"
                                 x-transition:leave-end="opacity-0 transform -translate-x-12"
                                 class="absolute w-full">
                                <p class="text-lg italic text-gray-600 dark:text-gray-300" x-text="testimonial.quote"></p>
                                <div class="mt-4">
                                    <div class="font-bold text-gray-800 dark:text-white" x-text="testimonial.name"></div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400" x-text="testimonial.title"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button @click="prev()" class="absolute top-1/2 -translate-y-1/2 left-0 md:-left-16 p-2 rounded-full bg-white/50 hover:bg-white dark:bg-gray-800/50 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="next()" class="absolute top-1/2 -translate-y-1/2 right-0 md:-right-16 p-2 rounded-full bg-white/50 hover:bg-white dark:bg-gray-800/50 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </section>
        
        <script>
            function testimonialSlider() {
                return {
                    active: 0,
                    interval: null,
                    testimonials: [
                        { quote: "This AI is a game-changer. I prepared three sets of prelim papers for my Class 12 Physics batch in 30 minutes. A task that used to take a whole weekend.", name: "Prof. S. R. Patil", title: "HOD Physics, Chate Coaching Classes" },
                        { quote: "Finally, a tool that understands the Maharashtra State Board pattern perfectly. The generated papers are flawless. Highly recommended.", name: "Mrs. Vidya Joshi", title: "Principal, Jnana Prabodhini School" },
                        { quote: "As an institute owner, efficiency is key. AI QPMaker has saved my faculty hundreds of hours, allowing them to focus more on student interaction.", name: "Aarav Mehta", title: "Director, Success Academy" },
                        { quote: "The best part is the flexibility. I can create a short 10-mark chapter test or a full 70-mark paper with the same ease. The AI handles all the complex rules.", name: "Sneha Kulkarni", title: "Biology Tutor, Pune" },
                        { quote: "Our students find the papers generated by this tool to be of high quality and perfectly aligned with the board's difficulty level.", name: "Ravi Deshmukh", title: "Chemistry Faculty, IITians Hub" },
                        { quote: "I was skeptical about AI, but this is truly intelligent. It correctly balances question types and difficulty. It's like having a seasoned paper-setter as an assistant.", name: "Dr. Alok Verma", title: "Mathematics Author & Educator" },
                        { quote: "A must-have for every modern educational institution in India. It's not just for science, we use it for commerce subjects too!", name: "Sunita Agarwal", title: "Trustee, Podar Group of Schools" },
                        { quote: "The print-ready PDF output is clean, professional, and saves our DTP operator a lot of time. The branding feature is a great touch.", name: "Imran Sheikh", title: "Operations Head, Ideal Classes" },
                        { quote: "We use AI QPMaker for our MHT-CET mock tests. The question selection is diverse and covers the entire syllabus as required.", name: "Prakash Shinde", title: "CET Coordinator, Vidyalankar Classes" },
                        { quote: "I can't imagine going back to the old manual way. This tool has brought our examination process into the 21st century. Thank you, Mohsin!", name: "Nisha Gupta", title: "Academic Dean, Ryan International" },
                    ],
                    next() { this.active = (this.active + 1) % this.testimonials.length; },
                    prev() { this.active = (this.active - 1 + this.testimonials.length) % this.testimonials.length; },
                    startSlider() { this.interval = setInterval(() => { this.next() }, 5000); },
                    stopSlider() { clearInterval(this.interval); }
                }
            }
        </script>
    </main>
    
    <footer class="bg-gray-800 w-full py-6">
        <div class="container mx-auto text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} AI Question Paper Maker. All rights reserved.</p>
            <p class="mt-1">Developed with ❤️ by Mohsin Khan in Mira Bhayandar</p>
        </div>
    </footer>
</body>
</html>