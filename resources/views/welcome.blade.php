<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SMB Invoicing') }} - Professional Invoice Management</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Navigation Header -->
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="shrink-0">
                    <h1 class="text-xl font-bold text-blue-600 dark:text-blue-500">
                        {{ config('app.name', 'SMB Invoicing') }}
                    </h1>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/admin') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 font-medium transition-colors">
                                Go to Admin Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 font-medium transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 lg:py-32 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 dark:text-gray-100 mb-6 tracking-tight">
                    Professional Invoice Management for Growing Businesses
                </h1>
                <p class="text-xl lg:text-2xl text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                    Self-hosted, modern invoicing solution with complete workflow management. From draft to payment tracking, handle everything in one place.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ url('/admin') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors shadow-lg hover:shadow-xl">
                        Go to Admin Panel
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 dark:border-indigo-500 dark:text-indigo-400 dark:hover:bg-indigo-950 px-8 py-4 rounded-lg font-semibold text-lg transition-colors">
                            Sign In
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Stats/Highlights Bar -->
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-center items-center gap-6 lg:gap-12">
                <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">Multi-Currency</span>
                </div>
                <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">PDF Generation</span>
                </div>
                <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">Email Automation</span>
                </div>
                <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span class="font-medium">Digital Signatures</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-12 lg:py-20 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Everything You Need to Manage Invoices
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Powerful features designed for small and medium businesses
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Feature 1: Invoice Management -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Invoice Management
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Create, track, and manage invoices with status tracking. Handle drafts, sent invoices, and payment confirmations effortlessly.
                    </p>
                </div>

                <!-- Feature 2: Multi-Currency -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Multi-Currency Support
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Handle international clients with ease. Support for multiple currencies ensures you can work with customers worldwide.
                    </p>
                </div>

                <!-- Feature 3: PDF & Email -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        PDF & Email
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Generate professional PDF invoices and send automated emails with customizable templates and placeholders.
                    </p>
                </div>

                <!-- Feature 4: Digital Signatures -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Digital Signatures
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Secure document signing with AuthorizeDoc integration. Add legally binding signatures to your invoices.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-12 lg:py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Why Choose SMB Invoicing
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Built with modern technology for modern businesses
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                <!-- Benefit 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                        Complete Workflow
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        From draft creation to payment tracking, manage your entire invoice lifecycle in one integrated system.
                    </p>
                </div>

                <!-- Benefit 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                        Self-Hosted
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Full control over your data with self-hosted deployment. Your invoices stay on your infrastructure.
                    </p>
                </div>

                <!-- Benefit 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                        Modern Stack
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Built with Laravel, Livewire, and Filament for a fast, reliable, and maintainable application.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-600 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'SMB Invoicing') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
