<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $sub }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin:0; padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; margin-top: 20px; border-radius: 10px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #007bff; color: white; padding: 20px; font-size: 24px;">
                            Blog Name
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px; color: #333333;">
                            <h2 style="color: #007bff;">Hello!</h2>
                            <p>{{ $msg }}</p>
                            <p>We are thrilled to have you on our blog community. Stay tuned for the latest updates and exciting content!</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f1f1f1; padding: 20px; color: #555555; font-size: 14px;">
                            &copy; {{ date('Y') }} Blog Name. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
