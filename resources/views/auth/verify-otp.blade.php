<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - SecureVotePH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">SecureVotePH</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Verify Your Email</h2>
        <p>A verification code has been sent to your email: <strong>{{ session('otp_email') }}</strong></p>

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

        <form action="{{ route('otp.verify') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="token" class="form-label">OTP Code</label>
                <input type="text" class="form-control" id="token" name="token" value="{{ old('token') }}" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
