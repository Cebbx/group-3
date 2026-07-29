<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Vehicle Dispatch & Fleet Management System</title>
        
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Tailwind CSS (via CDN for maximum compatibility and modern rendering) -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Outfit', 'sans-serif'],
                            display: ['Space Grotesk', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#f0f9ff',
                                100: '#e0f2fe',
                                500: '#0ea5e9', // Steel Blue
                                600: '#0284c7',
                                700: '#0369a1',
                            }
                        }
                    }
                }
            }
        </script>
        
        <style>
            body {
                background: radial-gradient(circle at top left, #0b0f19, #02040a 90%);
            }
            .grid-overlay {
                background-image: radial-gradient(rgba(14, 165, 233, 0.08) 1.5px, transparent 1.5px);
                background-size: 32px 32px;
            }
            .glass {
                background: rgba(15, 23, 42, 0.55);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.03);
                position: relative;
                overflow: hidden;
            }
            /* Card Shine Sweep Effect */
            .glass::before {
                content: '';
                position: absolute;
                top: 0;
                left: -150%;
                width: 100%;
                height: 100%;
                background: linear-gradient(
                    90deg,
                    transparent,
                    rgba(255, 255, 255, 0.05),
                    transparent
                );
                transition: 0.6s;
            }
            .glass:hover::before {
                left: 150%;
            }
            .glass-hover:hover {
                background: rgba(15, 23, 42, 0.75);
                border-color: rgba(14, 165, 233, 0.25);
                box-shadow: 0 20px 40px -15px rgba(14, 165, 233, 0.2);
                transform: translateY(-6px);
            }
            .text-glow {
                text-shadow: 0 0 30px rgba(14, 165, 233, 0.6);
            }
            /* Keyframe Animations for Floating Orbs */
            @keyframes float-orb-1 {
                0% { transform: translate(0px, 0px) scale(1); }
                50% { transform: translate(40px, -60px) scale(1.15); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            @keyframes float-orb-2 {
                0% { transform: translate(0px, 0px) scale(1); }
                50% { transform: translate(-50px, 50px) scale(1.2); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            @keyframes pulse-slow {
                0%, 100% { opacity: 0.4; }
                50% { opacity: 0.8; }
            }
            .animate-orb-1 {
                animation: float-orb-1 20s infinite alternate ease-in-out;
            }
            .animate-orb-2 {
                animation: float-orb-2 25s infinite alternate ease-in-out;
            }
            .animate-pulse-slow {
                animation: pulse-slow 4s infinite ease-in-out;
            }
        </style>
    </head>
    <body class="text-stone-100 min-h-screen flex flex-col justify-between antialiased selection:bg-brand-500 selection:text-white overflow-x-hidden relative">
        
        <!-- Techy Grid Background Overlay -->
        <div class="absolute inset-0 grid-overlay -z-20 pointer-events-none opacity-80"></div>
        
        <!-- Dynamic Glowing & Floating Background Orbs -->
        <div class="absolute top-[-10%] left-[10%] w-[500px] h-[500px] bg-brand-500/15 rounded-full blur-[120px] -z-10 pointer-events-none animate-orb-1"></div>
        <div class="absolute bottom-[10%] right-[10%] w-[600px] h-[600px] bg-indigo-500/10 rounded-full blur-[140px] -z-10 pointer-events-none animate-orb-2"></div>
        <div class="absolute top-[40%] left-[50%] -translate-x-1/2 w-[350px] h-[350px] bg-cyan-500/5 rounded-full blur-[100px] -z-10 pointer-events-none animate-pulse-slow"></div>

        <!-- Header -->
        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center shadow-lg shadow-brand-500/10 border border-brand-500/20">
                    <img src="/csu-logo.png" alt="CSU Logo" class="w-full h-full object-contain" />
                </div>
                <div>
                    <span class="font-display font-bold text-lg tracking-wide uppercase bg-gradient-to-r from-stone-100 to-stone-400 bg-clip-text text-transparent">PeliCle</span>
                    <span class="text-[10px] block text-stone-500 tracking-widest uppercase font-semibold">VEHICLE & TRIP</span>
                </div>
            </div>
            <div class="text-sm font-medium text-stone-400">
                System Status: <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-semibold border border-emerald-500/20"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Online</span>
            </div>
        </header>

        <!-- Main Hero Section -->
        <main class="w-full max-w-6xl mx-auto px-6 py-4 flex-grow flex flex-col justify-around items-center z-10">
            
            <!-- Hero Title -->
            <div class="text-center max-w-3xl mb-4 flex flex-col items-center">
                <!-- Logo with glowing background ring -->
                <div class="relative mb-4 group">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-brand-500 to-indigo-500 blur-md opacity-40 group-hover:opacity-75 transition-opacity duration-300"></div>
                    <img src="/csu-lallo-clean.png" alt="CSU Lal-lo Campus Logo" class="w-28 h-28 object-contain rounded-full border border-sky-400/30 shadow-2xl relative z-10 transform group-hover:scale-105 transition-transform duration-300 bg-slate-950/80 p-0.5" />
                </div>
                
                <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-3 leading-tight">
                    <span class="bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-400 bg-clip-text text-transparent text-glow">Pelicle Portal Access</span>
                </h1>
                <p class="text-stone-300 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed font-light">
                    Welcome to the PeliCle Vehicle & Trip Management System. Please select your portal below to log in and get started.
                </p>
            </div>

            <!-- Portal Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-6xl mb-8">
                
                <!-- Admin Control Panel -->
                <a href="/admin" class="glass glass-hover p-6 rounded-[1.5rem] transition-all duration-300 flex flex-col justify-between group">
                    <!-- Top accent line -->
                    <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-indigo-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-all duration-300 border border-indigo-500/20 group-hover:bg-indigo-500/20">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold font-display text-stone-100 group-hover:text-indigo-400 transition-colors duration-300 mb-2">Admin Panel</h2>
                        <p class="text-xs text-stone-400 leading-relaxed mb-4">
                            Approve request schedules, assign drivers/vehicles, dispatch trip tickets, and track detailed fleet analytics charts.
                        </p>
                    </div>
                    <div class="flex items-center text-xs font-semibold text-brand-500 gap-1 group-hover:translate-x-2 transition-transform duration-300">
                        Access Control Center 
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </a>

                <!-- Employee Portal -->
                <a href="/employee" class="glass glass-hover p-6 rounded-[1.5rem] transition-all duration-300 flex flex-col justify-between group">
                    <!-- Top accent line -->
                    <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-teal-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-all duration-300 border border-teal-500/20 group-hover:bg-teal-500/20">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold font-display text-stone-100 group-hover:text-teal-400 transition-colors duration-300 mb-2">Employee Portal</h2>
                        <p class="text-xs text-stone-400 leading-relaxed mb-4">
                            Request university vehicles, specify passenger lists, select travel details, and track your reservation status live.
                        </p>
                    </div>
                    <div class="flex items-center text-xs font-semibold text-brand-500 gap-1 group-hover:translate-x-2 transition-transform duration-300">
                        Request a Vehicle 
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </a>

                <!-- Driver Portal -->
                <a href="/driver" class="glass glass-hover p-6 rounded-[1.5rem] transition-all duration-300 flex flex-col justify-between group">
                    <!-- Top accent line -->
                    <div class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-brand-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-brand-500/10 text-brand-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-all duration-300 border border-brand-500/20 group-hover:bg-brand-500/20">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold font-display text-stone-100 group-hover:text-brand-500 transition-colors duration-300 mb-2">Driver Portal</h2>
                        <p class="text-xs text-stone-400 leading-relaxed mb-4">
                            View assigned trips, request fuel/cash withdrawal slips, and present your secure QR code for gate check-out.
                        </p>
                    </div>
                    <div class="flex items-center text-xs font-semibold text-brand-500 gap-1 group-hover:translate-x-2 transition-transform duration-300">
                        Driver Sign In 
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </a>

            </div>

            <!-- Security Guard Scanner Link -->
            <div class="mt-2 mb-8 text-center z-10">
                <a href="/guard/scanner" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 hover:bg-slate-900/90 text-xs font-semibold text-slate-400 hover:text-emerald-400 transition-all shadow-lg hover:shadow-emerald-950/20">
                    <svg class="w-4 h-4 text-emerald-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Campus Security Guard Scanner Portal
                </a>
            </div>

            <!-- Stats Overview Banner -->
            <div class="glass w-full max-w-6xl rounded-2xl p-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold font-display bg-gradient-to-r from-brand-400 to-blue-400 bg-clip-text text-transparent">4</span>
                    <span class="block text-[10px] text-stone-500 uppercase tracking-wider font-semibold mt-1">Active Vehicles</span>
                </div>
                <div class="border-l border-stone-800">
                    <span class="text-xl sm:text-2xl font-extrabold font-display bg-gradient-to-r from-brand-400 to-blue-400 bg-clip-text text-transparent">5</span>
                    <span class="block text-[10px] text-stone-500 uppercase tracking-wider font-semibold mt-1">Real Drivers</span>
                </div>
                <div class="border-l border-stone-800">
                    <span class="text-xl sm:text-2xl font-extrabold font-display bg-gradient-to-r from-brand-400 to-blue-400 bg-clip-text text-transparent">100%</span>
                    <span class="block text-[10px] text-stone-500 uppercase tracking-wider font-semibold mt-1">SMS Logs Alerted</span>
                </div>
                <div class="border-l border-stone-800">
                    <span class="text-xl sm:text-2xl font-extrabold font-display bg-gradient-to-r from-brand-400 to-blue-400 bg-clip-text text-transparent">QR</span>
                    <span class="block text-[10px] text-stone-500 uppercase tracking-wider font-semibold mt-1">Verification Code</span>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="w-full max-w-7xl mx-auto px-6 py-8 border-t border-stone-900 text-center text-xs text-stone-500 flex flex-col gap-2 justify-center items-center z-10">
            <p>&copy; {{ date('Y') }} Cagayan State University Lal-lo Campus. All rights reserved.</p>
            <p class="font-display opacity-50">PeliCle System</p>
        </footer>

    </body>
</html>
