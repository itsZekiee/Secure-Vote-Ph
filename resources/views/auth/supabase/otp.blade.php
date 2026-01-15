<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity - SecureVote PH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .otp-input:focus {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-4">
    <div class="max-w-[480px] w-full">
        <!-- Logo/Header -->
        <div class="text-center mb-10">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-100 mb-4">
                <i class="ri-shield-check-fill text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Identity Verification</h1>
            <p class="text-[11px] text-slate-500 font-bold tracking-widest uppercase mt-1">Protecting your democratic
                voice</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
            <div class="p-8 sm:p-12">
                <div class="mb-8">
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">Check your email</h2>
                    <p class="text-sm text-slate-500 font-medium mt-2 leading-relaxed">
                        We've sent a 8-digit verification code to <span
                            class="text-indigo-600 font-bold">{{ session('otp_email') }}</span>.
                        Please enter it below to continue.
                    </p>
                </div>

                @if($errors->any())
                    <div
                        class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3 text-rose-600">
                        <i class="ri-error-warning-fill text-lg mt-0.5"></i>
                        <div class="text-[11px] font-bold uppercase tracking-wider leading-relaxed">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ $verify_route ?? route('otp.verify') }}" class="space-y-8">
                    @csrf

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Verification
                            Code</label>
                        <div class="relative group">
                            <i
                                class="ri-key-2-line absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors text-xl"></i>
                            <input type="text" name="token" placeholder="00000000" maxlength="8" required autofocus
                                class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-14 py-4 text-xl font-black tracking-[0.5em] text-slate-700 focus:bg-white focus:border-indigo-600/20 focus:ring-4 focus:ring-indigo-500/5 transition-all outline-none placeholder:text-slate-300 placeholder:tracking-normal">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-slate-900 text-white rounded-2xl py-5 font-black text-xs tracking-[0.2em] uppercase hover:bg-indigo-600 hover:shadow-2xl hover:shadow-indigo-200 transition-all active:scale-[0.98]">
                        Verify Account
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                    <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest mb-4">Didn't receive the
                        code?</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <form method="POST" action="{{ route('otp.resend') }}">
    @csrf
    <button type="submit"
        class="text-indigo-600 font-black text-[10px] uppercase tracking-widest hover:text-indigo-700 transition-colors">
        Resend Code
    </button>
</form>

                        <span class="hidden sm:block w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                        <a href="{{ $back_route ?? route('home') }}"
                            class="text-slate-500 font-black text-[10px] uppercase tracking-widest hover:text-slate-700 transition-colors">
                            Back to Login
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-8 py-4 text-center">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.1em]">
                    <i class="ri-lock-fill mr-1"></i> End-to-end encrypted verification
                </p>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center mt-8 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
            &copy; 2026 SecureVote PH. All rights reserved.
        </p>
    </div>
</body>

</html>