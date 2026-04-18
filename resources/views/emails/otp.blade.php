<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .otp-box {
            background: #f8f9fa;
            border: 2px dashed #667eea;
            padding: 20px;
            border-radius: 10px;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 10px;
            color: #667eea;
            margin: 20px 0;
            font-family: monospace;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .note {
            color: #999;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 OTP Verification</h1>
        </div>
        <div class="content">
            <h2>Hello {{ $userName }}!</h2>
            <p>Use the following OTP code to complete your action. This code will expire in 10 minutes.</p>

            <div class="otp-box">
                {{ $otp }}
            </div>

            <p>If you didn't request this code, please ignore this email.</p>

            <div class="note">
                For security reasons, never share this code with anyone.
            </div>
        </div>
        <div class="footer">
            <p>© 2026 Fitness App. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
