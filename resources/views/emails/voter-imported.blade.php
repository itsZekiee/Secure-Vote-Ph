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
    <p><strong>Voting Period:</strong></p>
    <ul>
        <li><strong>Start Time:</strong> {{ $election->start_date ? $election->start_date->format('F d, Y h:i A') : 'N/A' }}</li>
        <li><strong>End Time:</strong> {{ $election->end_date ? $election->end_date->format('F d, Y h:i A') : 'N/A' }}</li>
    </ul>
    <p><strong>Election Code:</strong> <code>{{ $election->code }}</code></p>
    <p><strong>Election Link:</strong> <a href="{{ route('voter.registration.index', $election->code) }}?mode=signin">{{ route('voter.registration.index', $election->code) }}?mode=signin</a></p>
    <p><strong>Temporary Password:</strong> <span style="font-family: monospace; font-size: 1.2em; font-weight: bold; color: #4f46e5;">{{ $tempPassword }}</span></p>

    <p>Please use the temporary password above to sign in and cast your vote during the voting period.</p>

    <p>Thank you for participating in the democratic process!</p>
    <p>Best regards,<br>The SecureVote PH Team</p>
</body>
</html>
