<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.3/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/custom/print.css') }}">
    @yield('styles')
</head>

<body class="@yield('paper')">
    <div class="sheet">
        @yield('content')
    </div>
    <script src="{{ asset('assets/bootstrap-5.3.3/js/bootstrap.bundle.min.js') }}"></script>

    @hasSection('print')
        <script>
            window.print();
        </script>
    @endif
</body>

</html>
