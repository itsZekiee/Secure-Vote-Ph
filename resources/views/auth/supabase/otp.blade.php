<!DOCTYPE html>
<html>

<head>
    <title>Verify OTP</title>
</head>

<body>
    <h1>OTP Verification</h1>

    @if ($errors->any())
        <ul style="color:red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <input type="text" name="token" placeholder="Enter 6-digit code" required>
        <button type="submit">Verify</button>
    </form>
</body>

</html>