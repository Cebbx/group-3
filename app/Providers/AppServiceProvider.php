<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dynamically override APP_URL and scheme for asset/route generation when accessed via tunnel
        if (isset($_SERVER['HTTP_HOST'])) {
            $proto = 'http';
            if (
                (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            ) {
                $proto = 'https';
            }
            $currentUrl = $proto . '://' . $_SERVER['HTTP_HOST'];
            config(['app.url' => $currentUrl]);
            \Illuminate\Support\Facades\URL::forceRootUrl($currentUrl);
            if ($proto === 'https') {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }

        $this->configureDefaults();

        // Real-time trip status activation based on travel date and time
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('trip_tickets')) {
                $pendingReadyTrips = \App\Models\TripTicket::where('status', 'pending')
                    ->whereHas('vehicleRequest', function ($q) {
                        $q->whereNotNull('document');
                    })
                    ->with(['vehicleRequest', 'driver'])
                    ->get();

                $now = \Illuminate\Support\Carbon::now('Asia/Manila');

                foreach ($pendingReadyTrips as $trip) {
                    $tripDateTime = \Illuminate\Support\Carbon::parse(
                        $trip->vehicleRequest->date . ' ' . $trip->vehicleRequest->time, 
                        'Asia/Manila'
                    );

                    if ($now->greaterThanOrEqualTo($tripDateTime)) {
                        // Activate trip ticket!
                        $trip->status = 'active';
                        $trip->save(); // This triggers model saving/saved hooks, updating driver status to 'on_trip' and sending SMS notification!
                    }
                }

                // 2. Auto-decline pending trips with no document after 2 hours of travel time
                $expiredTrips = \App\Models\TripTicket::where('status', 'pending')
                    ->whereHas('vehicleRequest', function ($q) {
                        $q->whereNull('document');
                    })
                    ->with(['vehicleRequest', 'driver'])
                    ->get();

                foreach ($expiredTrips as $trip) {
                    $tripDateTime = \Illuminate\Support\Carbon::parse(
                        $trip->vehicleRequest->date . ' ' . $trip->vehicleRequest->time, 
                        'Asia/Manila'
                    );

                    // If current time is 2 hours (120 minutes) past the travel time
                    if ($now->diffInMinutes($tripDateTime, false) < -120) {
                        // Cancel Trip Ticket quietly to prevent triggers
                        $trip->status = 'cancelled';
                        $trip->saveQuietly();

                        // Reject Vehicle Request quietly
                        if ($trip->vehicleRequest) {
                            $trip->vehicleRequest->status = 'rejected';
                            $trip->vehicleRequest->saveQuietly();
                        }

                        // Release Driver status manually
                        if ($trip->driver_id) {
                            $driver = \App\Models\Driver::find($trip->driver_id);
                            if ($driver) {
                                $driver->update(['status' => 'available']);
                            }
                        }

                        // Log to ActivityLog
                        \App\Models\ActivityLog::create([
                            'user_id' => null,
                            'user_name' => 'System',
                            'action' => 'Auto-Declined Request',
                            'model_type' => \App\Models\TripTicket::class,
                            'model_id' => $trip->id,
                            'details' => "System automatically declined request {$trip->vehicleRequest?->request_number} and cancelled ticket {$trip->ticket_number} (Travel time {$tripDateTime->format('h:i A')} passed by 2+ hours without CEO signature upload).",
                            'ip_address' => '127.0.0.1',
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence exceptions during database setup/migrations
        }

        // Register PWA Manifest, iOS Meta Tags, and Service Worker in Filament Head
        try {
            \Filament\Support\Facades\FilamentView::registerRenderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => '
                    <link rel="manifest" href="/manifest.json">
                    <meta name="theme-color" content="#1e3a8a">
                    <meta name="apple-mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
                    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
                    <script>
                        if ("serviceWorker" in navigator) {
                            window.addEventListener("load", function() {
                                navigator.serviceWorker.register("/sw.js").then(function(reg) {
                                    console.log("PWA Service Worker registered:", reg.scope);
                                }).catch(function(err) {
                                    console.log("PWA Service Worker registration failed:", err);
                                });
                            });
                        }
                    </script>
                    <style>
                        /* Premium Hover Highlight Effects for Filament Cards/Widgets */
                        .fi-wi-stats-overview-card,
                        .fi-wi-widget,
                        .fi-section,
                        .fi-ta-ctn {
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }
                        
                        /* Stats Card Hover Effect */
                        .fi-wi-stats-overview-card:hover {
                            transform: translateY(-5px) scale(1.01) !important;
                            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15), 0 10px 15px -5px rgba(0, 0, 0, 0.1) !important;
                            border-color: rgba(59, 130, 246, 0.4) !important;
                            background: rgba(30, 41, 59, 0.55) !important;
                        }
                        
                        /* Charts Hover Effect */
                        .fi-wi-widget:hover {
                            transform: translateY(-3px) !important;
                            box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.12) !important;
                            border-color: rgba(99, 102, 241, 0.3) !important;
                        }
                        
                        /* Tables and Form Sections Hover */
                        .fi-section:hover, 
                        .fi-ta-ctn:hover {
                            transform: translateY(-1px) !important;
                            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.08) !important;
                        }
                    </style>
                '
            );
        } catch (\Throwable $e) {
            // Silence if Filament is not fully loaded in CLI
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
