@if ($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach ($errors->all() as $error)
                showErrorToast("{{ $error }}");
            @endforeach
        });
    </script>
@endif
