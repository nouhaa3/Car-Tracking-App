# EMAIL CONFIGURATION GUIDE
# =========================

## Option 1: Gmail (Recommended for Testing)
# ------------------------------------------
# 1. Go to your Google Account settings
# 2. Enable 2-Factor Authentication
# 3. Generate an App Password:
#    - Go to: https://myaccount.google.com/apppasswords
#    - Select "Mail" and "Windows Computer"
#    - Copy the 16-character password
# 4. Add to .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Car Tracking App"

## Option 2: Mailtrap (Recommended for Development)
# --------------------------------------------------
# 1. Sign up at: https://mailtrap.io
# 2. Go to Email Testing > Inboxes > My Inbox
# 3. Copy SMTP credentials
# 4. Add to .env:

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@cartracking.test
MAIL_FROM_NAME="Car Tracking App"

## Option 3: SendGrid (Recommended for Production)
# -------------------------------------------------
# 1. Sign up at: https://sendgrid.com
# 2. Create an API Key
# 3. Add to .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Car Tracking App"

## Option 4: Microsoft Outlook/Office 365
# ----------------------------------------
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@outlook.com
MAIL_FROM_NAME="Car Tracking App"

## Testing Email Configuration
# -----------------------------
# After updating .env, test with:

php artisan tinker
Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Or use the contact form and send a reply to test the full flow!

## Troubleshooting
# ----------------
# - Clear config cache: php artisan config:clear
# - Check logs: storage/logs/laravel.log
# - Verify firewall allows outbound SMTP connections
# - For Gmail: Make sure "Less secure app access" is ON (if not using App Password)
