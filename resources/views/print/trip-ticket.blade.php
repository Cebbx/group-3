<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Trip Ticket - {{ $ticket->ticket_number }}</title>
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
        <span class="text-gray-700 font-bold text-sm">Vehicle Trip Ticket</span>
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

    <!-- Main Ticket Sheet -->
    <div class="bg-white w-full max-w-[8.5in] mx-auto p-4 sm:p-8 flex flex-col print-container" style="min-height: 10.5in;">
        <!-- Header -->
        <div class="w-full border-b-2 border-black pb-4 mb-6 flex justify-center">
            <div class="relative flex flex-col items-center">
                <img src="/csu-logo.png" alt="CSU Logo" class="absolute right-[100%] mr-6 top-1/2 -translate-y-1/2 w-16 h-16 object-contain" />
                <h2 class="text-xs uppercase tracking-wider text-black">Republic of the Philippines</h2>
                <h1 class="text-sm font-extrabold uppercase text-black tracking-wide mt-0.5">Cagayan State University</h1>
                <h3 class="text-xs text-black mt-0.5">Lal-lo, Cagayan</h3>
            </div>
        </div>

        <!-- Title and QR Code row -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-sm font-extrabold tracking-wide uppercase border-b border-black inline-block pb-0.5">Vehicle Trip Ticket</h1>
                <div class="text-xs font-bold text-black mt-2 font-mono">TT No. Lal-2026 - {{ substr($ticket->ticket_number, 3) }}</div>
            </div>
            
            <!-- Scan to Complete QR Code -->
            <div class="flex flex-col items-center border border-black p-1 bg-white rounded shadow-sm">
                @php
                    $completionUrl = route('trip-tickets.complete-via-qr', ['ticket_number' => $ticket->ticket_number]);
                    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=85x85&data=' . urlencode($completionUrl);
                @endphp
                <img src="{{ $qrCodeUrl }}" alt="Completion QR" class="w-[85px] h-[85px] object-contain" />
                <span class="text-[8px] font-bold text-black mt-0.5 uppercase tracking-wider">Scan to Complete</span>
            </div>
        </div>

        <!-- Details Table Grid -->
        <table class="w-full border-collapse border border-black mb-6 text-xs">
            <tbody>
                <tr>
                    <td class="border border-black p-2.5 font-bold bg-gray-50 w-28">Date:</td>
                    <td class="border border-black p-2.5 w-1/2">{{ \Carbon\Carbon::parse($ticket->vehicleRequest?->date ?? $ticket->created_at)->format('F d, Y') }}</td>
                    <td class="border border-black p-2.5 font-bold bg-gray-50 w-24">Vehicle:</td>
                    <td class="border border-black p-2.5">{{ $vehicleModel }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2.5 font-bold bg-gray-50">Driver's Name:</td>
                    <td class="border border-black p-2.5 font-bold">{{ $ticket->driver?->name ?? 'N/A' }}</td>
                    <td class="border border-black p-2.5 font-bold bg-gray-50">PLATE No:</td>
                    <td class="border border-black p-2.5 font-bold">{{ $vehiclePlate }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2.5 font-bold bg-gray-50">Authorized Passenger/s:</td>
                    <td class="border border-black p-2.5" colspan="3">
                        @php
                            $passengers = $ticket->vehicleRequest?->passenger_names ?? [];
                            if (is_string($passengers)) {
                                $passengers = json_decode($passengers, true) ?? [];
                            }
                            $passengerNames = collect($passengers)->pluck('name')->join(', ');
                        @endphp
                        {{ $passengerNames ?: $ticket->vehicleRequest?->employee_name ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="border border-black p-2.5 font-bold bg-gray-50">Place to Visit:</td>
                    <td class="border border-black p-2.5" colspan="3">{{ $ticket->vehicleRequest?->destination ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2.5 font-bold bg-gray-50">Purpose/s:</td>
                    <td class="border border-black p-2.5" colspan="3">{{ $ticket->vehicleRequest?->purpose ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Certifications & Prepared/Approved signatures -->
        <div class="mb-6 text-xs">
            <p class="italic font-semibold mb-6">I CERTIFY that the vehicle is in GOOD RUNNING CONDITION:</p>
            
            <div class="grid grid-cols-3 gap-6 text-center mt-4">
                <div class="flex flex-col justify-end min-h-[65px]">
                    <span class="text-xs font-bold text-black">JOEL A. TUMAMAO</span>
                    <span class="text-[9px] text-black uppercase font-semibold">General Services Officer</span>
                    <div class="border-t border-black pt-1 mt-1 text-[9px] uppercase font-bold text-black">Prepared by:</div>
                </div>
                
                <div class="flex flex-col justify-end min-h-[65px]">
                    <span class="text-xs font-bold text-black">ENGR. JAMES B. CABILDO, PHD, ASEAN ENGR.</span>
                    <span class="text-[9px] text-black uppercase font-semibold">Campus Executive Officer</span>
                    <div class="border-t border-black pt-1 mt-1 text-[9px] uppercase font-bold text-black">Approved by:</div>
                </div>

                <div class="flex flex-col justify-end min-h-[65px]">
                    <div class="h-6 border-b border-gray-300 w-11/12 mx-auto"></div>
                    <div class="border-t border-black pt-1 mt-1 text-[9px] uppercase font-bold text-black">Driver's Signature:</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-4 mt-4">
            <div class="border-t border-black text-center pt-2 text-xs font-bold text-black uppercase">Passenger's Signature</div>
            <div class="border-t border-black text-center pt-2 text-xs font-bold text-black uppercase">Driver's Signature</div>
        </div>

        <!-- Itinerary to be filled by driver -->
        <div class="border border-black p-3 mb-3">
            <span class="text-[10px] font-extrabold uppercase tracking-wide text-black block mb-1">To be filled up by Driver:</span>
            <span class="text-xs underline font-bold text-black block mb-1">Itinerary of Travel:</span>
            
            <table class="w-full border-collapse border border-black text-[10px]">
                <thead>
                    <tr>
                        <th class="border border-black p-1 text-center font-bold" rowspan="2">DATE</th>
                        <th class="border border-black p-1 text-center font-bold" colspan="2">DEPARTURE</th>
                        <th class="border border-black p-1 text-center font-bold" colspan="2">ARRIVAL</th>
                    </tr>
                    <tr>
                        <th class="border border-black p-1 text-center font-bold">TIME</th>
                        <th class="border border-black p-1 text-center font-bold">PLACE</th>
                        <th class="border border-black p-1 text-center font-bold">TIME</th>
                        <th class="border border-black p-1 text-center font-bold">PLACE</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i=0; $i<4; $i++)
                        <tr>
                            <td class="border border-black p-2 h-6"></td>
                            <td class="border border-black p-2"></td>
                            <td class="border border-black p-2"></td>
                            <td class="border border-black p-2"></td>
                            <td class="border border-black p-2"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Post-travel Certification -->
        <div class="mb-3 text-xs mb-auto">
            <p class="font-semibold mb-2">I CERTIFY the Condition of Vehicle after travel:</p>
            <div class="flex justify-between items-end">
                <div class="flex flex-col gap-1.5">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="w-4 h-4 accent-black" />
                        <span class="font-bold text-black">GOOD</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="w-4 h-4 accent-black" />
                        <span class="font-bold text-black">Not GOOD (for Service maintenance)</span>
                    </label>
                </div>
                
                <div class="flex flex-col items-center w-64">
                    <div class="h-6 border-b border-gray-300 w-11/12 mx-auto mb-1"></div>
                    <div class="w-full border-t border-black text-center pt-1 text-[10px] font-bold text-black uppercase">Driver's Signature</div>
                </div>
            </div>
        </div>

        <!-- Document Metadata footer details -->
        <div class="pt-4 flex justify-between items-center text-[10px] text-black font-mono border-t border-black mt-4">
            <span>F - GSO - 61202</span>
            <span>Rev. No. 02, October 20, 2025</span>
        </div>
    </div>

    <!-- PDF Download Script -->
    <script>
        function downloadPDF() {
            const element = document.querySelector('.print-container');
            const opt = {
                margin:       0.4,
                filename:     'Vehicle-Trip-Ticket-{{ $ticket->ticket_number }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>

</body>
</html>
