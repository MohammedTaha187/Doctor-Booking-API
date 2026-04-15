<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Instructions</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .token-box {
            background-color: #f1f5f9;
            border: 2px dashed #3b82f6;
            padding: 20px;
            margin: 30px 0;
            font-size: 32px;
            font-weight: bold;
            color: #1e40af;
            letter-spacing: 10px;
            border-radius: 8px;
        }
        .footer {
            background-color: #f8fafc;
            color: #64748b;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Doctor Booking API</h1>
        </div>
        <div class="content">
            <h2>Reset Your Password</h2>
            <p>We received a request to reset your password. Use the security code below to proceed:</p>
            
            <div class="token-box">
                {{ $token }}
            </div>
            
            <p>This code will expire in 60 minutes. If you did not request a password reset, please ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Doctor Booking Platform. All rights reserved.
        </div>
    </div>
</body>
</html>
