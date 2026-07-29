<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - PeliCle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top, #0f172a 0%, #020617 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            max-width: 500px;
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: #ef4444;
        }
        .icon-wrapper svg {
            width: 40px;
            height: 40px;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #f8fafc 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p {
            font-size: 16px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.03);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>
        <h1>Portal Access Denied</h1>
        <p>Your current logged-in account does not have permission to view this section of the PeliCle Vehicle System.</p>
        <div class="btn-group">
            <a href="/" class="btn btn-primary">Go to Home Portals</a>
            <form action="{{ route('filament.admin.auth.logout') }}" method="POST" style="display: none;" id="logout-admin-form">
                @csrf
            </form>
            <form action="{{ route('filament.employee.auth.logout') }}" method="POST" style="display: none;" id="logout-employee-form">
                @csrf
            </form>
            <form action="{{ route('filament.driver.auth.logout') }}" method="POST" style="display: none;" id="logout-driver-form">
                @csrf
            </form>
            <button onclick="handleLogout()" class="btn btn-secondary">Switch Accounts / Sign Out</button>
        </div>
    </div>

    <script>
        function handleLogout() {
            // Trigger logout for any active panels they might be stuck in, then redirect
            const forms = ['logout-admin-form', 'logout-employee-form', 'logout-driver-form'];
            let submitted = false;
            
            // Try to find and submit the appropriate form or redirect to a global logout/home
            forms.forEach(id => {
                const form = document.getElementById(id);
                if (form) {
                    try {
                        form.submit();
                        submitted = true;
                    } catch(e) {}
                }
            });
            
            if (!submitted) {
                window.location.href = '/';
            }
        }
    </script>
</body>
</html>
