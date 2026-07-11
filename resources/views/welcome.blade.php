<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex items-center justify-center min-h-screen bg-[#f5f5f5]">
        <div class="text-center">
            <h1 class="text-[2rem] mb-[1rem]">Account Payable System</h1>
            <p class="text-gray-500 mb-[2rem]">Manage supplier bills, approvals, payments and reports</p>
            <a href="{{ url('/dashboard') }}" class="inline-block px-[32px] py-[12px] bg-[#5865f2] text-white rounded-[8px] no-underline text-[16px]">Go to Dashboard</a>
        </div>
    </body>
</html>
