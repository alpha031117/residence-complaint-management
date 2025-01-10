@extends('layouts.master_login')

@section('title', 'Residence Complaint Portal')

@section('content')
<div class="card m-3 shadow-lg mt-5 mb-5" style="width: 100%; max-width: 450px;">
    <!-- Card Body -->
    <div class="card-body">
        <h3 class="text-center" style="color: #333; font-weight:bold;">Residence Complaint Portal</h3>
        <p class="card-subtitle mb-2 text-body-secondary text-center">Sign Up Your Account</p>

        <!-- Email Input -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <input id="name" class="block mt-1 w-full rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <input id="email" class="block mt-1 w-full rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />

                <input id="password" class="block mt-1 w-full rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <input id="password_confirmation" class="block mt-1 w-full rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="text-sm text-gray-600 hover:text-blue-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('login') }}">
                    Already registered?
                </a>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-2">
                <button class="btn btn-primary w-100" style="border-radius: 10px;">
                    Create An Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
