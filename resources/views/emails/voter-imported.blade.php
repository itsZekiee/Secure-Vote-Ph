<!DOCTYPE html>
<html>
<head>
    <title>Voting Credentials</title>
</head>
<body>
    <h3>Hello, {{ $voter->name }}!</h3>
    <p>You have been registered as a voter for the following election:</p>
    <p><strong>Election Title:</strong> {{ $election->title }}</p>
    @if($election->description)
        <p><strong>Election Description:</strong> {{ $election->description }}</p>
    @endif
    <p><strong>Voting Start Date & Time:</strong> {{ $election->start_date ? $election->start_date->format('F d, Y h:i A') : 'N/A' }}</p>
    <p><strong>Voting End Date & Time:</strong> {{ $election->end_date ? $election->end_date->format('F d, Y h:i A') : 'N/A' }}</p>
    <p><strong>Temporary Password / Unique Key:</strong> <span style="font-family: monospace; font-size: 1.2em; font-weight: bold; color: #4f46e5;">{{ $tempPassword }}</span></p>

    <p>You can access the voting portal here: <a href="{{ route('voter.access') }}">{{ route('voter.access') }}</a></p>
    <p>Enter the Election Code: <strong>{{ $election->code }}</strong> when prompted.</p>

    <p>Thank you for participating in the democratic process!</p>
    <p>Best regards,<br>The SecureVote PH Team</p>
</body>
</html>
