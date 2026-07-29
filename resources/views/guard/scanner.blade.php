<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CSU Lal-lo - Security QR Scanner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top left, #0b111e, #020408 100%);
        }
        
        /* Forces the camera video feed to scale correctly without black letterbox bars */
        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 1.5rem !important;
        }
        
        /* Hides default helper text/links injected by the html5-qrcode library */
        #reader img { display: none !important; }
        #reader span { display: none !important; }
        #reader a { display: none !important; }
        
        .scanner-laser {
            position: absolute;
            left: 6%;
            right: 6%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
            box-shadow: 0 0 12px #10b981, 0 0 20px rgba(16, 185, 129, 0.5);
            animation: scan 2.5s ease-in-out infinite;
            z-index: 10;
        }
        
        @keyframes scan {
            0% { top: 10%; }
            50% { top: 90%; }
            100% { top: 10%; }
        }
        
        /* Shake animation for wrong pin */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex items-center justify-center antialiased">

    <!-- Responsive Main Container: Full screen on mobile, elegant card on desktop -->
    <div class="w-full md:max-w-md min-h-screen md:min-h-[640px] md:h-[680px] bg-slate-900/10 md:bg-slate-900/40 md:backdrop-blur-2xl md:border md:border-slate-800/80 md:rounded-3xl p-5 sm:p-6 md:shadow-2xl relative overflow-hidden flex flex-col justify-between">
        
        <!-- Ambient Glow Effects (Visible on desktop view card) -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none hidden md:block"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none hidden md:block"></div>

        <!-- 1. PIN LOCK OVERLAY SCREEN -->
        <div id="pin-screen" style="background-color: #0b0f19;" class="absolute inset-0 z-50 flex flex-col justify-between p-6 sm:p-8 transition-opacity duration-300 {{ $isVerified ? 'hidden pointer-events-none' : '' }}">
            
            <!-- Exit button -->
            <div class="flex justify-start">
                <a href="/" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors py-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Portals
                </a>
            </div>

            <!-- PIN Header & Dots Info -->
            <div class="text-center my-auto py-4">
                <div class="w-14 h-14 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-[0_0_30px_rgba(59,130,246,0.1)]">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="text-base font-bold text-white tracking-tight uppercase tracking-wider">Gate Security verification</h2>
                <p class="text-xs text-slate-400 mt-1">Please enter your 4-digit security PIN</p>
                
                <!-- PIN Dots indicator -->
                <div id="pin-dots" class="flex justify-center gap-5 mt-6">
                    <div class="w-3.5 h-3.5 rounded-full border border-slate-800 bg-slate-950/60 transition-all duration-200"></div>
                    <div class="w-3.5 h-3.5 rounded-full border border-slate-800 bg-slate-950/60 transition-all duration-200"></div>
                    <div class="w-3.5 h-3.5 rounded-full border border-slate-800 bg-slate-950/60 transition-all duration-200"></div>
                    <div class="w-3.5 h-3.5 rounded-full border border-slate-800 bg-slate-950/60 transition-all duration-200"></div>
                </div>
                <p id="pin-error" class="text-xs text-red-400 mt-4 h-4 font-semibold"></p>
            </div>

            <!-- KEYPAD (Optimized sizing & padding for narrow phone screens) -->
            <div class="grid grid-cols-3 gap-y-3.5 gap-x-5 max-w-[260px] mx-auto mb-4">
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="1">1</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="2">2</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="3">3</button>
                
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="4">4</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="5">5</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="6">6</button>
                
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="7">7</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="8">8</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="9">9</button>
                
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-950/40 border border-transparent flex items-center justify-center text-[10px] font-bold tracking-wider text-slate-500 hover:text-slate-400 active:scale-90 transition-all" data-val="C">CLEAR</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-900/60 hover:bg-slate-800/80 active:bg-slate-700/50 border border-slate-800/40 flex items-center justify-center text-xl font-bold text-slate-100 active:scale-90 transition-all" data-val="0">0</button>
                <button class="keypad-btn w-14 h-14 rounded-full bg-slate-950/40 border border-transparent flex items-center justify-center text-lg font-bold text-slate-500 hover:text-slate-400 active:scale-90 transition-all" data-val="B">⌫</button>
            </div>
            
            <div class="text-center text-[9px] text-slate-600 font-bold uppercase tracking-widest mt-1">
                Secure Pin Entry
            </div>
        </div>

        <!-- 2. MAIN SCANNER SCREEN CONTAINER -->
        <div id="scanner-screen" class="flex-1 flex flex-col justify-between {{ $isVerified ? '' : 'hidden' }}">
            
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-850 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <h1 class="text-sm font-bold text-white leading-tight">Security Gate Portal</h1>
                        <p class="text-[10px] text-slate-400 leading-none mt-0.5">CSU Lal-lo Campus Check-out</p>
                    </div>
                </div>
                <a href="/" class="text-[10px] bg-slate-950 border border-slate-850 px-3 py-1.5 rounded-lg text-slate-400 hover:text-white font-semibold transition-colors">
                    Exit
                </a>
            </div>

            <!-- Scanner Viewport Area -->
            <div class="flex-1 flex flex-col items-center justify-center my-auto py-2">
                <div class="scanner-container w-full aspect-square max-w-[280px] mx-auto bg-slate-950/60 rounded-3xl border border-slate-800/60 overflow-hidden relative shadow-2xl">
                    
                    <!-- Target Brackets Overlay -->
                    <div class="absolute inset-0 z-20 pointer-events-none flex items-center justify-center">
                        <div class="w-48 h-48 border border-white/5 rounded-3xl relative">
                            <!-- Top-Left Corner -->
                            <div class="absolute -top-1 -left-1 w-6 h-6 border-t-[4px] border-l-[4px] border-emerald-500 rounded-tl-xl"></div>
                            <!-- Top-Right Corner -->
                            <div class="absolute -top-1 -right-1 w-6 h-6 border-t-[4px] border-r-[4px] border-emerald-500 rounded-tr-xl"></div>
                            <!-- Bottom-Left Corner -->
                            <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-[4px] border-l-[4px] border-emerald-500 rounded-bl-xl"></div>
                            <!-- Bottom-Right Corner -->
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-[4px] border-r-[4px] border-emerald-500 rounded-br-xl"></div>
                            
                            <!-- Pulsing Center Indicator -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-25">
                                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Scanner Animation Laser Line -->
                    <div id="laser" class="scanner-laser {{ $isVerified ? '' : 'hidden' }}"></div>

                    <!-- Camera stream container -->
                    <div id="reader" class="w-full h-full"></div>

                    <!-- Loading State / Permission Prompt -->
                    <div id="placeholder-prompt" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-slate-950/90 z-30 {{ $isVerified ? '' : 'hidden' }}">
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center mb-3 shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-white mb-1">Camera Permission Required</p>
                        <p class="text-[10px] text-slate-400 mb-4 max-w-[200px] leading-relaxed">Please grant camera permissions to allow scanning QR codes on driver smartphones.</p>
                        <button id="start-camera-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl text-[10px] uppercase tracking-wider transition-all shadow-lg shadow-emerald-950/50">
                            Allow Camera
                        </button>
                    </div>
                </div>

                <!-- Camera selector dropdown (styled with premium dark select) -->
                <div class="w-full max-w-[280px] mt-4 hidden" id="camera-select-container">
                    <label for="camera-select" class="block text-[9px] uppercase font-bold tracking-wider text-slate-500 mb-1">Select Active Camera</label>
                    <select id="camera-select" class="w-full bg-slate-950 border border-slate-800/80 text-slate-300 text-xs rounded-xl p-2.5 focus:outline-none focus:border-emerald-500 transition-colors"></select>
                </div>
            </div>

            <!-- Footer / Status Info -->
            <div class="mt-4 border-t border-slate-850 pt-4 flex flex-col items-center">
                <div class="inline-flex items-center gap-2 bg-slate-950/80 border border-slate-850 px-4 py-2 rounded-full text-xs font-semibold text-slate-300 shadow-md">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-600 animate-pulse" id="status-indicator"></span>
                    <span id="status-text">Scanner Ready</span>
                </div>
                
                <p class="text-[9px] text-slate-600 uppercase tracking-widest mt-4 font-bold">PeliCle Trip Management System</p>
            </div>

        </div>

    </div>

    <!-- Html5Qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Keypad & PIN functionality
            const pinScreen = document.getElementById('pin-screen');
            const pinDots = document.getElementById('pin-dots').children;
            const pinError = document.getElementById('pin-error');
            const keypadBtns = document.querySelectorAll('.keypad-btn');
            
            let enteredPin = "";
            let isVerified = {{ $isVerified ? 'true' : 'false' }};
 
            keypadBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const val = this.getAttribute('data-val');
                    
                    if (val === 'C') {
                        enteredPin = "";
                    } else if (val === 'B') {
                        enteredPin = enteredPin.slice(0, -1);
                    } else if (enteredPin.length < 4) {
                        enteredPin += val;
                    }
                    
                    updatePinDots();
                    
                    if (enteredPin.length === 4) {
                        verifyPinCode(enteredPin);
                    }
                });
            });
 
            function updatePinDots() {
                for (let i = 0; i < 4; i++) {
                    if (i < enteredPin.length) {
                        pinDots[i].classList.add('bg-emerald-500', 'border-emerald-500', 'scale-110');
                        pinDots[i].classList.remove('bg-slate-900/60', 'border-slate-800');
                    } else {
                        pinDots[i].classList.remove('bg-emerald-500', 'border-emerald-500', 'scale-110');
                        pinDots[i].classList.add('bg-slate-900/60', 'border-slate-800');
                    }
                }
            }
 
            function verifyPinCode(pin) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('{{ route("guard.verify-pin") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ pin: pin })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Correct PIN
                        pinScreen.classList.add('opacity-0');
                        setTimeout(() => {
                            pinScreen.classList.add('hidden');
                            // Show scanner screen
                            const scannerScreen = document.getElementById('scanner-screen');
                            scannerScreen.classList.remove('hidden');
                            isVerified = true;
                            // Initialize camera automatically after pin verify
                            initScanner();
                        }, 300);
                    } else {
                        // Wrong PIN
                        document.getElementById('pin-dots').classList.add('shake');
                        pinError.innerText = data.message;
                        enteredPin = "";
                        setTimeout(() => {
                            document.getElementById('pin-dots').classList.remove('shake');
                            updatePinDots();
                        }, 400);
                    }
                })
                .catch(() => {
                    pinError.innerText = "Network error! Please try again.";
                    enteredPin = "";
                    updatePinDots();
                });
            }
 
            // Scanner functionality
            const startBtn = document.getElementById('start-camera-btn');
            const placeholder = document.getElementById('placeholder-prompt');
            const laser = document.getElementById('laser');
            const statusInd = document.getElementById('status-indicator');
            const statusText = document.getElementById('status-text');
            const cameraSelect = document.getElementById('camera-select');
            const cameraSelectContainer = document.getElementById('camera-select-container');
 
            let html5QrCode = null;
            let currentCameraId = null;
 
            // Trigger scanner initialization
            startBtn.addEventListener('click', function() {
                initScanner();
            });
 
            // Auto-trigger if already verified
            if (isVerified) {
                initScanner();
            }
 
            function initScanner() {
                placeholder.classList.add('hidden');
                laser.classList.remove('hidden');
                
                statusInd.classList.remove('bg-slate-600', 'bg-red-500', 'bg-blue-500');
                statusInd.classList.add('bg-emerald-500');
                statusText.innerText = "Initializing Camera...";
 
                // Get list of cameras
                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
                        cameraSelect.innerHTML = '';
                        devices.forEach(device => {
                            const opt = document.createElement('option');
                            opt.value = device.id;
                            opt.text = device.label || `Camera ${cameraSelect.length + 1}`;
                            cameraSelect.appendChild(opt);
                        });
 
                        let defaultCamera = devices[0].id;
                        // Prefer rear camera for scanning codes
                        const backCam = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment') || d.label.toLowerCase().includes('rear') || d.label.toLowerCase().includes('cobalt'));
                        if (backCam) {
                            defaultCamera = backCam.id;
                        }
 
                        cameraSelect.value = defaultCamera;
                        currentCameraId = defaultCamera;
 
                        if (devices.length > 1) {
                            cameraSelectContainer.classList.remove('hidden');
                        }
 
                        startScanning(currentCameraId);
                    } else {
                        showError("No cameras found on this device.");
                    }
                }).catch(err => {
                    showError("Camera permission denied.");
                    placeholder.classList.remove('hidden');
                });
            }
 
            function startScanning(cameraId) {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        launchCamera(cameraId);
                    }).catch(() => {
                        launchCamera(cameraId);
                    });
                } else {
                    launchCamera(cameraId);
                }
            }
 
            function launchCamera(cameraId) {
                html5QrCode = new Html5Qrcode("reader");
                const config = { 
                    fps: 15, 
                    qrbox: function(width, height) {
                        const size = Math.min(width, height) * 0.75;
                        return { width: size, height: size };
                    }
                };
 
                html5QrCode.start(
                    cameraId, 
                    config, 
                    (decodedText) => {
                        // Success scanning
                        statusInd.classList.remove('bg-emerald-500');
                        statusInd.classList.add('bg-blue-500');
                        statusText.innerText = "Ticket detected! Redirecting...";
                        
                        // Parse path to prevent cross-domain session loss
                        let path = "/";
                        try {
                            const parsedUrl = new URL(decodedText);
                            path = parsedUrl.pathname;
                        } catch (e) {
                            // Fallback if not a valid absolute URL
                            if (decodedText.includes('/trip-tickets/')) {
                                const idx = decodedText.indexOf('/trip-tickets/');
                                path = decodedText.substring(idx);
                            }
                        }

                        const targetUrl = window.location.origin + path;
                        
                        // Stop scanning and redirect using Guard's current domain
                        html5QrCode.stop().then(() => {
                            window.location.href = targetUrl;
                        }).catch(() => {
                            window.location.href = targetUrl;
                        });
                    },
                    (errorMessage) => {
                        // Silent verbose logs to keep scan feed light
                    }
                ).then(() => {
                    statusInd.classList.remove('bg-emerald-500');
                    statusInd.classList.add('bg-emerald-400');
                    statusText.innerText = "Scanning Active...";
                }).catch(err => {
                    showError("Camera feed connection failed.");
                });
            }
 
            cameraSelect.addEventListener('change', function() {
                currentCameraId = this.value;
                startScanning(currentCameraId);
            });
 
            function showError(msg) {
                statusInd.classList.remove('bg-slate-600', 'bg-emerald-500', 'bg-emerald-400', 'bg-blue-500');
                statusInd.classList.add('bg-red-500');
                statusText.innerText = msg;
                laser.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
