<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\Driver;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QrCodeController extends Controller
{
    public function completeTrip($ticketNumber)
    {
        // Security Check: Only verified gate guards can complete trips
        if (!session('guard_verified', false)) {
            return $this->renderResponse(
                'error',
                'Access Denied',
                'Only authorized campus gate guards with verified sessions can complete trip tickets. Please use the official guard scanner portal.',
                null
            );
        }

        $ticket = TripTicket::where('ticket_number', $ticketNumber)->first();

        if (!$ticket) {
            return $this->renderResponse(
                'error',
                'Trip Ticket Not Found',
                "We couldn't find any trip ticket with reference number: <strong>{$ticketNumber}</strong>.",
                null
            );
        }

        // Check current status
        if ($ticket->status === 'completed') {
            return $this->renderResponse(
                'info',
                'Trip Already Completed',
                "This trip ticket (<strong>{$ticketNumber}</strong>) was already marked as completed.",
                $ticket
            );
        }

        if ($ticket->status !== 'active') {
            return $this->renderResponse(
                'warning',
                'Trip Not Active',
                "This trip ticket (<strong>{$ticketNumber}</strong>) is currently in <strong>" . ucfirst($ticket->status) . "</strong> status. Only trips currently \"On Trip\" can be completed.",
                $ticket
            );
        }

        // Complete the trip ticket!
        $ticket->update(['status' => 'completed']);

        // Sync associated Vehicle Request
        if ($ticket->vehicleRequest) {
            $ticket->vehicleRequest->update(['status' => 'completed']);
        }

        // Note: Driver status is automatically synced to 'available' by the TripTicket saved model observer

        return $this->renderResponse(
            'success',
            'Trip Completed Successfully',
            "Trip ticket <strong>{$ticketNumber}</strong> has been successfully completed. The driver and vehicle are now available.",
            $ticket
        );
    }

    private function renderResponse($type, $title, $message, ?TripTicket $ticket)
    {
        $bgColor = 'from-gray-900 to-slate-950';
        $cardBg = 'bg-slate-900/40 backdrop-blur-xl border border-slate-800';
        $iconHtml = '';
        $themeColor = 'blue';

        if ($type === 'success') {
            $themeColor = 'emerald';
            $iconHtml = '<div class="w-20 h-20 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(16,185,129,0.15)] animate-bounce">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>';
        } elseif ($type === 'error') {
            $themeColor = 'red';
            $iconHtml = '<div class="w-20 h-20 bg-red-500/10 border border-red-500/20 text-red-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(239,68,68,0.15)]">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>';
        } elseif ($type === 'warning') {
            $themeColor = 'amber';
            $iconHtml = '<div class="w-20 h-20 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(245,158,11,0.15)]">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>';
        } else {
            $themeColor = 'blue';
            $iconHtml = '<div class="w-20 h-20 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_50px_rgba(59,130,246,0.15)]">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>';
        }

        $detailsHtml = '';
        if ($ticket) {
            $driverName = $ticket->driver?->name ?? 'N/A';
            $vehicleName = $ticket->vehicle ?? 'N/A';
            $destination = $ticket->vehicleRequest?->destination ?? 'N/A';
            $completedAt = Carbon::now('Asia/Manila')->format('F d, Y h:i A');

            $detailsHtml = "
            <div class='mt-8 pt-6 border-t border-slate-800/60 text-left text-xs text-slate-400 space-y-3.5'>
                <div class='flex justify-between items-center'><span class='text-slate-500'>Driver:</span> <strong class='text-slate-200'>{$driverName}</strong></div>
                <div class='flex justify-between items-center'><span class='text-slate-500'>Vehicle:</span> <strong class='text-slate-200'>{$vehicleName}</strong></div>
                <div class='flex justify-between items-start gap-4'><span class='text-slate-500 shrink-0'>Destination:</span> <strong class='text-slate-200 text-right'>{$destination}</strong></div>
                <div class='flex justify-between items-center'><span class='text-slate-500'>Completed:</span> <strong class='text-slate-200'>{$completedAt}</strong></div>
            </div>";
        }

        $html = "
        <!DOCTYPE html>
        <html lang='en' class='h-full'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title}</title>
            <script src='https://cdn.tailwindcss.com'></script>
            <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap' rel='stylesheet'>
            <style>
                body {
                    font-family: 'Outfit', sans-serif;
                }
            </style>
        </head>
        <body class='h-full flex items-center justify-center bg-gradient-to-tr {$bgColor} p-4 text-slate-100 antialiased'>
            <div class='w-full max-w-md {$cardBg} rounded-3xl p-8 text-center shadow-2xl relative overflow-hidden transition-all duration-300 hover:scale-[1.01]'>
                <!-- Glow Effect -->
                <div class='absolute -top-24 -left-24 w-48 h-48 bg-{$themeColor}-500/10 rounded-full blur-3xl pointer-events-none'></div>
                
                {$iconHtml}
                
                <h1 class='text-2xl font-extrabold tracking-tight text-white mb-3'>{$title}</h1>
                <p class='text-sm text-slate-300 leading-relaxed'>{$message}</p>
                
                {$detailsHtml}
                
                <div class='mt-8 flex flex-col gap-2.5'>
                    <a href='/guard/scanner' class='w-full inline-flex items-center justify-center bg-slate-950 border border-slate-800/80 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-400 font-bold py-3 px-6 rounded-2xl text-xs uppercase tracking-wider transition-all shadow-lg hover:shadow-emerald-950/20'>
                        Scan Next Vehicle
                    </a>
                </div>

                <div class='mt-6 shrink-0'>
                    <span class='inline-block text-[10px] uppercase tracking-widest text-slate-500 font-semibold'>PeliCle Trip Management</span>
                </div>
            </div>
        </body>
        </html>";

        return response($html);
    }

    public function scannerPage()
    {
        $isVerified = session('guard_verified', false);
        return view('guard.scanner', compact('isVerified'));
    }

    public function verifyPin(Request $request)
    {
        $pin = $request->input('pin');
        if ($pin === '1234') {
            session(['guard_verified' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Incorrect PIN code! Please try again.']);
    }
}
