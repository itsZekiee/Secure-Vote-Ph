<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join Election - SecureVote PH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        primary: { 400: '#0ea5e9', 500: '#0891b2', 600: '#0e7490', 700: '#164e63' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(15px, -20px) rotate(3deg); }
            66% { transform: translate(-10px, -30px) rotate(-2deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #0891b2 25%, #06b6d4 50%, #10b981 75%, #34d399 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .floating-orb {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(16, 185, 129, 0.1));
            backdrop-filter: blur(40px);
            animation: float 20s ease-in-out infinite;
            box-shadow: 0 0 60px rgba(14, 165, 233, 0.3);
        }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(30px) saturate(180%);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            box-shadow:
                0 20px 60px -15px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(14, 165, 233, 0.1) inset;
        }

        .card-enter { animation: fadeIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1); }

        .code-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            border: 2px solid #99f6e4;
        }

        .code-input:focus {
            transform: translateY(-2px) scale(1.05);
            box-shadow:
                0 8px 25px -8px rgba(20, 184, 166, 0.4),
                0 0 0 4px rgba(20, 184, 166, 0.15);
            background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%);
            border-color: #14b8a6;
        }

        .code-input.filled {
            background: linear-gradient(135deg, #99f6e4 0%, #5eead4 100%);
            border-color: #14b8a6;
            color: #0f766e;
            font-weight: 800;
            box-shadow: 0 4px 15px -5px rgba(20, 184, 166, 0.5);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0891b2 50%, #06b6d4 100%);
            background-size: 200% auto;
            box-shadow:
                0 10px 30px -10px rgba(14, 165, 233, 0.6),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover:not(:disabled) {
            background-position: right center;
            transform: translateY(-2px) scale(1.02);
            box-shadow:
                0 20px 50px -10px rgba(14, 165, 233, 0.8),
                0 0 30px rgba(6, 182, 212, 0.4);
        }

        .btn-primary:disabled {
            background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
            box-shadow: none;
            cursor: not-allowed;
        }

        .progress-bar {
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(90deg, #14b8a6, #10b981, #34d399);
            box-shadow: 0 0 20px rgba(20, 184, 166, 0.6);
        }

        .link-input-container::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4, #10b981);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: -1;
        }

        .link-input-container:focus-within::before {
            opacity: 1;
            box-shadow: 0 0 30px rgba(14, 165, 233, 0.4);
        }

        .icon-glow {
            box-shadow: 0 0 25px rgba(20, 184, 166, 0.5);
        }

        @media (max-width: 640px) {
            .code-input { width: 38px; height: 46px; font-size: 1.125rem; }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-3 sm:p-4 relative overflow-hidden">
<!-- Enhanced Floating Orbs -->
<div class="floating-orb w-64 h-64 -top-20 -left-20 sm:w-96 sm:h-96 sm:-top-28 sm:-left-28" style="animation-delay: 0s;"></div>
<div class="floating-orb w-80 h-80 -bottom-32 -right-32 sm:w-[500px] sm:h-[500px] sm:-bottom-40 sm:-right-40" style="animation-delay: 2s;"></div>
<div class="floating-orb w-56 h-56 top-1/4 right-1/4 sm:w-80 sm:h-80" style="animation-delay: 4s;"></div>

<div class="relative z-10 w-full max-w-xs sm:max-w-sm card-enter">
    <!-- Main Card -->
    <div class="glass-morphism rounded-2xl p-4 sm:p-6">
        <!-- Header with Gradient Icon -->
        <div class="text-center mb-4 sm:mb-5">
            <div class="relative inline-flex items-center justify-center mb-3">
                <div class="relative w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-cyan-500 via-teal-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg icon-glow">
                    <i class="fas fa-vote-yea text-white text-xl sm:text-2xl"></i>
                </div>
                <div class="absolute -bottom-0 -right-0 w-5 h-5 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                    <i class="fas fa-check text-white text-[8px]"></i>
                </div>
            </div>
            <h1 class="text-xl sm:text-2xl font-black bg-gradient-to-r from-cyan-600 via-teal-600 to-emerald-600 bg-clip-text text-transparent mb-1">Join Election</h1>
            <p class="text-gray-600 text-[11px] sm:text-xs font-medium">Enter your code to vote securely</p>
        </div>

        <!-- Enhanced Security Badge -->
        <div class="flex items-center justify-center gap-1.5 mb-4 sm:mb-5 py-1.5 px-3 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 border-2 border-emerald-300 rounded-full w-fit mx-auto shadow-sm">
            <div class="w-4 h-4 bg-gradient-to-br from-emerald-500 to-teal-600 rounded flex items-center justify-center shadow-md">
                <i class="fas fa-shield-alt text-white text-[9px]"></i>
            </div>
            <span class="text-emerald-700 text-[10px] font-bold">SSL Encrypted</span>
        </div>

        <!-- Form -->
        <form action="{{ route('voter.elections.verify') }}" method="POST" id="joinForm" class="space-y-3 sm:space-y-4">
            @csrf

            <!-- Code Input Section -->
            <div id="codeSection">
                <label class="block text-[11px] sm:text-xs font-bold bg-gradient-to-r from-cyan-700 to-teal-700 bg-clip-text text-transparent mb-2.5 sm:mb-3 text-center">
                    6-Digit Election Code
                </label>

                <!-- Code Inputs -->
                <div class="flex justify-center gap-1 sm:gap-1.5 mb-2.5 sm:mb-3">
                    @for($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            maxlength="1"
                            class="code-input w-9 h-11 sm:w-11 sm:h-13 text-center text-base sm:text-lg font-bold rounded-lg outline-none"
                            pattern="[A-Za-z0-9]"
                            required
                        >
                    @endfor
                </div>

                <input type="hidden" name="election_code" id="electionCode">
                <input type="hidden" name="input_type" id="inputType" value="code">

                <!-- Enhanced Progress Bar -->
                <div class="relative w-full h-1 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full overflow-hidden mb-3">
                    <div id="progressBar" class="progress-bar absolute h-full w-0 rounded-full"></div>
                </div>
            </div>

            <!-- Link Input Section -->
            <div id="linkSection" class="hidden">
                <label class="block text-[11px] sm:text-xs font-bold bg-gradient-to-r from-cyan-700 to-teal-700 bg-clip-text text-transparent mb-2.5 sm:mb-3">
                    Election Link
                </label>
                <div class="link-input-container relative">
                    <input
                        type="url"
                        name="election_link"
                        id="electionLink"
                        class="w-full py-2.5 sm:py-3 px-3 sm:px-4 border-2 border-cyan-200 rounded-lg text-xs sm:text-sm outline-none transition-all duration-300 bg-gradient-to-r from-cyan-50 to-teal-50"
                        placeholder="https://securevote.ph/election/..."
                    >
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="p-2.5 sm:p-3 bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-300 rounded-lg flex items-start gap-2 shadow-sm">
                    <i class="fas fa-exclamation-circle text-red-500 text-sm mt-0.5"></i>
                    <div class="flex-1">
                        @foreach($errors->all() as $error)
                            <p class="text-red-700 text-[11px] sm:text-xs font-semibold">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Enhanced Submit Button -->
            <button
                type="submit"
                id="submitBtn"
                class="btn-primary w-full py-2.5 sm:py-3 px-4 rounded-lg sm:rounded-xl text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 relative overflow-hidden disabled:cursor-not-allowed"
                disabled
            >
                <span>Continue to Vote</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-3 sm:my-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t-2 border-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-2 bg-white text-gray-500 text-[10px] font-semibold">or</span>
            </div>
        </div>

        <!-- Enhanced Toggle Button -->
        <button
            type="button"
            id="toggleInput"
            class="w-full py-2 sm:py-2.5 px-3 border-2 border-cyan-300 hover:border-cyan-500 rounded-lg sm:rounded-xl text-cyan-700 hover:text-cyan-800 font-semibold text-[11px] sm:text-xs transition-all duration-300 flex items-center justify-center gap-1.5 bg-gradient-to-r from-white to-cyan-50 group hover:shadow-lg"
        >
            <i class="fas fa-link text-xs transition-transform group-hover:scale-110 text-cyan-600" id="toggleIcon"></i>
            <span id="toggleText">Use Election Link Instead</span>
        </button>

        <!-- Enhanced Help Section -->
        <div class="mt-3 sm:mt-4 p-2.5 sm:p-3 bg-gradient-to-br from-cyan-50 via-teal-50 to-emerald-50 rounded-lg border-2 border-cyan-200 shadow-sm">
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 sm:w-7 sm:h-7 bg-gradient-to-br from-cyan-500 to-teal-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-md">
                    <i class="fas fa-info text-white text-[10px] sm:text-xs"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-cyan-900 text-[10px] sm:text-xs font-bold mb-0.5">Need Help?</h4>
                    <p class="text-cyan-700 text-[9px] sm:text-[10px] leading-relaxed">
                        Check your email or contact your election administrator for your unique code.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Back Link -->
    <div class="mt-3 text-center">
        <a href="{{ route('home') }}" class="text-white/95 hover:text-white text-[11px] sm:text-xs font-semibold transition-all duration-300 inline-flex items-center gap-1.5 group bg-white/15 backdrop-blur-md px-3 py-1.5 rounded-lg hover:bg-white/25 shadow-lg hover:shadow-xl border border-white/20">
            <i class="fas fa-arrow-left text-[10px] group-hover:-translate-x-1 transition-transform"></i>
            <span>Back to Home</span>
        </a>
    </div>

    <!-- Footer -->
    <div class="mt-2 text-center">
        <p class="text-white/80 text-[9px] sm:text-[10px] font-medium drop-shadow-md">
            SecureVote PH © {{ date('Y') }} •
            <a href="#" class="hover:text-white transition-colors underline">Privacy</a> •
            <a href="#" class="hover:text-white transition-colors underline">Terms</a>
        </p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const codeInputs = document.querySelectorAll('.code-input');
        const progressBar = document.getElementById('progressBar');
        const codeSection = document.getElementById('codeSection');
        const linkSection = document.getElementById('linkSection');
        const toggleBtn = document.getElementById('toggleInput');
        const toggleText = document.getElementById('toggleText');
        const toggleIcon = document.getElementById('toggleIcon');
        const electionCode = document.getElementById('electionCode');
        const electionLink = document.getElementById('electionLink');
        const inputType = document.getElementById('inputType');
        const submitBtn = document.getElementById('submitBtn');
        let isCodeMode = true;

        function updateProgress() {
            let filledCount = 0;
            codeInputs.forEach(input => {
                if (input.value) {
                    filledCount++;
                    input.classList.add('filled');
                } else {
                    input.classList.remove('filled');
                }
            });
            progressBar.style.width = (filledCount / 6 * 100) + '%';
            submitBtn.disabled = filledCount !== 6;
        }

        function updateHiddenCode() {
            electionCode.value = Array.from(codeInputs).map(i => i.value).join('');
            updateProgress();
        }

        codeInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
                if (e.target.value && index < 5) codeInputs[index + 1].focus();
                updateHiddenCode();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
                pastedData.split('').forEach((char, i) => {
                    if (codeInputs[i]) codeInputs[i].value = char;
                });
                updateHiddenCode();
                if (pastedData.length === 6) codeInputs[5].focus();
            });

            input.addEventListener('focus', function() { this.select(); });
        });

        electionLink.addEventListener('input', function() {
            submitBtn.disabled = !this.value.trim();
        });

        toggleBtn.addEventListener('click', function() {
            isCodeMode = !isCodeMode;
            if (isCodeMode) {
                codeSection.classList.remove('hidden');
                linkSection.classList.add('hidden');
                toggleText.textContent = 'Use Election Link Instead';
                toggleIcon.className = 'fas fa-link text-xs transition-transform group-hover:scale-110 text-cyan-600';
                inputType.value = 'code';
                setTimeout(() => codeInputs[0].focus(), 100);
                updateProgress();
            } else {
                codeSection.classList.add('hidden');
                linkSection.classList.remove('hidden');
                toggleText.textContent = 'Use Election Code Instead';
                toggleIcon.className = 'fas fa-keyboard text-xs transition-transform group-hover:scale-110 text-cyan-600';
                inputType.value = 'link';
                setTimeout(() => electionLink.focus(), 100);
                submitBtn.disabled = !electionLink.value.trim();
            }
        });

        codeInputs[0].focus();
    });
</script>
</body>
</html>
