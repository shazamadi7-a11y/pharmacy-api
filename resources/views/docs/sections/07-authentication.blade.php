<h2>5. Authentication</h2>

<p>The API uses <strong>Laravel Sanctum</strong> for token-based authentication. Tokens are issued on login/register and must be included in the <span class="inline-code">Authorization</span> header for protected endpoints.</p>

<h3>5.1 Sanctum Token Flow</h3>

<div class="lifecycle-box">
1. User registers or logs in<br>
&nbsp;&nbsp;&nbsp;&nbsp;|<br>
2. Server creates a personal access token<br>
&nbsp;&nbsp;&nbsp;&nbsp;|<br>
3. Token returned in response: "token": "1|abc123..."<br>
&nbsp;&nbsp;&nbsp;&nbsp;|<br>
4. Client includes token in all subsequent requests:<br>
&nbsp;&nbsp;&nbsp;Authorization: Bearer 1|abc123...<br>
&nbsp;&nbsp;&nbsp;&nbsp;|<br>
5. On logout, the token is revoked (deleted)
</div>

<div class="info-box">
    <strong>Authentication Header:</strong> All protected endpoints require:
    <div class="code-block">Authorization: Bearer {your-token}</div>
</div>

<h3>5.2 Email Verification</h3>

<p>When a user registers, a verification email is sent. The email contains a signed URL. The flow is:</p>

<ol>
    <li><strong>Register:</strong> <span class="inline-code">POST /api/v1/register</span> &mdash; sends verification email automatically</li>
    <li><strong>Verify:</strong> <span class="inline-code">POST /api/v1/email/verify/{id}/{hash}</span> &mdash; verifies the email (requires signed URL)</li>
    <li><strong>Resend:</strong> <span class="inline-code">POST /api/v1/email/resend</span> &mdash; resends the verification email (rate limited: 6/min)</li>
</ol>

<div class="warning-box">
    <strong>Note:</strong> In development, emails are written to <span class="inline-code">storage/logs/laravel.log</span> since MAIL_MAILER=log. Check the log file for verification links.
</div>

<h3>5.3 OTP Phone Verification</h3>

<p>Users can verify their phone number using a 6-digit OTP code:</p>

<ol>
    <li><strong>Send OTP:</strong> <span class="inline-code">POST /api/v1/otp/send</span> &mdash; generates a 6-digit code (valid for 10 minutes)</li>
    <li><strong>Verify OTP:</strong> <span class="inline-code">POST /api/v1/otp/verify</span> &mdash; verifies the code and sets <span class="inline-code">phone_verified_at</span></li>
</ol>

<p>OTP codes are stored in the <span class="inline-code">otp_codes</span> table and delivered via SMS using <strong>Twilio</strong>. The notification is sent to both the database channel (for in-app notifications) and the Twilio SMS channel (for phone delivery). Configure <span class="inline-code">TWILIO_ACCOUNT_SID</span>, <span class="inline-code">TWILIO_AUTH_TOKEN</span>, and <span class="inline-code">TWILIO_VERIFY_SID</span> in your <span class="inline-code">.env</span> file to enable SMS delivery. When Twilio credentials are not configured, OTP codes are generated locally and returned in the API response (debug mode).</p>

<h3>5.4 Password Reset</h3>

<ol>
    <li><strong>Request reset:</strong> <span class="inline-code">POST /api/v1/forgot-password</span> with <span class="inline-code">{"email": "..."}</span></li>
    <li><strong>Reset password:</strong> <span class="inline-code">POST /api/v1/reset-password</span> with email, token, new password, and confirmation</li>
</ol>

<p>The reset token is sent via email and is single-use.</p>

<div class="page-break"></div>
