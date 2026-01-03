<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SecureVote PH')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        .gradient-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        }
        .gradient-accent {
            background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .glass-dark {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .input-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-modern:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px -10px rgba(99, 102, 241, 0.4);
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.5);
        }
        .floating-shapes::before {
            content: '';
            position: absolute;
            top: 10%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(139, 92, 246, 0.3));
            border-radius: 50%;
            filter: blur(60px);
            animation: float 8s ease-in-out infinite;
        }
        .floating-shapes::after {
            content: '';
            position: absolute;
            bottom: 10%;
            right: 10%;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.3), rgba(168, 85, 247, 0.3));
            border-radius: 50%;
            filter: blur(60px);
            animation: float 10s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slideUp {
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        .tab-active {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 10px 30px -5px rgba(99, 102, 241, 0.4);
        }
        .candidate-card {
            transition: all 0.3s ease;
        }
        .candidate-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
        }
        .candidate-card.selected {
            border-color: #10b981;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
        }
        .position-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">
<main class="flex-grow">
    @yield('content')
</main>

@include('voter.layouts.footer')

@stack('scripts')
</body>
</html>
