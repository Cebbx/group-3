<x-filament-panels::page>
    @php
        $activeTrip = $this->getActiveTrip();
    @endphp

    <style>
        .driver-dashboard-container {
            font-family: 'Outfit', 'Inter', 'Segoe UI', sans-serif;
        }
        
        /* Banner Card */
        .hero-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: #ffffff;
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .hero-banner h2 {
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 6px 0;
            color: #ffffff;
            line-height: 1.2;
        }
        .hero-banner p {
            font-size: 13px;
            color: #cbd5e1;
            margin: 0;
            font-family: monospace;
        }
        .btn-logout {
            background: #ef4444;
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: #dc2626;
        }

        /* Alert Active Trip Card */
        .banner-alert {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            color: #b45309;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .dark .banner-alert {
            background: #78350f;
            border-left-color: #f59e0b;
            color: #fef3c7;
        }
        .banner-alert p {
            margin: 0 0 12px 0;
            font-size: 14px;
            font-weight: 700;
        }
        
        /* Action Buttons Row */
        .alert-actions-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-top: 12px;
        }
        .btn-alert-action {
            background: #d97706;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-alert-action:hover {
            background: #b45309;
        }
        .btn-milestone {
            background: #10b981;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11px;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-milestone:hover {
            background: #059669;
        }
        .btn-milestone:disabled {
            background: #cbd5e1;
            color: #94a3b8;
            cursor: not-allowed;
        }
        .dark .btn-milestone:disabled {
            background: #334155;
            color: #475569;
        }
        .btn-emergency {
            background: #ef4444;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11px;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-emergency:hover {
            background: #dc2626;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dark .stat-card {
            background: #182232;
            border-color: #2d3748;
        }
        .stat-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-val {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
            display: block;
        }
        .dark .stat-val {
            color: #ffffff;
        }
        .stat-emoji {
            font-size: 24px;
        }
        
        .btn-toggle-duty {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 9px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.2s;
        }
        .dark .btn-toggle-duty {
            background: #1e293b;
            color: #cbd5e1;
            border-color: #334155;
        }
        .btn-toggle-duty:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .dark .btn-toggle-duty:hover {
            background: #334155;
            color: white;
        }

        /* Layout Columns */
        .dashboard-layout {
            display: grid;
            grid-template-columns: 2.2fr 1fr;
            gap: 24px;
        }
        @media (max-width: 1024px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Card panels */
        .card-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .dark .card-panel {
            background: #182232;
            border-color: #2d3748;
        }
        .card-header {
            background: #f8fafc;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 800;
            color: #0f172a;
            font-size: 14px;
        }
        .dark .card-header {
            background: #1e293b;
            border-bottom-color: #2d3748;
            color: #ffffff;
        }
        .card-body {
            padding: 20px;
        }

        /* Structured Trip Table */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .trip-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        .trip-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            padding: 12px 16px;
            text-transform: uppercase;
            font-size: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .dark .trip-table th {
            background: #1e293b;
            color: #94a3b8;
            border-bottom-color: #2d3748;
        }
        .trip-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .dark .trip-table td {
            color: #cbd5e1;
            border-bottom-color: #2d3748;
        }
        .trip-table tr:hover {
            background: #f8fafc;
        }
        .dark .trip-table tr:hover {
            background: #1f2937;
        }

        /* Status Badge */
        .badge-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .badge-active {
            background: #ffedd5;
            color: #c2410c;
        }
        .dark .badge-active {
            background: #7c2d12;
            color: #ffedd5;
        }
        .badge-pending {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .dark .badge-pending {
            background: #172554;
            color: #dbeafe;
        }

        /* Action Buttons */
        .btn-view {
            background: #3b82f6;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-view:hover {
            background: #2563eb;
        }

        /* Guidelines & Contacts */
        .rules-list {
            list-style: decimal;
            padding-left: 20px;
            margin: 0;
            font-size: 12px;
            line-height: 1.6;
            color: #475569;
        }
        .dark .rules-list {
            color: #94a3b8;
        }
        .rules-list li {
            margin-bottom: 8px;
        }
        .contact-item {
            border-bottom: 1px solid #f1f5f9;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .dark .contact-item {
            border-bottom-color: #2d3748;
        }
        .contact-item:last-child {
            border-bottom: none;
        }
        .contact-name {
            color: #64748b;
        }
        .dark .contact-name {
            color: #94a3b8;
        }
        .contact-num {
            font-weight: 700;
            color: #0f172a;
        }
        .dark .contact-num {
            color: #ffffff;
        }
    </style>

    <div class="driver-dashboard-container">
        
        <!-- Welcome Hero Banner -->
        <div class="hero-banner">
            <div>
                <h2>Hello, {{ auth()->user()->name }}!</h2>
                <p>License ID: {{ auth()->user()->email }} &nbsp;|&nbsp; Duty: Official Driver</p>
            </div>
            
            <form action="{{ filament()->getPanel('driver')->getLogoutUrl() }}" method="post">
                @csrf
                <button type="submit" class="btn-logout">
                    Exit Portal
                </button>
            </form>
        </div>

        <!-- Alert Active Trip & Interactive Logs -->
        @if($activeTrip)
             @php
                $depLogged = $this->hasLoggedDeparture($activeTrip->id);
                $arrLogged = $this->hasLoggedArrival($activeTrip->id);
                $completionUrl = route('trip-tickets.complete-via-qr', ['ticket_number' => $activeTrip->ticket_number]);
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($completionUrl);
            @endphp
            <div class="banner-alert">
                <p>🚨 ON-GOING TRIP: Destination: <strong>{{ $activeTrip->vehicleRequest?->destination ?? 'N/A' }}</strong></p>
                <div style="font-size: 12px; margin-bottom: 12px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 10px;">
                    <strong>Travel Logs:</strong>
                    @if(!$depLogged)
                        <span style="opacity: 0.8;">Departure not logged.</span>
                    @elseif($depLogged && !$arrLogged)
                        <span style="color: #10b981; font-weight: bold;">🛫 Departure Logged!</span> &middot; <span style="opacity: 0.8;">Not arrived yet.</span>
                    @else
                        <span style="color: #10b981; font-weight: bold;">🛫 Departure & 🛬 Arrival Logged!</span> &middot; <span style="font-weight: bold;">Present QR at gate desk.</span>
                    @endif
                </div>

                <!-- Embedded Home QR Code -->
                <div class="flex flex-col items-center justify-center p-5 bg-amber-500/5 dark:bg-amber-400/5 border border-amber-500/20 rounded-2xl my-4 max-w-[260px] mx-auto gap-3 text-center shadow-inner">
                    <img src="{{ $qrCodeUrl }}" alt="Trip QR Code" class="w-40 h-40 rounded-xl shadow-md border-2 border-white dark:border-slate-800 bg-white p-1" />
                    <div>
                        <p class="text-[11px] text-amber-800 dark:text-amber-200 font-extrabold uppercase tracking-wider">Gate Clearance QR Code</p>
                        <p class="text-[10px] text-amber-700/85 dark:text-amber-300/80 mt-1">Present this QR code to the Security Guard at the campus gate.</p>
                    </div>
                </div>
                
                <div class="alert-actions-row">
                    <a href="{{ \App\Filament\Driver\Resources\TripTickets\TripTicketResource::getUrl('view', ['record' => $activeTrip->id]) }}" class="btn-alert-action">
                        📄 View Trip Details
                    </a>
                    
                    @if(!$depLogged)
                        <button wire:click="logDeparture" class="btn-milestone">
                            🛫 Log Departure
                        </button>
                    @elseif($depLogged && !$arrLogged)
                        <button wire:click="logArrival" class="btn-milestone">
                            🛬 Log Arrival
                        </button>
                    @endif

                    <button onclick="confirm('Are you sure you want to report a vehicle breakdown?\nThis will cancel the active trip ticket and alert GSO Admin.') && @this.reportBreakdown()" class="btn-emergency">
                        ⚠️ Report Breakdown
                    </button>
                </div>
            </div>
        @endif

        <!-- Quick Statistics Panel -->
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <span class="stat-label">Duty Status</span>
                    <span class="stat-val">
                        @if(auth()->user()->driver?->status === 'on_trip')
                            On Trip
                        @elseif(auth()->user()->driver?->status === 'unavailable')
                            Offline (Off-Duty)
                        @else
                            Available
                        @endif
                    </span>
                    <button wire:click="toggleDutyStatus" class="btn-toggle-duty">
                        🔄 Toggle Status
                    </button>
                </div>
                <div class="stat-emoji">🚦</div>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Completed Trips</span>
                    <span class="stat-val">{{ $this->getCompletedTripsCount() }}</span>
                </div>
                <div class="stat-emoji">🏁</div>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Active Vehicle</span>
                    <span class="stat-val">{{ $activeTrip ? $activeTrip->vehicle : 'None' }}</span>
                </div>
                <div class="stat-emoji">🚗</div>
            </div>
        </div>

        <!-- Two Column Workspace -->
        <div class="dashboard-layout">
            
            <!-- Left: Assigned Trips Table -->
            <div class="card-panel">
                <div class="card-header">
                    Assigned Trips and Schedules
                </div>
                <div class="table-container">
                    <table class="trip-table">
                        <thead>
                            <tr>
                                <th>Ticket No.</th>
                                <th>Destination</th>
                                <th>Schedule (Date & Time)</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->getAssignedTrips() as $trip)
                                <tr>
                                    <td style="font-weight: 700; font-family: monospace;">
                                        {{ $trip->ticket_number }}
                                    </td>
                                    <td style="font-weight: 600;">
                                        {{ $trip->vehicleRequest?->destination ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <strong>{{ $trip->vehicleRequest?->date ? \Carbon\Carbon::parse($trip->vehicleRequest->date)->format('M d, Y') : 'N/A' }}</strong>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                            {{ $trip->vehicleRequest?->time ? \Carbon\Carbon::parse($trip->vehicleRequest->time)->format('g:i A') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $trip->vehicle }}
                                    </td>
                                    <td>
                                        <span class="badge-status {{ $trip->status === 'active' ? 'badge-active' : 'badge-pending' }}">
                                            {{ $trip->status }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ \App\Filament\Driver\Resources\TripTickets\TripTicketResource::getUrl('view', ['record' => $trip->id]) }}" class="btn-view">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 32px; color: #94a3b8; font-style: italic;">
                                        No active or pending trips assigned currently.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Rules & Contacts -->
            <div>
                
                <!-- Travel Guidelines -->
                <div class="card-panel">
                    <div class="card-header">
                        Travel Guidelines
                    </div>
                    <div class="card-body">
                        <ul class="rules-list">
                            <li>Verify that the **Vehicle Log** has been signed before heading out.</li>
                            <li>Maintain passenger limit capacity rules.</li>
                            <li>Request fuel using printed Gasoline Slips.</li>
                            <li>Scan your QR Code with the Gate Guard to complete trips.</li>
                        </ul>
                    </div>
                </div>

                <!-- Emergency Contacts -->
                <div class="card-panel">
                    <div class="card-header">
                        GSO Hotline
                    </div>
                    <div class="card-body" style="padding: 10px 20px;">
                        <div class="contact-item">
                            <span class="contact-name">GSO Main Office</span>
                            <span class="contact-num">0917-555-8888</span>
                        </div>
                        <div class="contact-item">
                            <span class="contact-name">Joel Tumamao (GSO)</span>
                            <span class="contact-num">0953-113-537</span>
                        </div>
                        <div class="contact-item">
                            <span class="contact-name">Security Gate</span>
                            <span class="contact-num">Local 104</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-filament-panels::page>
