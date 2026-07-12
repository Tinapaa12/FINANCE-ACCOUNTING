<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Report PDF')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .pdf-page { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
    @if(request()->query('print') === '1')
        <script>window.addEventListener('DOMContentLoaded', function () { setTimeout(window.print, 500); });</script>
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    var opt = {
                        margin:       0.5,
                        filename:     '@yield('pdf-title', 'Report').pdf',
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2, letterRendering: true },
                        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
                    };
                    html2pdf().set(opt).from(document.getElementById('pdf-page')).save();
                }, 1000);
            });
        </script>
    @endif
</head>
<body class="bg-gray-200 min-h-screen py-10">

    <div class="no-print max-w-3xl mx-auto mb-4 flex items-center justify-between px-2">
        <button onclick="window.close()" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <span>&larr;</span> Close
        </button>
        <button onclick="window.print()" class="bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-800 transition-colors">
            Download
        </button>
    </div>

    <div id="pdf-page" class="pdf-page bg-white max-w-3xl mx-auto shadow-lg rounded-sm p-10 text-gray-800">

        <div class="flex items-center justify-between border-b pb-4 mb-6">
            <div>
                <p class="font-bold text-lg">@yield('company-name', 'Murana')</p>
                <p class="text-xs text-gray-400">Finance and Accounting</p>
            </div>
            <div class="text-right text-xs text-gray-400">
                <p>Generated: {{ now()->format('F j, Y g:i A') }}</p>
            </div>
        </div>

        <h1 class="text-2xl font-bold mb-6">@yield('pdf-title', 'Report')</h1>

        @yield('pdf-content')

        <div class="border-t mt-10 pt-4 text-xs text-gray-400 text-center">
            This is a system-generated document from Finance and Accounting.
        </div>
    </div>

</body>
</html>
