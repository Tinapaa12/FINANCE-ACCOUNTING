<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Payable</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    @include('layouts.sidebar')

    <div class="main">

        @include('layouts.navbar')

        @yield('content')

    </div>

    <script src="{{ asset('js/supplier-bills.js') }}"></script>


</body>
</html>