<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Activity Tracker') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-amber-600 to-amber-800 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Activity Tracker</h1>
            <p class="text-gray-500 mb-6">Daily activity tracking for support teams</p>
            <div class="flex items-center justify-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-gradient-to-r from-amber-600 to-amber-800 text-white font-medium rounded-xl hover:from-amber-700 hover:to-amber-900 transition-all shadow-md">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-gradient-to-r from-amber-600 to-amber-800 text-white font-medium rounded-xl hover:from-amber-700 hover:to-amber-900 transition-all shadow-md">
                        Log In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-6 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-200 hover:bg-gray-50 transition-all shadow-sm">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </body>
</html>
