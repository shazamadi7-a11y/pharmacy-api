<h2>3. Environment Variables</h2>

<p>The following environment variables are configured in the <span class="inline-code">.env</span> file. Copy <span class="inline-code">.env.example</span> and update values as needed.</p>

<table>
    <thead>
        <tr>
            <th style="width: 30%;">Variable</th>
            <th style="width: 30%;">Value</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($envVars as $var)
        <tr>
            <td><span class="inline-code">{{ $var['name'] }}</span></td>
            <td><span class="inline-code">{{ $var['value'] }}</span></td>
            <td>{{ $var['description'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h3>Cloudinary Configuration</h3>

<div class="info-box">
    <strong>Image Storage:</strong> All category and medicine images are uploaded to Cloudinary. The following credentials are pre-configured for the development environment:
</div>

<div class="code-block"># Cloudinary credentials (already in .env)
CLOUDINARY_CLOUD_NAME=dn0r6dwxe
CLOUDINARY_API_KEY=421443189959716
CLOUDINARY_API_SECRET=Js_R17UdxaVTdZcRKRCTNw9kJTI
CLOUDINARY_FOLDER=pharmacy-app</div>

<p>Images are uploaded to the <span class="inline-code">pharmacy-app/</span> folder on Cloudinary. Category images go to <span class="inline-code">pharmacy-app/categories/</span> and medicine images go to <span class="inline-code">pharmacy-app/medicines/</span>.</p>

<h3>Twilio SMS Configuration</h3>

<div class="info-box">
    <strong>SMS OTP Delivery:</strong> OTP verification codes are sent via SMS using the Twilio API. You must configure the following credentials to enable SMS delivery:
</div>

<div class="code-block"># Twilio Verify credentials (required for OTP)
TWILIO_ACCOUNT_SID=your-twilio-account-sid
TWILIO_AUTH_TOKEN=your-twilio-auth-token
TWILIO_VERIFY_SID=your-twilio-verify-service-sid</div>

<p>Sign up at <strong>twilio.com</strong> for API credentials. The free trial includes $15.50 in credits with no credit card required. Create a Verify Service at <strong>console.twilio.com/us1/develop/verify/services</strong>. Twilio supports Palestinian (+970), Egyptian (+20), Saudi (+966), UAE (+971), and Jordanian (+962) phone numbers.</p>

<h3>Mail Configuration</h3>

<p>In development, mail is set to the <span class="inline-code">log</span> driver, which writes emails to <span class="inline-code">storage/logs/laravel.log</span> instead of actually sending them. For production, switch to <span class="inline-code">smtp</span> and configure your mail provider.</p>

<div class="code-block"># Development (default)
MAIL_MAILER=log

# Production example
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@yourpharmacy.com</div>

<div class="page-break"></div>
