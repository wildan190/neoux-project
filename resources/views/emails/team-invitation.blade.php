<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #f9fafb;
            padding: 24px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .header h1 {
            margin: 0;
            color: #111827;
            font-size: 24px;
            font-weight: 700;
        }

        .content {
            padding: 32px;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            line-height: 1.5;
            color: #4b5563;
            margin-bottom: 24px;
        }

        .button {
            display: inline-block;
            background-color: #ec6a2d;
            color: #ffffff;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #d95e26;
        }

        .footer {
            padding: 24px;
            background-color: #f9fafb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Join Our Team</h1>
        </div>
        <div class="content">
            <p><strong>{{ $invitation->company->name }}</strong> has invited you to join their team as a
                <strong>{{ ucfirst($invitation->role) }}</strong> on NeoUX Platform.</p>
            <p>Click the button below to accept the invitation and get started.</p>
            <a href="{{ route('team.accept-invitation', $invitation->token) }}" class="button">Accept Invitation</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NeoUX. All rights reserved.</p>
        </div>
    </div>
</body>

</html>