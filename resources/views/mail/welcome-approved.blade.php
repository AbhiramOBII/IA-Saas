<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Intelligent Accelerators</title>
</head>
<body style="margin:0;padding:0;background:#F8FAFC;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0"
                       style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.07);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#3F51B5;padding:32px 40px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:36px;height:36px;background:#5C6BC0;border-radius:10px;text-align:center;vertical-align:middle;">
                                        <span style="color:#ffffff;font-size:18px;font-weight:bold;">&#9889;</span>
                                    </td>
                                    <td style="padding-left:12px;">
                                        <span style="color:#ffffff;font-size:15px;font-weight:600;letter-spacing:.3px;">Intelligent Accelerators</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px;">
                            <p style="margin:0 0 6px;font-size:22px;font-weight:700;color:#0F172A;">
                                You're approved, {{ $user->name }}! 🎉
                            </p>
                            <p style="margin:0 0 28px;font-size:15px;color:#64748B;line-height:1.6;">
                                Your account has been reviewed and approved. You can now download
                                and start using the Intelligent Accelerators desktop application.
                            </p>

                            {{-- Download button --}}
                            <table cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td style="background:#3F51B5;border-radius:10px;padding:14px 28px;">
                                        <a href="{{ $downloadUrl }}"
                                           style="color:#ffffff;font-size:15px;font-weight:600;text-decoration:none;display:block;">
                                            Download the Application
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Credentials reminder --}}
                            <div style="background:#F1F5F9;border-radius:12px;padding:20px;margin-bottom:24px;">
                                <p style="margin:0 0 8px;font-size:12px;font-weight:600;color:#94A3B8;text-transform:uppercase;letter-spacing:1px;">
                                    Your Login Credentials
                                </p>
                                <p style="margin:0 0 4px;font-size:14px;color:#0F172A;">
                                    <strong>Email:</strong> {{ $user->email }}
                                </p>
                                <p style="margin:0;font-size:13px;color:#64748B;">
                                    Use the password you set during registration.
                                </p>
                            </div>

                            <p style="margin:0;font-size:13px;color:#94A3B8;line-height:1.6;">
                                If you need help getting started, contact us at
                                <a href="mailto:support@ia-platform.com"
                                   style="color:#3F51B5;text-decoration:none;">support@ia-platform.com</a>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 40px 32px;border-top:1px solid #F1F5F9;">
                            <p style="margin:0;font-size:12px;color:#CBD5E1;text-align:center;">
                                &copy; {{ date('Y') }} Intelligent Accelerators. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
