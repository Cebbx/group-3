<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gasoline Withdrawal Slip - {{ $slip->slip_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Arial', 'Calibri', sans-serif;
            background-color: #f3f4f6; /* Gray background on screen */
            color: black;
        }
        @media print {
            body {
                background: white;
                color: black;
                font-size: 13px;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: letter; /* Force US Letter paper size */
                margin: 0.5in;
            }
            .print-container {
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
                height: auto !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 py-6 px-4">

    <!-- Floating Top Bar (hidden on print) -->
    <div class="no-print max-w-[7.5in] mx-auto mb-4 flex items-center justify-between bg-white p-4 rounded-xl shadow-md border border-gray-200 gap-4">
        <span class="text-gray-700 font-bold text-sm">Gasoline Withdrawal Slip</span>
        <div class="flex gap-2">
            <button onclick="downloadPDF()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download PDF File
            </button>
            <button onclick="window.print()" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center gap-2 text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Document
            </button>
        </div>
    </div>

    @php
        $items = $slip->requested_items ?? [];
        
        // If it's a string, try to decode it as JSON first (just in case it's a JSON-encoded array string)
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $items = $decoded;
            }
        }

        // Helper to safely fetch value from array items
        $getVal = function ($key) use ($items) {
            if (is_array($items)) {
                $val = $items[$key] ?? '';
                if (is_array($val)) {
                    return implode(', ', $val);
                }
                return (string) $val;
            }
            return '';
        };

        $diesel = $getVal('diesel');
        $gasRegular = $getVal('gasoline_regular');
        $gasPremium = $getVal('gasoline_premium');
        $lubricant40 = $getVal('lubricant_40');
        $lubricant30 = $getVal('lubricant_30');
        $brakeFluid = $getVal('brake_fluid');
        $grease = $getVal('grease_atf');
        $gearOil = $getVal('gear_oil');
    @endphp

    <!-- Main Ticket Sheet -->
    <div class="bg-white w-full max-w-[7.5in] mx-auto p-8 sm:p-12 shadow-lg border border-gray-200 flex flex-col justify-between print-container" style="min-height: 10.0in;">
        <div>
            <!-- Header (matching official CSU layout) -->
            <div class="text-center mb-1 pt-4">
                <p class="text-[12px] uppercase tracking-wider text-black font-medium">Republic of the Philippines</p>
                <h1 class="text-[16px] font-bold uppercase text-black tracking-wide mt-0.5">Cagayan State University</h1>
                <p class="text-[12px] text-black italic mt-0.5">Lal-lo, Campus</p>
            </div>

            <!-- Control No. and Date (right-aligned underneath header) -->
            <div class="flex justify-end text-[13px] mb-8 mt-2">
                <div class="space-y-1">
                    <p>Control No.: <span class="border-b border-black font-bold px-3 inline-block min-w-[130px] text-center">{{ $slip->slip_number }}</span></p>
                    <p>Date: <span class="border-b border-black px-3 inline-block min-w-[130px] text-center">{{ $slip->created_at->format('F d, Y') }}</span></p>
                </div>
            </div>

            <!-- Subject Details -->
            <div class="text-[14px] text-black space-y-4 mb-6">
                <div>
                    <p class="font-bold">The Manager</p>
                    <p>Shell Service Station</p>
                    <p>Bagumbayan, Lal-lo, Cagayan</p>
                </div>

                <div>
                    <p>Sir/ Madam:</p>
                </div>

                <!-- Authorization Paragraph -->
                <div class="leading-relaxed text-justify pt-1">
                    This is to AUTHORIZE <span class="border-b border-black font-bold px-3 inline-block min-w-[280px] text-center">{{ $slip->tripTicket?->driver?->name ?? '_______________' }}</span> official driver 
                    of <span class="border-b border-black font-bold px-3 inline-block min-w-[160px] text-center">{{ $vehicleModel ?: '_______________' }}</span> with plate No. <span class="border-b border-black font-bold px-3 inline-block min-w-[150px] text-center">{{ $vehiclePlate ?: '_______________' }}</span> to withdraw the following:
                </div>
            </div>

            @if(is_string($items) && !empty(trim($items)))
                <!-- Raw text layout (notes submitted from driver portal or seeded string) -->
                <div class="pl-16 text-[14px] text-black mb-8 leading-relaxed">
                    <strong class="block mb-2 text-slate-800">Requested Items/Notes:</strong>
                    <div class="border border-dashed border-gray-400 p-4 rounded-lg bg-gray-50/50 whitespace-pre-line">
                        {{ $items }}
                    </div>
                </div>
            @else
                <!-- Items Table/List (matching the image layout perfectly) -->
                <div class="pl-16 text-[14px] text-black space-y-2.5 mb-8">
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $diesel }}</span>
                        <span>No. of Liters of Diesel</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $gasRegular }}</span>
                        <span>No. of Liters of Gasoline (Regular)</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $gasPremium }}</span>
                        <span>No. of Liters of Gasoline (Premium)</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $lubricant40 }}</span>
                        <span>No. of Liters of Lubricant Oil 40</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $lubricant30 }}</span>
                        <span>No. of Liters of Lubricant Oil 30</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $brakeFluid }}</span>
                        <span>No. of Liters of Brake Fluid</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $grease }}</span>
                        <span>No. of Liters of <span class="underline underline-offset-2 font-semibold">2T</span>&nbsp;&nbsp;<span class="underline underline-offset-2 font-semibold">Grease</span>&nbsp;&nbsp;<span class="underline underline-offset-2 font-semibold">ATF</span></span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border-b border-black font-bold text-center w-16 inline-block h-6 pb-0.5">{{ $gearOil }}</span>
                        <span>No. of Liters of Gear Oil</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer Signatures (staggered stack to match photo exactly) -->
        <div class="mt-4">
            <div class="space-y-8 text-[14px] text-black">
                <!-- Prepared By (Left aligned, name indented) -->
                <div class="text-left w-full pl-4">
                    <p>Prepared by:</p>
                    <div class="pl-12 mt-4">
                        <p class="font-bold uppercase tracking-wide">JOEL A. TUMAMAO</p>
                        <p class="text-[13px] text-gray-800">General Services Officer</p>
                    </div>
                </div>

                <!-- Approved By (Left aligned, name indented) -->
                <div class="text-left w-full pl-4">
                    <p>Approved by:</p>
                    <div class="pl-12 mt-4">
                        <p class="font-bold uppercase tracking-wide">ENGR. JAMES B. CABILDO, ASEAN ENGR.</p>
                        <p class="text-[13px] text-gray-800">Campus Executive Officer</p>
                    </div>
                </div>
            </div>

            <!-- Form Code and Revision number at the bottom -->
            <div class="mt-8 flex justify-between text-[10px] text-black font-mono">
                <span>F - GSO - 61203</span>
                <span>Rev. No.: 01: 10-20-2025</span>
            </div>
        </div>
    </div>

    <!-- PDF Download Script -->
    <script>
        function downloadPDF() {
            const element = document.querySelector('.print-container');
            const opt = {
                margin:       0.5,
                filename:     'Gasoline-Withdrawal-Slip-{{ $slip->slip_number }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>

</body>
</html>
