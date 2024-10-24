@if (session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            showSuccessToast("{{ session('success') }}");
        });
    </script>
@endif
