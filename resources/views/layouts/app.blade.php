<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Residence Complaint Portal')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Bootstrap 5.3 --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

        <!-- Axios -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            {{-- Fallback Tailwind CSS reset and utility styles from previous login page --}}
            <style>
                /* Tailwind CSS reset and utility styles */
                {{ file_get_contents(public_path('path/to/tailwind/styles.css')) }}
            </style>
        @endif

        @yield('head-scripts')
        @yield('head-styles')

        {{-- Additional Styles --}}
        @yield('page-styles')
    </head>
    <body class="font-sans antialiased @yield('body-class')">
        @section('background')
        <style>
            body {
                background-image: url('{{ asset('images/login-background.jpg') }}');
                background-size: cover;  /* Make the image cover the entire screen */
                background-position: center center; /* Center the image */
                background-repeat: no-repeat; /* Ensure the image doesn't repeat */
                background-attachment: fixed;  /* Fix the background so it stays in place while scrolling */
                height: 100%;  /* Make sure the body takes up the full height */
                margin: 0;  /* Remove any default margin from the body */
            }
        </style>
        @show

        @yield('header')
        
        {{-- Notifications/Alerts --}}
        {{-- <div class="container mt-3">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div> --}}

        {{-- Main Content --}}
        <main class="@yield('main-class', 'd-flex justify-content-center align-items-center')">
            @yield('content')
        </main>

        {{-- Footer --}}
        @section('footer')
        <footer class="mt-3 text-center bg-transparent">
            <hr/>
            <p class="text-muted">
                &copy; {{ date('Y') }} Residence Complaint Portal. All Rights Reserved.
            </p>
        </footer>
        @show

        <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>
        
        {{-- Additional Scripts --}}
        @yield('page-scripts')
    </body>
</html>