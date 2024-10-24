<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cisco')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">

    <!-- sweet alert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sweetalert.js') }}?v=1.0"></script>

    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/Toastify.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.11.2/Toastify.min.js"></script>

    <!-- Include Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/axios.js') }}?v=1.0"></script>

    <!-- Toastify JS Algo -->
    <script src="{{ asset('js/toast.js') }}?v=1.0"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>

    <!-- Include Navbar -->
    @include('partials.navbar')

    <!-- Main Content -->
    <div class="d-flex flex-column min-vh-100">
        @yield('content')

        <!-- Include Footer -->
        @include('partials.footer')

        <!-- Terms and Condition -->
        <script src="{{ asset('js/terms.js') }}"></script>
    </div>

    <!-- Success Notification -->
    @include('partials.validation-success')

    <!-- Error Notifications -->
    @include('partials.validation-errors')

    <!-- Bootstrap JS (Optional for interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>

</body>
</html>
