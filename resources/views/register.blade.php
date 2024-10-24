@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center">
                    <h4>Register</h4>
                </div>
                <div class="card-body">
                    <!-- Display Validation Errors -->
                    @include('partials.validation-errors')

                    <!-- Display Success Message -->
                    @include('partials.validation-success')

                    <form action="{{ route('register_post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small>Already have an account? <a href="{{ route('login') }}">Login here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
