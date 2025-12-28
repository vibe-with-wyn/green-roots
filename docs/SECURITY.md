# Green Roots - Security Documentation

This document outlines the security features and best practices implemented in the Green Roots application.

## Table of Contents

- [Security Overview](#security-overview)
- [Authentication & Authorization](#authentication--authorization)
- [Password Security](#password-security)
- [Session Management](#session-management)
- [Database Security](#database-security)
- [Input Validation](#input-validation)
- [File Upload Security](#file-upload-security)
- [CSRF Protection](#csrf-protection)
- [XSS Protection](#xss-protection)
- [Rate Limiting](#rate-limiting)
- [Known Limitations](#known-limitations)
- [Security Best Practices](#security-best-practices)

## Security Overview

Green Roots implements multiple layers of security to protect user data and prevent common web vulnerabilities. The application follows industry-standard security practices while acknowledging areas for future improvement.

## Authentication & Authorization

### User Authentication

- **Login System**:
  - Credentials verified against database with encrypted passwords
  - Failed login tracking with account lockout
  - Session-based authentication
  - Secure session token generation

- **Role-Based Access Control (RBAC)**:
  - Three user roles: `user`, `eco_validator`, `admin`
  - Role-specific redirects after login
  - Page-level authorization checks
  - Unauthorized access redirects to appropriate pages

### Authorization Checks

Each protected page verifies:
1. User is logged in (session exists)
2. User has appropriate role for the page
3. User has permission to access specific resources

Example authorization flow:
```php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user has validator role
if ($_SESSION['role'] !== 'eco_validator') {
    header("Location: access_denied.php");
    exit;
}
```

## Password Security

### Password Hashing

- **Algorithm**: PHP's `password_hash()` with default algorithm (currently bcrypt)
- **Salt**: Automatically generated unique salt for each password
- **Cost Factor**: Default bcrypt cost (currently 10)

### Password Requirements

Users must create passwords with:
- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- At least one special character (!@#$%^&*()_+-=[]{}|;:,.<>?)

### Password Verification

- Uses `password_verify()` for secure comparison
- Constant-time comparison to prevent timing attacks
- No plain-text password storage

### Password Change Flow

1. Verify current password with `password_verify()`
2. Validate new password meets requirements
3. Confirm new password matches confirmation
4. Hash new password with `password_hash()`
5. Update database with new hash
6. Clear relevant sessions if needed

## Session Management

### Session Configuration

- **Session Start**: Initiated on every authenticated page
- **Session ID**: Regenerated on login to prevent session fixation
- **Session Storage**: Server-side (PHP default)
- **Session Lifetime**: Browser session (ends when browser closes)

### Session Security Measures

1. **Session Regeneration**:
   ```php
   session_start();
   session_regenerate_id(true); // Regenerate on login
   ```

2. **Session Variables**:
   - `user_id` - Unique user identifier
   - `username` - Current username
   - `role` - User role (user/eco_validator/admin)
   - `barangay_id` - User's barangay (for validators)

3. **Session Destruction**:
   - Complete session clearance on logout
   - All session variables unset
   - Session cookie destroyed

### Logout Security

```php
// Complete session termination
$_SESSION = [];
session_destroy();
header("Location: index.php");
exit;
```

## Database Security

### SQL Injection Prevention

- **PDO Prepared Statements**: All database queries use prepared statements
- **Parameter Binding**: User inputs bound as parameters, never concatenated
- **Named Placeholders**: Used for clarity and security

Example:
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
```

### Database Connection

- **PDO Exception Mode**: Error mode set to exceptions
- **Connection Encryption**: Uses UTF-8 charset (utf8mb4)
- **Credentials**: Stored in separate config file (`includes/config.php`)
- **Error Handling**: Database errors caught and logged without exposing details

### Transaction Management

Critical operations use transactions to ensure data integrity:
```php
try {
    $pdo->beginTransaction();
    // Multiple database operations
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    // Error handling
}
```

## Input Validation

### Server-Side Validation

All user inputs are validated on the server:

1. **Username**:
   - Non-empty
   - Alphanumeric with underscores
   - Unique in database

2. **Email**:
   - Valid email format (`FILTER_VALIDATE_EMAIL`)
   - Sanitized (`FILTER_SANITIZE_EMAIL`)
   - Unique in database

3. **Phone Number**:
   - Optional field
   - International format validation
   - Regex pattern matching

4. **Text Inputs**:
   - Non-empty where required
   - Length limits enforced
   - Special character handling

5. **Numeric Inputs**:
   - Type checking
   - Range validation (e.g., trees: 1-100)
   - Integer conversion

### Client-Side Validation

- **HTML5 Validation**: Required fields, input types, patterns
- **JavaScript Validation**: Real-time feedback, format checking
- **Character Counters**: For text areas with limits (e.g., feedback)

**Note**: Client-side validation is for user experience only. Server-side validation is mandatory for security.

## File Upload Security

### Upload Restrictions

1. **File Types**:
   - Allowed: JPEG, PNG, GIF
   - Validation via MIME type and file extension
   - Magic number verification (file signature)

2. **File Size**:
   - Profile pictures: Maximum 20MB
   - Submission photos: Maximum 10MB
   - Server-side enforcement

3. **File Validation**:
   ```php
   $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
   $max_size = 20 * 1024 * 1024; // 20MB
   
   if (!in_array($file['type'], $allowed_types)) {
       // Reject upload
   }
   
   if ($file['size'] > $max_size) {
       // Reject upload
   }
   ```

### Submission Photo Requirements

Additional security for tree planting submissions:
- Minimum resolution: 800x600 pixels
- EXIF data extraction for GPS coordinates
- Photo hash generation to prevent duplicates
- Location data verification

### Storage Security

- **Binary Storage**: Photos stored as BLOBs in database
- **PDO LOB Parameter**: Uses `PARAM_LOB` for safe storage
- **No Direct File System Access**: Prevents directory traversal
- **Base64 Encoding**: For display in HTML

## CSRF Protection

### Token Generation

- **Unique Tokens**: Generated for each form
- **Token Storage**: Stored in session
- **Token Lifetime**: Valid for session duration

Token generation:
```php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

### Token Validation

Forms include hidden CSRF tokens:
```html
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
```

Server-side validation:
```php
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed");
}
```

### Protected Forms

CSRF tokens implemented on:
- Login form
- Registration form
- Submission forms (tree planting, feedback)
- Reward redemption
- Password changes
- Account updates

## XSS Protection

### Current Implementation

- **Limited XSS Protection**: Currently under development
- **Input Sanitization**: Email sanitization with `FILTER_SANITIZE_EMAIL`
- **Output Context**: Some HTML entity encoding

### Areas for Improvement

The following XSS protections are planned for future releases:

1. **Output Encoding**:
   - HTML entity encoding for all user-generated content
   - Context-aware encoding (HTML, JavaScript, CSS, URL)
   - Use of `htmlspecialchars()` with proper flags

2. **Content Security Policy (CSP)**:
   - Restrict script sources
   - Prevent inline script execution
   - Whitelist trusted domains

3. **DOM Sanitization**:
   - Client-side input sanitization
   - DOMPurify or similar library implementation

**Current Status**: Basic sanitization exists, but comprehensive XSS mitigation is in development.

## Rate Limiting

### Login Attempts

- **Limit**: 5 failed attempts
- **Window**: 5 minutes
- **Action**: Temporary account lockout
- **Reset**: Automatic after window expires

Implementation tracking:
- Failed attempts stored in session or database
- Timestamp-based window calculation
- Clear attempts on successful login

### Cash Withdrawals

- **Limit**: 5 withdrawals per hour
- **Tracking**: Database-based timestamp checking
- **Enforcement**: Server-side validation before processing

### Feedback Submission

- **Limit**: 1 submission per 24 hours per user
- **Tracking**: Database timestamp in `feedback` table
- **Validation**: Checks `submitted_at` against current time

## Known Limitations

### Security Gaps

1. **XSS Protection**: Limited implementation; comprehensive solution pending
2. **HTTPS**: Development uses HTTP; HTTPS required for production
3. **Admin Features**: Incomplete implementation with potential security gaps
4. **Validator Features**: May contain bugs due to limited testing time
5. **Password Reset**: Not yet implemented
6. **Two-Factor Authentication**: Not implemented
7. **API Rate Limiting**: Limited to specific features

### Prototype Features

The following features are prototypes with limited security:
- **Payment Methods**: PayPal email validation only, no API integration
- **Admin Dashboard**: Partially implemented, security not fully tested

## Security Best Practices

### For Developers

1. **Always Use Prepared Statements**:
   ```php
   // Good
   $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
   $stmt->execute([$user_id]);
   
   // Bad - Never do this!
   $query = "SELECT * FROM users WHERE id = " . $user_id;
   ```

2. **Validate All Inputs**:
   - Server-side validation is mandatory
   - Never trust client-side validation alone
   - Validate data type, format, range, and content

3. **Use Password Hashing**:
   ```php
   // Hash passwords
   $hashed = password_hash($password, PASSWORD_DEFAULT);
   
   // Verify passwords
   if (password_verify($input, $hashed)) {
       // Password correct
   }
   ```

4. **Implement Proper Error Handling**:
   - Catch exceptions
   - Log errors securely
   - Display generic error messages to users
   - Never expose database structure or query details

5. **Sanitize Output**:
   ```php
   echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
   ```

### For Deployment

1. **Use HTTPS**: Essential for production environments
2. **Update Dependencies**: Keep PHP, MySQL, and libraries updated
3. **Secure Configuration**:
   - Strong database passwords
   - Restrict file permissions
   - Disable directory listing
   - Configure proper error logging

4. **Environment Variables**: Use for sensitive configuration
5. **Regular Backups**: Database and file system
6. **Security Monitoring**: Log suspicious activities
7. **Regular Security Audits**: Periodic code reviews

### For Users

1. **Strong Passwords**: Follow password requirements
2. **Logout After Use**: Especially on shared computers
3. **Report Suspicious Activity**: Use feedback system
4. **Keep Information Updated**: Maintain accurate account details

## Future Security Enhancements

### Planned Improvements

1. **Comprehensive XSS Protection**:
   - Output encoding for all user content
   - Content Security Policy implementation
   - DOM sanitization

2. **Enhanced Password Security**:
   - Password strength meter
   - Password history (prevent reuse)
   - Password expiration policy

3. **Additional Authentication**:
   - Two-factor authentication (2FA)
   - Biometric authentication support
   - OAuth integration

4. **Advanced Rate Limiting**:
   - IP-based rate limiting
   - Distributed rate limiting for scalability
   - Adaptive rate limiting based on user behavior

5. **Security Headers**:
   - Strict-Transport-Security
   - X-Frame-Options
   - X-Content-Type-Options
   - Referrer-Policy

6. **Audit Logging**:
   - Comprehensive activity logging
   - Security event monitoring
   - Intrusion detection

7. **Encrypted Communications**:
   - End-to-end encryption for sensitive data
   - Database encryption at rest
   - Encrypted file storage

## Reporting Security Issues

If you discover a security vulnerability in Green Roots:

1. **Do Not** open a public GitHub issue
2. Contact me directly via email: [lapurawendel.dev@gmail.com]
3. Provide detailed information:
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (if available)

I will:
- Acknowledge receipt as soon as possible
- Investigate and confirm the issue
- Develop and test a fix
- Release a security patch
- Credit you as the reporter (if desired)

**Note**: As a solo developer, response times may vary, but security issues are treated as high priority.

# Security Guidelines

## Pre-Deployment Security

### 1. Database Security
- Use strong database passwords
- Never commit credentials to version control
- Limit database user privileges
- Regularly backup database

### 2. File Protection
- Keep sensitive files outside `htdocs`
- Use `.htaccess` to block direct access
- Validate all file uploads
- Sanitize user inputs

### 3. Session Security
- Use secure session handling
- Implement session timeout
- Regenerate session IDs on login
- Use HTTPS only (when available)

### 4. SQL Injection Prevention
- Always use prepared statements
- Never concatenate SQL queries
- Validate and sanitize inputs
- Use parameterized queries

### 5. XSS Prevention
- Use `htmlspecialchars()` for output
- Validate input data types
- Implement Content Security Policy
- Escape JavaScript variables

## InfinityFree Specific Security

### Limitations to Consider
1. **No SSL on subdomains** - Use custom domain for SSL
2. **No .htpasswd support** - Implement application-level auth
3. **Limited file permissions** - Use application-level checks
4. **No shell access** - All management via FTP/control panel

### Recommended Practices
1. Enable CloudFlare (free SSL)
2. Use strong session management
3. Implement rate limiting in PHP
4. Log security events to database
5. Regular security audits

## Production Checklist
- [ ] Change all default passwords
- [ ] Enable error logging (not display)
- [ ] Set `$is_production = true`
- [ ] Remove debug code
- [ ] Validate all user inputs
- [ ] Implement CSRF protection
- [ ] Set secure cookie flags
- [ ] Regular backup schedule
- [ ] Monitor access logs
- [ ] Keep dependencies updated

## Security Checklist

### Pre-Deployment

- [ ] All passwords properly hashed
- [ ] CSRF tokens on all forms
- [ ] SQL injection prevention verified
- [ ] File upload validation tested
- [ ] Session security configured
- [ ] HTTPS enabled
- [ ] Error messages sanitized
- [ ] Rate limiting tested
- [ ] Database credentials secured
- [ ] Debug mode disabled
- [ ] Security headers configured

### Ongoing Maintenance

- [ ] Regular security audits
- [ ] Dependency updates
- [ ] Log monitoring
- [ ] Backup verification
- [ ] User feedback review
- [ ] Penetration testing
- [ ] Code reviews
- [ ] Security training for team

---

**Note**: This document reflects the current security implementation. For a complete system overview, see [ARCHITECTURE.md](ARCHITECTURE.md). For feature details, see [FEATURES.md](FEATURES.md).

> 🌐 **Production Instance**: [https://green-roots.is-great.net](https://green-roots.is-great.net)  
> 🔒 **Security Status**: HTTPS enabled, production-ready security measures active

## Production Security Measures

The live application at https://green-roots.is-great.net implements the following security features:

- ✅ **SSL/HTTPS encryption** for all traffic
- ✅ **Secure session management** with HTTP-only cookies
- ✅ **SQL injection protection** via PDO prepared statements
- ✅ **XSS prevention** through output sanitization
- ✅ **Password hashing** using PHP's password_hash()
- ✅ **CSRF protection** on all forms
- ✅ **Role-based access control** (User, Validator, Admin)
