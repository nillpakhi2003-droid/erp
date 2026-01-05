@extends('layouts.guest')

@section('title', 'লগইন - ইআরপি সিস্টেম')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-xl p-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="mb-4">
                <svg class="mx-auto h-16 w-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">ইআরপি সিস্টেম</h2>
            <p class="text-sm text-gray-600 leading-relaxed">আপনার ব্যবসার সকল হিসাব নিকাশ রাখুন<br>নিরাপদ এবং সুরক্ষিত মাধ্যমে</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            
            <div class="mb-4">
                <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">ফোন নম্বর</label>
                <input type="text" 
                       name="phone" 
                       id="phone" 
                       value="{{ old('phone') }}"
                       placeholder="01711111111"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('phone') border-red-500 @enderror" 
                       required 
                       autofocus>
                @error('phone')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">পাসওয়ার্ড</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" 
                       required>
                @error('password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-blue-600">
                    <span class="ml-2 text-sm text-gray-700">আমাকে মনে রাখুন</span>
                </label>
            </div>

            <div class="flex items-center justify-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    লগইন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
