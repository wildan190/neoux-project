<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome to Prodexa</title>
</head>

<body style="background-color:#f5f5f5; padding:30px; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:12px; overflow:hidden;">

                    <!-- HEADER -->
                    <tr>
                        <td style="background:#6366F1; padding:40px; text-align:center; color:white;">
                            <h1 style="margin:0; font-size:32px; font-weight:bold;">
                                Welcome to Prodexa!
                            </h1>
                            <p style="margin-top:10px; font-size:16px; opacity:0.9;">
                                Your Smart Production Management System
                            </p>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:40px;">

                            <p style="font-size:16px; color:#333;">
                                Hi <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="font-size:15px; color:#555; line-height:1.6;">
                                We're excited to welcome you to <strong>Prodexa</strong> —
                                the modern platform designed to simplify and optimize your manufacturing operations.
                            </p>

                            <p style="font-size:15px; color:#555; line-height:1.6;">
                                With Prodexa, you can now manage production workflows, track inventory, monitor
                                purchasing,
                                and view real-time performance dashboards — all in one unified system.
                            </p>

                            <p style="font-size:15px; color:#555; line-height:1.6;">
                                To get started, simply log in and explore the dashboard.
                                If you need help, our support team is always ready to assist you.
                            </p>

                            <div style="text-align:center; margin:40px 0;">
                                <a href="{{ url('/login') }}" style="
                                       background:#6366F1;
                                       padding:14px 32px;
                                       border-radius:8px;
                                       color:white;
                                       font-size:16px;
                                       text-decoration:none;
                                       font-weight:bold;
                                    ">
                                    Go to Dashboard
                                </a>
                            </div>

                            <p style="font-size:14px; color:#888; line-height:1.6;">
                                Thanks for choosing Prodexa.<br>
                                We’re committed to helping your business grow through efficiency and automation.
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#f0f0f0; padding:20px; text-align:center; font-size:12px; color:#777;">
                            © {{ date('Y') }} Prodexa — All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>

</html>