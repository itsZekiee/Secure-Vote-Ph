<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
</head>

<body>
    <h1>OTP Verification</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <label for="token">Enter OTP Code:</label>
        <input type="text" name="token" maxlength="8" pattern="\d{8}" required placeholder="Enter 8-digit code">
        <button type="submit">Verify</button>
    </form>
</body>

</html>