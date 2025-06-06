<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin {{ $title ? config('app.name') . ' / ' . $title : config('app.name') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect">
        <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>

    <body class="font-sans antialiased">
        {{ $slot }}
    </body>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Success flash message
            @if (session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                animation: false,
                customClass: {
                    popup: 'animate__animated animate__slideInRight',
                    container: 'swal2-container-high-zindex'
                },
                didOpen: () => {
                    setTimeout(() => {
                        Swal.getPopup().classList.remove('animate__slideInRight');
                        Swal.getPopup().classList.add('animate__slideOutRight');
                    }, 2500);
                }
            });
            @endif

            // Error flash message
            @if (session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                animation: false,
                customClass: {
                    popup: 'animate__animated animate__slideInRight',
                    container: 'swal2-container-high-zindex'
                },
                didOpen: () => {
                    setTimeout(() => {
                        Swal.getPopup().classList.remove('animate__slideInRight');
                        Swal.getPopup().classList.add('animate__slideOutRight');
                    }, 2500);
                }
            });
            @endif
        });
    </script>
</html>
