<x-filament-panels::page>
    <style>
        .phone-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px 0;
            background: #f8fafc;
        }
        .dark .phone-wrapper {
            background: #0f172a;
        }
        
        /* Realistic Phone Container */
        .phone-device {
            position: relative;
            width: 340px;
            height: 660px;
            background: #ffffff;
            border: 12px solid #1e293b;
            border-radius: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .dark .phone-device {
            background: #090d16;
            border-color: #334155;
        }

        /* Phone Camera Notch */
        .phone-notch {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 140px;
            height: 22px;
            background: #1e293b;
            border-radius: 0 0 16px 16px;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dark .phone-notch {
            background: #334155;
        }
        .phone-speaker {
            width: 45px;
            height: 3px;
            background: #475569;
            border-radius: 999px;
            margin-bottom: 2px;
        }

        /* Top Status Bar */
        .phone-status-bar {
            padding-top: 24px;
            padding-bottom: 8px;
            padding-left: 20px;
            padding-right: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            z-index: 10;
        }
        .dark .phone-status-bar {
            color: #cbd5e1;
        }

        /* Screen Area */
        .phone-screen {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f1f5f9;
            overflow: hidden;
            position: relative;
        }
        .dark .phone-screen {
            background: #020617;
        }

        /* Chat Room Header */
        .phone-chat-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        .dark .phone-chat-header {
            background: #0f172a;
            border-bottom-color: #1e293b;
        }
        .header-back {
            color: #3b82f6;
            background: transparent;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .header-title-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .header-title {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
        }
        .dark .header-title {
            color: #ffffff;
        }
        .header-sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 1px;
        }

        /* Message Logs Area */
        .phone-chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Messages Timestamp */
        .msg-timestamp {
            text-align: center;
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            margin: 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Chat Bubble */
        .chat-bubble-container {
            display: flex;
            align-items: flex-start;
            max-width: 85%;
            align-self: flex-start;
        }
        .chat-bubble {
            background: #e2e8f0;
            color: #0f172a;
            padding: 10px 14px;
            border-radius: 18px 18px 18px 4px;
            font-size: 12px;
            line-height: 1.5;
            white-space: pre-wrap;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border: 1px solid #cbd5e1;
        }
        .dark .chat-bubble {
            background: #1e293b;
            color: #cbd5e1;
            border-color: #334155;
        }

        /* Empty state */
        .chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #64748b;
            padding: 24px;
        }
        .chat-empty p {
            font-size: 11px;
            margin-top: 8px;
            line-height: 1.4;
        }

        /* Phone Footer Input */
        .phone-footer {
            background: #ffffff;
            padding: 10px 12px;
            border-t: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dark .phone-footer {
            background: #0f172a;
            border-top-color: #1e293b;
        }
        .footer-input-mock {
            flex: 1;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 11px;
            color: #94a3b8;
        }
        .dark .footer-input-mock {
            background: #020617;
            border-color: #1e293b;
            color: #475569;
        }
        .btn-send-mock {
            width: 28px;
            height: 28px;
            background: #3b82f6;
            border-radius: 59px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: none;
        }

        /* Screen Bottom Home Indicator */
        .phone-home-indicator {
            position: absolute;
            bottom: 6px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: #1e293b;
            border-radius: 999px;
            z-index: 50;
        }
        .dark .phone-home-indicator {
            background: #e2e8f0;
        }

        /* Slide-down active notification banner */
        .notification-banner {
            margin: 8px;
            padding: 12px;
            background: rgba(15, 23, 42, 0.95);
            color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 40;
            font-size: 11px;
        }
        .notification-title {
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            color: #ffffff;
        }
        .notification-body {
            color: #e2e8f0;
            line-height: 1.3;
        }
    </style>

    <div class="phone-wrapper">
        
        <!-- Phone Device Mockup -->
        <div class="phone-device">
            
            <!-- Phone Notch Speaker -->
            <div class="phone-notch">
                <div class="phone-speaker"></div>
            </div>

            <!-- Top Status Bar -->
            <div class="phone-status-bar">
                <span>9:41</span>
                <div style="display: flex; align-items: center; gap: 4px;">
                    <span>📶</span>
                    <span>WiFi</span>
                    <span>🔋 100%</span>
                </div>
            </div>

            <!-- Screen Container -->
            <div class="phone-screen">
                
                <!-- Notification Banner (Incoming SMS simulation) -->
                @php
                    $latestLog = $this->getSmsLogs()->last();
                @endphp
                @if($latestLog)
                    <div class="notification-banner">
                        <div class="notification-title">
                            <span>💬 Message Alert</span>
                            <span style="opacity: 0.6; font-size: 9px;">now</span>
                        </div>
                        <div style="font-weight: 800; font-size: 11px; margin-bottom: 2px;">PeliCle System</div>
                        <div class="notification-body">
                            @php
                                $lines = explode("\n", $latestLog->message);
                                $firstLine = isset($lines[2]) ? $lines[2] : $latestLog->message;
                                $secondLine = isset($lines[3]) ? $lines[3] : '';
                                echo e($firstLine . ($secondLine ? ' - ' . $secondLine : ''));
                            @endphp
                        </div>
                    </div>
                @endif

                <!-- Chat Room Header -->
                <div class="phone-chat-header">
                    <button class="header-back">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="header-title-container">
                        <span class="header-title">PeliCle System</span>
                        <span class="header-sub">+63 917 000 0000</span>
                    </div>
                    <div style="width: 16px;"></div> <!-- Spacer -->
                </div>

                <!-- Chat Messages Body -->
                <div class="phone-chat-body">
                    @forelse($this->getSmsLogs() as $log)
                        <!-- Timestamp -->
                        <div class="msg-timestamp">
                            {{ $log->created_at->format('M d, Y, h:i A') }}
                        </div>

                        <!-- Chat Bubble -->
                        <div class="chat-bubble-container">
                            <div class="chat-bubble">
                                {{ $log->message }}
                            </div>
                        </div>
                    @empty
                        <!-- Empty Chat Simulation -->
                        <div class="chat-empty">
                            <span style="font-size: 32px; margin-bottom: 8px;">💬</span>
                            <p style="font-weight: 800; font-size: 12px; color: #475569;">No Messages Yet</p>
                            <p>Simulated SMS notifications for your trip assignments will appear here once GSO creates a new trip ticket.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Phone Input Footer -->
                <div class="phone-footer">
                    <div class="footer-input-mock">
                        Text Message
                    </div>
                    <button class="btn-send-mock">
                        <svg style="width: 14px; height: 14px; transform: rotate(90deg);" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                    </button>
                </div>

            </div>

            <!-- Home Screen Indicator Bar -->
            <div class="phone-home-indicator"></div>

        </div>

    </div>
</x-filament-panels::page>
