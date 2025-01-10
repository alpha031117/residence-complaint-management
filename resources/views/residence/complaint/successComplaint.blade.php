@extends('layouts.app')

@section('title', 'Complaint Success')

@section('header')
    @include('layouts.navigation')
    @section('page-styles')
        <style>
            /* Full-height layout using flexbox */
            .page-wrapper {
                display: flex;
                flex-direction: column;
                min-height: 60vh;
            }

            .container {
                text-align: center;
                padding: 40px;
                flex: 2; /* Take up remaining space to push footer down */
                margin-top: 50px;
            }

            .success-icon {
                width: 80px;
                height: 80px;
                background: #c8f7c5;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0 auto 20px;
            }

            .success-icon svg {
                width: 40px;
                height: 40px;
            }

            h1 {
                font-size: 24px;
                margin-bottom: 10px;
                color: #333;
            }

            p {
                font-size: 16px;
                margin-bottom: 20px;
                color: #666;
            }

        </style>
    @endsection
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="container">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <circle cx="8" cy="8" r="8" fill="#c8f7c5" /> <!-- Light green background -->
                    <path d="M6.354 11.354l6-6-.708-.708-5.646 5.647L3.354 7.354l-.708.708 3 3a.5.5 0 0 0 .708 0z" fill="#22C55E"
                    stroke="#22C55E" stroke-width="1" /> <!-- Dark green checkmark -->
                </svg>
            </div>
            <h1>We have received your complaint.</h1>
            <p>We will take around 1 to 3 days for complaint's investigation. Thank you for your contribution.</p>
            <a href="{{ route('dashboard')}} ">
                <button class="btn btn-primary mt-3">
                    Back to Home
                </button>
            </a>
        </div>
    </div>
@endsection

@section('page-scripts')
@endsection
