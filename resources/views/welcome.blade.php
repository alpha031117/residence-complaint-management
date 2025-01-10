@extends('layouts.master_login')

@section('title', 'Residence Complaint Portal')

@section('content')
<div class="card m-3 shadow-lg" style="width: 100%; max-width: 450px;">
    <!-- Hero Image -->
    <img src="{{ asset('images/login-hero-gif.gif') }}" class="card-img-top" alt="hero-image" style="width: 100%; height: auto;">
    
    <!-- Card Body -->
    <div class="card-body">
        <h4 class="card-title text-center" style="color: #333; font-weight:bold;">Residence Complaint Portal</h4>
        <p class="card-subtitle mb-2 text-body-secondary text-center">Login Your Account</p>

        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Input -->
            <div class="mb-3">
                <label for="email" class="form-label" style="color: #333;">Email address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" style="border-radius: 10px;">
            </div>

            <!-- Password Input -->
            <div class="mb-3">
                <label for="password" class="form-label" style="color: #333;">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="********" style="border-radius: 10px;">
            </div>
            
            <!-- Remember Me -->
            <div class="block mt-2">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-2">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Don\'t have an account?') }} &nbsp;
                </div>
                <a class="text-sm text-gray-600 hover:text-blue-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('register') }}">
                        Sign Up Here!
                </a>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-2">
                <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px;">
                    Log In
                </button>
            </div>
        </form>
    </div>
</div>
@endsection