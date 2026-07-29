<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Request Form - {{ $request->request_number }}</title>
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
                font-size: 11px;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                min-height: 0 !important;
                height: auto !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: letter; /* Force US Letter paper size */
                margin: 0.4in;
            }
        }
    </style>
</head>
<body class="bg-gray-100 py-6 px-4">

    <!-- Floating Top Bar (hidden on print) -->
    <div class="no-print max-w-[8.5in] mx-auto mb-4 flex items-center justify-between bg-white p-4 rounded-xl shadow-md border border-gray-200 gap-4">
        <span class="text-gray-700 font-bold text-sm">Vehicle Request Form</span>
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

    <!-- Main Form Page Sheet -->
    <div class="bg-white w-full max-w-[8.5in] mx-auto p-4 sm:p-8 shadow-lg border border-gray-200 flex flex-col print-container" style="min-height: 10.5in;">
        <!-- Header -->
        <div class="w-full border-b-2 border-black pb-4 mb-6 flex justify-center">
            <div class="relative flex flex-col items-center">
                <img src="/csu-logo.png" alt="CSU Logo" class="absolute right-[100%] mr-6 top-1/2 -translate-y-1/2 w-16 h-16 object-contain" />
                <h2 class="text-xs uppercase tracking-wider text-black">Republic of the Philippines</h2>
                <h1 class="text-sm font-extrabold uppercase text-black tracking-wide mt-0.5">Cagayan State University</h1>
                <h3 class="text-xs text-black mt-0.5">Lal-lo Campus</h3>
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-black mt-1">General Service Office</h2>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center mb-4">
            <h1 class="text-base font-extrabold tracking-wide uppercase border-b border-black inline-block pb-0.5">Vehicle Request Form</h1>
        </div>

        <!-- Request Details Table Grid -->
        <div class="border border-black mb-6">
            <!-- Row 1 -->
            <div class="grid grid-cols-12 border-b border-black">
                <div class="col-span-8 p-3 border-r border-black flex flex-col justify-between" style="min-height: 70px;">
                    <span class="text-[10px] uppercase font-bold text-black">Client:</span>
                    <span class="text-sm font-bold text-black text-center mt-1 border-b border-gray-300 w-11/12 mx-auto">{{ $request->employee_name }}</span>
                    <span class="text-[9px] text-black text-center mt-0.5">Name</span>
                </div>
                <div class="col-span-4 p-3 flex flex-col justify-between" style="min-height: 70px;">
                    <span class="text-[10px] uppercase font-bold text-black">Date:</span>
                    <span class="text-sm font-bold text-black text-center mt-1 border-b border-gray-300 w-11/12 mx-auto">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</span>
                    <span class="text-[9px] text-black text-center mt-0.5">Office: <span class="font-bold text-black">{{ $request->department }}</span></span>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="grid grid-cols-12 border-b border-black">
                <div class="col-span-6 p-3 border-r border-black flex flex-col" style="min-height: 50px;">
                    <span class="text-[10px] uppercase font-bold text-black">Date of Travel:</span>
                    <span class="text-sm font-bold text-black mt-1">{{ \Carbon\Carbon::parse($request->date)->format('F d, Y') }}</span>
                </div>
                <div class="col-span-6 p-3 flex flex-col" style="min-height: 50px;">
                    <span class="text-[10px] uppercase font-bold text-black">Departure Time:</span>
                    <span class="text-sm font-bold text-black mt-1">{{ \Carbon\Carbon::parse($request->time)->format('g:i A') }}</span>
                </div>
            </div>

            <!-- Row 3 -->
            <div class="grid grid-cols-12 border-b border-black">
                <div class="col-span-12 p-3 flex flex-col" style="min-height: 50px;">
                    <span class="text-[10px] uppercase font-bold text-black">Destination:</span>
                    <span class="text-sm font-bold text-black mt-1">{{ $request->destination }}</span>
                </div>
            </div>

            <!-- Row 4 -->
            <div class="grid grid-cols-12 border-b border-black">
                <div class="col-span-12 p-3 flex flex-col" style="min-height: 70px;">
                    <span class="text-[10px] uppercase font-bold text-black">Purpose of Travel:</span>
                    <span class="text-sm font-medium text-black mt-1 leading-relaxed">{{ $request->purpose }}</span>
                </div>
            </div>

            <!-- Row 5 (Passengers) -->
            <div class="grid grid-cols-12">
                <div class="col-span-3 p-3 border-r border-black flex items-center justify-center">
                    <span class="text-[10px] uppercase font-bold text-black text-center">Name of Passengers</span>
                </div>
                <div class="col-span-9 p-3 flex flex-col gap-1.5">
                    @php
                        $passengers = $request->passenger_names ?? [];
                        if (is_string($passengers)) {
                            $passengers = json_decode($passengers, true) ?? [];
                        }
                    @endphp
                    @if(count($passengers) > 0)
                        <div class="grid grid-cols-2 gap-2 text-xs font-semibold text-black">
                            @foreach($passengers as $idx => $p)
                                <div class="border-b border-gray-300 pb-0.5">{{ $idx + 1 }}. {{ $p['name'] ?? $p }}</div>
                            @endforeach
                        </div>
                    @else
                        <span class="text-xs text-black font-light italic">No passengers specified.</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer approvals section -->
        <div class="grid grid-cols-12 gap-8 mt-6 mb-auto">
            <!-- Left: Client's Signature -->
            <div class="col-span-6 flex flex-col items-center justify-end mt-6">
                <div class="w-full border-t border-black text-center pt-2">
                    <span class="text-xs font-bold text-black uppercase">Client's Signature</span>
                </div>
            </div>

            <!-- Right: Approved / Disapproved checkboxes and Joel Tumamao signature -->
            <div class="col-span-6 flex flex-col gap-6 pl-6">
                <!-- Checkboxes -->
                <div class="flex items-center gap-6 text-xs font-bold text-black">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 accent-black" {{ $request->status === 'approved' || $request->status === 'completed' || $request->status === 'on_trip' ? 'checked' : '' }} />
                        <span>Approved</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 accent-black" {{ $request->status === 'rejected' ? 'checked' : '' }} />
                        <span>Disapproved</span>
                    </label>
                </div>

                <!-- GSO signature -->
                <div class="flex flex-col items-center mt-6">
                    <span class="text-sm font-extrabold text-black">JOEL A. TUMAMAO</span>
                    <span class="text-[10px] text-black uppercase font-semibold">GSO</span>
                    <div class="w-full border-t border-black text-center mt-2 pt-1">
                        <span class="text-[10px] font-bold text-black uppercase">Received by:</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Metadata footer details -->
        <div class="pt-4 flex justify-between items-center text-[10px] text-black font-mono border-t border-black mt-6">
            <span>F - GSO - 61207</span>
            <span>Rev. No. 00, October 23, 2025</span>
        </div>
    </div>

    <!-- PDF Download Script -->
    <script>
        function downloadPDF() {
            const element = document.querySelector('.print-container');
            const opt = {
                margin:       0.4,
                filename:     'Vehicle-Request-Form-{{ $request->request_number }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>

</body>
</html>
