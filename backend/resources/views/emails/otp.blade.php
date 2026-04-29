<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduWrap OTP</title>
</head>
<body style="margin:0;padding:0;background:#f4f0ff;font-family:Segoe UI,Arial,sans-serif;color:#1f1235;">
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f4f0ff;padding:24px 10px;">
    <tr>
      <td align="center">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;background:#ffffff;border-radius:20px;overflow:hidden;border:1px solid #eee7ff;">
          <tr>
            <td style="background:linear-gradient(135deg,#6d28d9 0%,#9333ea 55%,#ea580c 100%);padding:22px 24px;">
              <div style="font-size:12px;letter-spacing:1.2px;font-weight:700;color:#f8f5ff;text-transform:uppercase;">EduWrap Security</div>
              <div style="font-size:26px;line-height:32px;font-weight:800;color:#ffffff;margin-top:8px;">{{ $title }}</div>
              <div style="font-size:14px;line-height:22px;color:#f8eefe;margin-top:8px;">{{ $subtitle }}</div>
            </td>
          </tr>
          <tr>
            <td style="padding:24px;">
              <p style="margin:0 0 12px 0;font-size:15px;color:#2f1f4a;">Hi {{ $name ?: 'Learner' }},</p>
              <p style="margin:0 0 18px 0;font-size:14px;line-height:22px;color:#5c4d72;">
                Use the one-time password below to proceed. This code expires in {{ $expiresInMinutes }} minutes.
              </p>

              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0 0 18px 0;">
                <tr>
                  <td align="center" style="background:#faf7ff;border:1px dashed {{ $accent }};border-radius:16px;padding:16px;">
                    <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#7b6a95;font-weight:700;margin-bottom:8px;">Your OTP Code</div>
                    <div style="font-size:36px;line-height:1.1;letter-spacing:10px;font-weight:900;color:{{ $accent }};">{{ $otp }}</div>
                  </td>
                </tr>
              </table>

              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;">
                <tr>
                  <td style="padding:12px 14px;font-size:13px;line-height:20px;color:#7c2d12;">
                    Security tip: Never share this OTP with anyone. EduWrap support will never ask for it.
                  </td>
                </tr>
              </table>

              <p style="margin:18px 0 0 0;font-size:13px;color:#7b6a95;line-height:20px;">
                If you did not request this, you can safely ignore this email.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:16px 24px;background:#fcfaff;border-top:1px solid #eee7ff;">
              <div style="font-size:12px;color:#8f7daf;line-height:18px;">
                © {{ date('Y') }} EduWrap · Smart learning, beautifully delivered.
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
