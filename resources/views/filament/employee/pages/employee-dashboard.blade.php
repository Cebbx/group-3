<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $recentRequests = $this->getRecentRequests();
        $activeTrips = $this->getActiveTrips();
        $deptName = auth()->user()->name;
    @endphp

    <style>
        .employee-dashboard-container {
            font-family: 'Outfit', 'Inter', sans-serif;
        }

        /* Banner Header */
        .welcome-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
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
        .welcome-banner h2 {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
            color: white;
        }
        .welcome-banner p {
            font-size: 13px;
            color: #94a3b8;
            margin: 4px 0 0 0;
        }
        .btn-new-req {
            background: #f59e0b;
            color: #0f172a;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-new-req:hover {
            background: #d97706;
            color: white;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            font-size: 20px;
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

        /* Two Column Layout */
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

        /* Active Driver Card Grid */
        .active-trip-card {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            color: #1e3a8a;
        }
        .dark .active-trip-card {
            background: #1e293b;
            border-color: #3b82f6;
            color: #93c5fd;
        }
        .active-trip-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 800;
            font-size: 13px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(59, 130, 246, 0.15);
            padding-bottom: 8px;
        }

        /* Structured Requests Table */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .req-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        .req-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            padding: 12px 16px;
            text-transform: uppercase;
            font-size: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .dark .req-table th {
            background: #1e293b;
            color: #94a3b8;
            border-bottom-color: #2d3748;
        }
        .req-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .dark .req-table td {
            color: #cbd5e1;
            border-bottom-color: #2d3748;
        }
        .req-table tr:hover {
            background: #f8fafc;
        }
        .dark .req-table tr:hover {
            background: #1f2937;
        }

        /* Status Badge colors */
        .badge-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .badge-pending {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .dark .badge-pending {
            background: #1e293b;
            color: #93c5fd;
        }
        .badge-approved {
            background: #ecfdf5;
            color: #047857;
        }
        .dark .badge-approved {
            background: #064e3b;
            color: #a7f3d0;
        }
        .badge-on_trip {
            background: #fff7ed;
            color: #c2410c;
        }
        .dark .badge-on_trip {
            background: #7c2d12;
            color: #ffedd5;
        }
        .badge-completed {
            background: #f0fdf4;
            color: #16a34a;
        }
        .badge-rejected {
            background: #fef2f2;
            color: #dc2626;
        }
        .dark .badge-rejected {
            background: #7f1d1d;
            color: #fca5a5;
        }

        /* Actions */
        .btn-action {
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
        .btn-action:hover {
            background: #2563eb;
        }

        /* Contact Details list */
        .contact-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .contact-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .dark .contact-item {
            border-bottom-color: #2d3748;
        }
        .contact-item:last-child {
            border-bottom: none;
        }
    </style>

    <div class="employee-dashboard-container">
        
        <!-- Welcome banner -->
        <div class="welcome-banner">
            <div>
                <h2>Welcome, {{ $deptName }} Representative!</h2>
                <p>Manage and track your college vehicle requests for official trips and seminars.</p>
            </div>
            <a href="{{ \App\Filament\Employee\Resources\VehicleRequests\VehicleRequestResource::getUrl('create') }}" class="btn-new-req">
                ➕ Request a Vehicle
            </a>
        </div>

        <!-- Stats widgets grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <span class="stat-label">Total Requests</span>
                    <span class="stat-val">{{ $stats['total'] }}</span>
                </div>
                <div class="stat-emoji">📁</div>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Pending GSO</span>
                    <span class="stat-val">{{ $stats['pending'] }}</span>
                </div>
                <div class="stat-emoji">🕒</div>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Approved / Scheduled</span>
                    <span class="stat-val">{{ $stats['approved'] }}</span>
                </div>
                <div class="stat-emoji">📅</div>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">On Trip</span>
                    <span class="stat-val">{{ $stats['on_trip'] }}</span>
                </div>
                <div class="stat-emoji">🚗</div>
            </div>
        </div>

        <!-- Main Workspace Layout -->
        <div class="dashboard-layout">
            
            <!-- Left Side: Recent Requests List -->
            <div class="card-panel">
                <div class="card-header">
                    Recent Vehicle Requests History
                </div>
                <div class="table-container">
                    <table class="req-table">
                        <thead>
                            <tr>
                                <th>Req ID</th>
                                <th>Destination</th>
                                <th>Travel Date & Time</th>
                                <th>Vehicle Type</th>
                                <th>Status</th>
                                <th style="text-align: center;">Print Slip</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $req)
                                <tr>
                                    <td style="font-weight: 700; font-family: monospace;">{{ $req->request_number }}</td>
                                    <td style="font-weight: 600;">{{ $req->destination }}</td>
                                    <td>
                                        <strong>{{ $req->date ? \Carbon\Carbon::parse($req->date)->format('M d, Y') : 'N/A' }}</strong>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                                            {{ $req->time ? \Carbon\Carbon::parse($req->time)->format('g:i A') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td>{{ $req->vehicle }}</td>
                                    <td>
                                        <span class="badge-status badge-{{ $req->status }}">
                                            {{ str_replace('_', ' ', $req->status) }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($req->status === 'approved' || $req->status === 'on_trip' || $req->status === 'completed')
                                            <a href="{{ route('vehicle-requests.print', $req->id) }}" target="_blank" class="btn-action" style="background: #10b981;">
                                                🖨️ Print
                                            </a>
                                        @else
                                            <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Pending Approval</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 32px; color: #94a3b8; font-style: italic;">
                                        No recent vehicle requests found. Click the button above to make your first request.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Side: Active Assignment details & Info -->
            <div>
                
                <!-- Live Trip Assignment Tracker -->
                <div class="card-panel">
                    <div class="card-header">
                        Driver & Vehicle Assignment
                    </div>
                    <div class="card-body">
                        @forelse($activeTrips as $tripReq)
                            @php
                                $ticket = $tripReq->tripTicket;
                                $driver = $ticket?->driver;
                            @endphp
                            <div class="active-trip-card">
                                <div class="active-trip-header">
                                    <span>{{ $tripReq->request_number }}</span>
                                    <span class="badge-status badge-{{ $tripReq->status }}">{{ str_replace('_', ' ', $tripReq->status) }}</span>
                                </div>
                                <div style="font-size: 12px; line-height: 1.6;">
                                    <div>📍 <strong>Dest:</strong> {{ $tripReq->destination }}</div>
                                    <div style="margin-top: 6px;">🚗 <strong>Vehicle:</strong> {{ $ticket->vehicle ?? 'Assigned Vehicle' }}</div>
                                    <div style="margin-top: 6px;">👤 <strong>Driver:</strong> {{ $driver?->name ?? 'GSO Assigning Driver...' }}</div>
                                    <div style="margin-top: 6px;">📞 <strong>Contact:</strong> {{ $driver?->contact_number ?? 'N/A' }}</div>
                                </div>
                                @if($ticket)
                                    <div style="margin-top: 12px; border-top: 1px solid rgba(59,130,246,0.1); padding-top: 10px; text-align: center;">
                                        <a href="{{ route('trip-tickets.print', $ticket->id) }}" target="_blank" style="font-size: 11px; font-weight: 800; color: #2563eb; text-decoration: underline;">
                                            📄 View Trip Ticket Voucher
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div style="text-align: center; padding: 24px 0; color: #94a3b8; font-size: 12px;">
                                <span style="font-size: 28px; display: block; margin-bottom: 8px;">🚗</span>
                                No current active trip assignments. Assignments will appear here once GSO approves your request.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Guidelines -->
                <div class="card-panel">
                    <div class="card-header">
                        Request Procedures
                    </div>
                    <div class="card-body" style="font-size: 12px; color: #475569; line-height: 1.6;">
                        <ol style="padding-left: 20px; margin: 0;">
                            <li style="margin-bottom: 6px;">Submit request at least **3 days before** the travel date.</li>
                            <li style="margin-bottom: 6px;">Review selected date to avoid busy vehicles.</li>
                            <li style="margin-bottom: 6px;">GSO will assign the driver and create the Trip Ticket.</li>
                            <li>Download and print the **Approved Request Slip** for your clearance.</li>
                        </ol>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-filament-panels::page>
