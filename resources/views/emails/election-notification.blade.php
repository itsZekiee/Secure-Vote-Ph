<!DOCTYPE html>
<html>
<head>
    <title>Election Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
        <h2 style="color: #4f46e5;">Election Notification</h2>
        <p>Hello {{ $voterName }},</p>

        @if($type === 'reminder')
            <p>This is a reminder that the election <strong>{{ $election->title }}</strong> is scheduled to start in 24 hours.</p>
            <p>Start Date: {{ $election->start_date->format('M d, Y h:i A') }}</p>
        @elseif($type === 'open')
            <p>The polls are now <strong>OPEN</strong> for <strong>{{ $election->title }}</strong>! You can now cast your vote.</p>
            <p>End Date: {{ $election->end_date->format('M d, Y h:i A') }}</p>
        @elseif($type === 'closed')
            <p>The polls for <strong>{{ $election->title }}</strong> are now <strong>CLOSED</strong>. Thank you for participating!</p>
        @endif

        <p>You can access the election portal here:</p>
        <p><a href="{{ route('voter.registration.index', $election->id) }}" style="background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Go to Election Portal</a></p>

        <p>Best regards,<br>The Secure-Vote-Ph Team</p>
    </div>
</body>
</html>
