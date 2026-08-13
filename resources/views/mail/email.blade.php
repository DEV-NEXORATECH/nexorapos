<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $sub }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #F4FAFF; margin:0; padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #FFFFFF; margin-top: 20px; border-radius: 10px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #0058A6; color: #FFFFFF; padding: 20px; font-size: 24px;">
                            Retail Point Of Sale
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px; color: #002E58;">
                            <h2 style="color: #0058A6;">Hello!</h2>
                            <p>{{ $msg }}</p>
                            <p>We are thrilled to have you on our platform. Stay tuned for the latest updates and exciting content!</p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #EAF5FF; padding: 20px; color: #1976C9; font-size: 14px;">
                            &copy; {{ date('Y') }} Retail Point Of Sale. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
