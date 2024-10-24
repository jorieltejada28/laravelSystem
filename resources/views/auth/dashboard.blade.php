@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="container mt-5">
        <h1 class="text-center">Dashboard</h1>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Welcome</div>
                    <div class="card-body">
                        <h5 class="card-title">Hello, {{ auth()->user()->name }}!</h5>
                        <p class="card-text">This is your dashboard. You can manage your account here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show the modal automatically when the page loads if terms are pending
        window.onload = () => {
            @if(auth()->user()->terms == 'pending')
                showTermsAndConditions();
            @endif
        };
    </script>

@endsection
