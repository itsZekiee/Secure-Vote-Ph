@extends('voter.layouts.app')

@section('title', 'Voter Registration')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo/Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-indigo-600">Secure Vote PH</h1>
                <p class="mt-2 text-sm text-gray-600">Register or sign in to participate in elections</p>
            </div>

            <!-- Tab Navigation -->
            <div x-data="{ activeTab: 'signin' }" class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex border-b border-gray-200">
                    <button @click="activeTab = 'signin'"
                            :class="activeTab === 'signin' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-4 px-4 text-center font-medium text-sm border-b-2 transition duration-150">
                        Sign In
                    </button>
                    <button @click="activeTab = 'signup'"
                            :class="activeTab === 'signup' ? 'border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex-1 py-4 px-4 text-center font-medium text-sm border-b-2 transition duration-150">
                        Sign Up
                    </button>
                </div>

                <!-- Sign In Form -->
                <div x-show="activeTab === 'signin'" x-cloak class="p-6">
                    <form method="POST" action="{{ route('voter.login') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="login_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input type="email" name="email" id="login_email" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('email') }}">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="login_password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" id="login_password" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" name="remember" id="remember"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                            </div>

                            <button type="submit"
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Sign In
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Sign Up Form -->
                <div x-show="activeTab === 'signup'" x-cloak class="p-6">
                    <form method="POST" action="{{ route('voter.register') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="name" id="name" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('name') }}">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input type="email" name="email" id="email" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('email') }}">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="student_id" class="block text-sm font-medium text-gray-700">Student/Voter ID</label>
                                <input type="text" name="student_id" id="student_id" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('student_id') }}" placeholder="Enter your ID number">
                                @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="election_code" class="block text-sm font-medium text-gray-700">Election Code</label>
                                <input type="text" name="election_code" id="election_code" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('election_code') }}" placeholder="Enter the election code provided to you">
                                @error('election_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" id="password" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="terms" id="terms" required
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="terms" class="ml-2 block text-sm text-gray-900">
                                    I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms and Conditions</a>
                                </label>
                            </div>

                            <button type="submit"
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
