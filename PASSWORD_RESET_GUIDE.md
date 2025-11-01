# Password Reset Implementation Guide

## Files Created/Modified

### 1. Vue Components

#### ForgotPassword.vue
Location: `resources/js/components/ForgotPassword.vue`
- Modern card design with car key icon
- Email input field
- Success/error message display
- Loading state during API call
- Links back to login page

#### ResetPassword.vue
Location: `resources/js/components/ResetPassword.vue`
- Modern card design with shield-lock icon
- Displays user's email (read-only)
- New password input with toggle visibility
- Confirm password input with toggle visibility
- Password validation (minimum 6 characters)
- Success/error message display
- Extracts token and email from URL query parameters
- Links back to login page

### 2. Backend Controller

#### PasswordResetController.php
Location: `app/Http/Controllers/PasswordResetController.php`

**Methods:**
- `forgotPassword(Request $request)` - Sends reset link to email
- `resetPassword(Request $request)` - Resets password with token

### 3. Routes

#### API Routes (routes/api.php)
```php
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
```

#### Frontend Routes (resources/js/router.js)
```javascript
{ path: '/forgot-password', name: 'ForgotPassword', component: ForgotPassword }
{ path: '/reset-password', name: 'ResetPassword', component: ResetPassword }
```

### 4. CSS Styles

Added to `resources/css/app.css`:
- `.success-message-auth` - Green success message box
- `.password-requirements` - Info box for password rules
- `.auth-btn:disabled` - Disabled button state

### 5. Login.vue Update
- Changed "Mot de passe oublié ?" link from `#` to `/forgot-password`

## User Flow

### 1. Forgot Password Flow
1. User clicks "Mot de passe oublié ?" on login page
2. Redirected to `/forgot-password`
3. User enters their email
4. Clicks "Envoyer le lien"
5. API sends reset link to email
6. Success message displayed

### 2. Reset Password Flow
1. User clicks link in email (e.g., `http://yourapp.com/reset-password?token=abc123&email=user@example.com`)
2. ResetPassword component extracts token and email from URL
3. User enters new password and confirmation
4. Password validated (match check, minimum length)
5. Clicks "Réinitialiser le mot de passe"
6. API validates token and updates password
7. Success message with link to login

## Laravel Setup Required

### 1. Database Migration
Laravel needs a `password_resets` or `password_reset_tokens` table.

Run this if not exists:
```bash
php artisan make:migration create_password_reset_tokens_table
```

The migration should look like:
```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

Then run:
```bash
php artisan migrate
```

### 2. Mail Configuration

Update `.env` file with your mail settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Password Reset Email Template (Optional)

Laravel has default templates, but you can customize:
```bash
php artisan vendor:publish --tag=laravel-mail
```

Then edit: `resources/views/vendor/mail/html/message.blade.php`

## API Endpoints

### POST /api/forgot-password
**Request:**
```json
{
  "email": "user@example.com"
}
```

**Success Response (200):**
```json
{
  "message": "Un lien de réinitialisation a été envoyé à votre email."
}
```

**Error Response (404):**
```json
{
  "message": "Aucun compte trouvé avec cet email."
}
```

### POST /api/reset-password
**Request:**
```json
{
  "token": "abc123...",
  "email": "user@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Success Response (200):**
```json
{
  "message": "Votre mot de passe a été réinitialisé avec succès."
}
```

**Error Response (400):**
```json
{
  "message": "Le lien de réinitialisation est invalide ou a expiré."
}
```

## Testing

### Manual Testing Steps:

1. **Test Forgot Password:**
   - Go to `/login`
   - Click "Mot de passe oublié ?"
   - Enter a valid email
   - Check email inbox for reset link

2. **Test Reset Password:**
   - Click the link in email
   - Should redirect to `/reset-password?token=...&email=...`
   - Enter new password (minimum 6 characters)
   - Confirm password must match
   - Click "Réinitialiser le mot de passe"
   - Should see success message
   - Click "Se connecter maintenant"
   - Login with new password

### Error Scenarios to Test:

1. Invalid email in forgot password form
2. Non-existent email
3. Expired reset token
4. Password mismatch
5. Password too short (less than 6 characters)
6. Invalid or missing token in URL

## Security Features

1. **Token Expiration:** Laravel password reset tokens expire after 60 minutes by default
2. **Single Use:** Tokens are deleted after successful password reset
3. **Email Verification:** Reset link sent only to registered email
4. **Password Confirmation:** User must enter password twice
5. **Minimum Length:** Password must be at least 6 characters
6. **HTTPS Recommended:** Always use HTTPS in production

## Customization Options

### Change Token Expiration Time
In `config/auth.php`:
```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60, // Change this value (minutes)
        'throttle' => 60,
    ],
],
```

### Change Minimum Password Length
In Vue components, update:
```javascript
minlength="6"  // Change this
```

In Laravel controller, update:
```php
'password' => 'required|min:6|confirmed'  // Change min:6
```

## Troubleshooting

### Email Not Sending:
1. Check `.env` mail configuration
2. Verify SMTP credentials
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test with `php artisan tinker`:
   ```php
   Mail::raw('Test', function($msg) {
       $msg->to('test@example.com')->subject('Test');
   });
   ```

### Token Invalid Error:
1. Check if migration ran: `php artisan migrate:status`
2. Verify table exists: Check `password_reset_tokens` table
3. Token might be expired (default: 60 minutes)

### Route Not Found:
1. Clear route cache: `php artisan route:clear`
2. Check if route exists: `php artisan route:list | grep password`

## Additional Notes

- The implementation uses Laravel's built-in `Password` facade
- Email templates can be customized using Laravel's mailable classes
- Consider adding rate limiting to prevent abuse
- In production, use a proper mail service (SendGrid, Mailgun, etc.)
- Consider adding CAPTCHA to forgot password form for security

## Next Steps

1. Configure mail settings in `.env`
2. Test the complete flow
3. Customize email templates if needed
4. Add rate limiting (optional)
5. Set up proper mail service for production
