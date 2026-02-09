<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $item->sku }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .shadow-lg {
                box-shadow: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg text-center max-w-sm w-full border border-gray-200">
        <h1 class="text-lg font-bold mb-1 text-gray-900">{{ $item->product->name }}</h1>
        <p class="text-sm text-gray-500 mb-6 font-mono">{{ $item->sku }}</p>

        <div class="flex justify-center mb-6">
            {!! $qrCode !!}
        </div>

        @if($item->stock > 0)
            <p class="text-xs text-green-600 font-medium mb-4">In Stock: {{ $item->stock }} {{ $item->unit }}</p>
        @endif

        <div class="no-print space-y-2 mt-6">
            <button onclick="window.print()"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-printer">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Print QR Code
            </button>
            <button onclick="window.close()"
                class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition font-medium">
                Close Window
            </button>
        </div>
    </div>
</body>

</html>