{{-- resources/views/auth/voter-verify-otp.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - SecureVotePH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">SecureVotePH</a>
    </div>
</nav>

<div class="container mt-5" style="max-width: 500px;">
    <h3 class="mb-3">Email Verification</h3>

    <p class="text-muted">
        We sent a 6-digit verification code to:<br>
        <strong>{{ session('otp_email') }}</strong>
    </p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('voter.otp.verify') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Verification Code</label>
            <input
                type="text"
                name="token"
                class="form-control text-center"
                placeholder="Enter 8-digit code"
                maxlength="8"
                required
                autofocus
            >
        </div>

        <button class="btn btn-primary w-100">
            Verify & Continue
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
