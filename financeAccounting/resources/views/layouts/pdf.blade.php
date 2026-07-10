{{--
    resources/views/layouts/pdf.blade.php

    A lightweight "mock PDF" layout — renders content as a printable A4-style
    white page centered on a gray backdrop, with a print button. Not a real
    PDF file; it's an HTML page styled to look/print like one. Swap this out
    later for a real PDF library (e.g. barryvdh/laravel-dompdf) if needed.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Report PDF')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .pdf-page { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-200 min-h-screen py-10">

    {{-- Toolbar (hidden when printing) --}}
    <div class="no-print max-w-3xl mx-auto mb-4 flex items-center justify-between px-2">
        <button onclick="window.close()" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <span>&larr;</span> Close
        </button>
        <button onclick="window.print()" class="bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-800 transition-colors">
            🖨️ Print / Save as PDF
        </button>
    </div>

    {{-- "Page" --}}
    <div class="pdf-page bg-white max-w-3xl mx-auto shadow-lg rounded-sm p-10 text-gray-800">

        <div class="flex items-center justify-between border-b pb-4 mb-6">
            <div>
                <p class="font-bold text-lg">Murana</p>
                <p class="text-xs text-gray-400">Finance and Accounting</p>
            </div>
            <div class="text-right text-xs text-gray-400">
                <p>Generated: {{ now()->format('F j, Y g:i A') }}</p>
            </div>
        </div>

        <h1 class="text-2xl font-bold mb-6">@yield('pdf-title', 'Report')</h1>

        @yield('pdf-content')

        <div class="border-t mt-10 pt-4 text-xs text-gray-400 text-center">
            This is a system-generated document from Murana Finance and Accounting.
        </div>
    </div>

</body>
</html>
