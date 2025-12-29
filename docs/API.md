# Green Roots - API & File Functionalities

This document provides detailed technical information about each file's functionality, dependencies, and implementation details.

## Table of Contents

- [User Functionalities](#user-functionalities)
- [Validator Functionalities](#validator-functionalities)
- [Admin Functionalities](#admin-functionalities)
- [Services](#services)

## User Functionalities

### index.php (Landing Page)

**Purpose**: Welcomes users to Green Roots.

**Functionalities**:
- Displays title, "Get Started" button, navigation bar, and feature highlights.
- Uses Font Awesome icons and custom CSS.

**External Dependencies**:
- Font Awesome (CDN: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`)
- CSS: `assets/css/index.css`

---

### login.php (Login Page)

**Purpose**: Authenticates users securely.

**Functionalities**:
- Limits login attempts (5 within 5 minutes)
- Uses CSRF tokens for security
- Validates credentials with encrypted passwords
- Redirects to appropriate dashboard based on role:
  - Validators → `validator_dashboard.php`
  - Users → `dashboard.php`
  - Admins → `admin_dashboard.php`
- Animated form with placeholders for third-party logins

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Security Features**:
- Password hashing with `password_hash()`
- CSRF token validation
- Rate limiting on login attempts
- Session management

---

### register.php (Registration Page)

**Purpose**: Handles new user sign-ups.

**Functionalities**:
- Validates inputs (username, email, password, location)
- Checks for unique usernames and emails
- Encrypts passwords using `password_hash()`
- Uses AJAX for dynamic location selection (barangay, city, province, region, country)
- Stores user data in the `users` table
- Redirects to `login.php` upon successful registration

**External Dependencies**:
- Font Awesome (CDN)
- Service: `services/get_locations.php`

**Database Tables**:
- `users` - Store new user data
- `barangays`, `cities`, `provinces`, `regions`, `countries` - Location data

---

### dashboard.php (Dashboard Page)

**Purpose**: Provides a personalized user overview.

**Functionalities**:
- Checks login status, redirects to `login.php` if not authenticated
- Displays user statistics:
  - Total trees planted
  - Eco points earned
  - CO2 offset (calculated)
  - User rank in barangay
  - Upcoming events
- Uses Chart.js for data visualization
- Includes sidebar navigation
- Features search bar and notifications
- Profile dropdown with links to:
  - Account Settings
  - Profile
  - Password & Security
  - Payment Methods
  - Logout

**External Dependencies**:
- Font Awesome (CDN)
- Chart.js (CDN: `https://cdn.jsdelivr.net/npm/chart.js`)
- CSS: `assets/css/dashboard.css`

**Database Tables**:
- `users` - User data and statistics
- `submissions` - Tree planting records
- `events` - Upcoming events
- `event_participants` - User event participation

---

### submit.php (Submit Tree Planting Page)

**Purpose**: Manages tree planting submissions.

**Functionalities**:
- Validates submissions:
  - Trees: 1-100
  - Photo: JPG/PNG format, <10MB, ≥800x600 pixels
  - Location data from EXIF
- Uses CSRF tokens for security
- Checks photo EXIF data for GPS coordinates
- Generates photo hash to prevent duplicates
- Saves submission to `submissions` table with "pending" status
- Logs activity in `activities` table
- Supports offline submissions
- Drag-and-drop photo upload

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Database Tables**:
- `submissions` - Store submission data
- `activities` - Log submission activity

**Security Features**:
- CSRF protection
- File type validation
- File size limits
- EXIF data extraction for authenticity

---

### planting_site.php (Designated Planting Site Page)

**Purpose**: Displays designated planting locations for the user's barangay.

**Functionalities**:
- Retrieves planting site coordinates from `planting_sites` table
- Displays barangay-specific location
- Provides OpenStreetMap link for coordinates
- Shows message to contact admin if no site is assigned
- Responsive design with sidebar navigation

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Database Tables**:
- `planting_sites` - Planting site coordinates
- `barangays` - Barangay information
- `users` - User location data

---

### events.php (Events Page)

**Purpose**: Enables users to browse, filter, join, and manage community tree planting events with QR code tickets.

**Functionalities**:
- **Authentication**: Verifies login status, fetches user data
- **Tabs**:
  - "Upcoming Events" - All regional events
  - "My Events" - User-joined events
- **Event Browsing**: Displays events with pagination (10 per page)
- **Filtering**: By date, province, city, or barangay within user's region
- **Joining Events**:
  - AJAX request to join events
  - Checks capacity and prevents duplicates
  - Generates unique QR code (SHA-256 hash)
  - Stores in `event_participants` table
  - Logs in `activities` table
- **QR Code Tickets**: Modal with QR code for attendance verification
- **PDF Tickets**: Download styled PDF with QR code
- **Attendance Verification**: Updates `confirmed_at` when scanned
- **Event Status**: Upcoming/Ongoing/Past with visual indicators

**External Dependencies**:
- Font Awesome (CDN)
- QRCode.js (CDN: `https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js`)
- jsPDF (CDN: `https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jsPDF.umd.min.js`)
- CSS: `assets/css/events.css`

**Database Tables**:
- `events` - Event information
- `event_participants` - Participation tracking
- `activities` - Activity logging
- `barangays`, `cities`, `provinces` - Location data

---

### history.php (History Page)

**Purpose**: Displays a log of user activities with filtering and pagination.

**Functionalities**:
- **Tabs**:
  - Planting History - Submission logs
  - Event History - Event participation
  - Reward History - Redeemed rewards
- **Activity Display**:
  - Planting: Trees, location, status, date
  - Events: Title, location, status (Confirmed/Pending), dates
  - Rewards: Type, value, eco points used, date
  - Feedback: Category and submission date
- **Filtering**: Date range filtering for all tabs
- **Pagination**: 10 entries per page
- **Event Tracking**: Shows confirmation status from `event_participants`

**External Dependencies**:
- Font Awesome (CDN)
- CSS: `assets/css/history.css`

**Database Tables**:
- `activities` - All user activities
- `submissions` - Tree planting records
- `events` - Event information
- `event_participants` - Event participation
- `redeemed_rewards` - Reward redemptions

---

### feedback.php (Feedback Page)

**Purpose**: Enables users to submit feedback and view history with admin responses.

**Functionalities**:
- **Tabs**:
  - Submit Feedback
  - Feedback History
- **Submission**:
  - Category selection (bug, feature, general)
  - 1-5 star rating
  - Comments (up to 1000 characters)
  - Anonymity option
  - Real-time character counter (warns at 900+)
  - One submission per 24 hours limit
- **Validation**:
  - Valid category and rating
  - Non-empty comments (<1000 chars)
  - 24-hour cooldown check
- **History Display**:
  - Pagination (10 per page)
  - Shows: category, rating, comments, anonymity, date, status, admin responses
  - "Show More/Less" toggle for long comments
- **Status**: Submitted, Under Review, Resolved

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Database Tables**:
- `feedback` - Feedback submissions
- `activities` - Activity logging

---

### account_settings.php (Account Settings Page)

**Purpose**: Allows users to update personal account details securely.

**Functionalities**:
- **Editable Fields**:
  - Username
  - Email
  - Phone number (optional)
  - First name
  - Last name
- **Read-Only Fields** (set during registration):
  - Barangay, City, Province, Region, Country
  - Tooltips explain immutability
- **Validation**:
  - Non-empty username
  - Valid email format
  - Valid phone number (international formats)
  - Unique username/email (excluding own record)
- **Database Updates**: Updates `users` table, reflects in session
- **Navigation Tabs**: Links to Profile, Password & Security, Payment Methods

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Database Tables**:
- `users` - User account information

**Security Features**:
- PDO prepared statements
- Input sanitization (`FILTER_SANITIZE_EMAIL`)
- Unique constraint validation

---

### profile.php (Profile Page)

**Purpose**: Enables users to manage their profile picture.

**Functionalities**:
- **Display**: Shows current profile picture (custom, default from assets, or static fallback)
- **Upload**:
  - Accepts JPEG, PNG, GIF
  - Maximum 20MB file size
  - Live preview using FileReader API
  - Stores as binary data (blob) in `users.profile_picture`
- **Remove**: Sets `profile_picture` to NULL, reverts to default
- **Default Handling**: Fetches from `assets` table or uses `default_profile.jpg`

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Database Tables**:
- `users` - Profile picture storage
- `assets` - Default profile picture

**Security Features**:
- File type validation
- File size limits
- PDO prepared statements with `PARAM_LOB`

---

### password_security.php (Password & Security Page)

**Purpose**: Enables users to change their password securely.

**Functionalities**:
- **Input Fields**:
  - Current password
  - New password
  - Confirm new password
- **Validation**:
  - Current password verification
  - Password strength requirements:
    - Minimum 8 characters
    - At least one uppercase letter
    - At least one lowercase letter
    - At least one number
    - At least one special character
  - Password match confirmation
- **UI Features**:
  - Password visibility toggles
  - Real-time strength indicator (Weak/Medium/Strong)
  - Field-specific error messages
- **Database Updates**: Hashes new password with `password_hash()`, updates `users` table

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Database Tables**:
- `users` - Password storage

**Security Features**:
- `password_hash()` with bcrypt
- `password_verify()` for current password
- PDO prepared statements
- Session-based error handling

---

### payment_methods.php (Payment Methods Page, Prototype)

**Purpose**: Manages PayPal email for reward withdrawals.

**Functionalities**:
- **Display**: Shows current PayPal email or "Not set"
- **Update**: Validates and stores PayPal email
- **Remove**: Sets `paypal_email` to NULL
- **Validation**: Email format validation using `FILTER_VALIDATE_EMAIL`
- **Integration**: Required for cash withdrawals in Rewards feature

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS

**Database Tables**:
- `users` - PayPal email storage (nullable)

**Prototype Note**: Limited to PayPal email management. Future enhancements may include:
- Additional payment methods
- PayPal API integration
- Payment verification

---

### rewards.php (Rewards Page)

**Purpose**: Manages point redemption for vouchers and cash.

**Functionalities**:
- **Earning Points**: Awarded for approved submissions or event participation
- **Voucher Redemption**:
  - Browse available vouchers
  - Redeem with sufficient points
  - Receive unique code and QR code
  - 30-day expiration
  - Prevents duplicate redemptions
- **Cash Withdrawal**:
  - Conversion: 1 point = ₱0.50
  - Minimum: 100 points
  - Limit: 5 withdrawals per hour
  - Requires PayPal email (set in Payment Methods)
- **Voucher Management**:
  - View redeemed vouchers
  - QR codes for verification
  - PDF download option
  - Automatic removal of expired vouchers
- **PDF Generation**: "EcoVoucher Pass" with details and QR code

**External Dependencies**:
- Font Awesome (CDN)
- QRCode.js (CDN)
- jsPDF (CDN)
- CSS: `assets/css/rewards.css`

**Database Tables**:
- `users` - Eco points and PayPal email
- `rewards` - Available rewards
- `redeemed_rewards` - Redemption records
- `activities` - Activity logging

**Security Features**:
- CSRF tokens
- Duplicate redemption prevention
- Rate limiting on cash withdrawals

---

### leaderboard.php (Leaderboard Page)

**Purpose**: Displays community and individual rankings based on trees planted.

**Functionalities**:
- **Community Rankings**:
  - Aggregates trees per barangay
  - Real-time updates using SQL `RANK()`
  - Updates `rankings` table
- **Individual Rankings**:
  - Users ranked within their barangay
  - Badges for top positions
- **Broader Rankings**:
  - Province rankings
  - Region rankings
  - Displayed in modals
  - Search functionality
- **Display**: User, barangay, province, and region ranks

**External Dependencies**:
- Font Awesome (CDN)
- CSS: `assets/css/leaderboard.css`

**Database Tables**:
- `users` - Individual statistics
- `rankings` - Community rankings
- `barangays`, `provinces`, `regions` - Location data

---

### logout.php (Logout Page)

**Purpose**: Terminates the user's session and redirects to landing page.

**Functionalities**:
- **Session Termination**:
  - Clears all session variables (`$_SESSION = []`)
  - Destroys session using `session_destroy()`
- **Redirection**: Redirects to `index.php`
- **Security**: Prevents residual session data

**External Dependencies**: None (pure PHP)

---

## Validator Functionalities

### validator_dashboard.php (Validator Dashboard Page)

**Purpose**: Provides validators with an overview of their assigned barangay.

**Functionalities**:
- **Authentication**: Restricts to `eco_validator` role
- **Statistics Display**:
  - User count in barangay
  - Pending submissions
  - Approved submissions
  - Flagged submissions
- **Recent Submission**: Shows most recent submission with details
- **Navigation**: Sidebar links to:
  - Dashboard
  - Pending Reviews
  - Reviewed Submissions
  - Barangay Planting Site
- **Search**: Page search with autocomplete

**External Dependencies**:
- Font Awesome (CDN)
- Chart.js (CDN)
- Inline CSS

**Database Tables**:
- `users` - Validator and user data
- `submissions` - Submission statistics
- `barangays` - Barangay information

---

### pending_reviews.php (Pending Reviews Page)

**Purpose**: Allows validators to review and manage pending tree planting submissions.

**Functionalities**:
- **Authentication**: Restricts to `eco_validator` role
- **Fetching**: Retrieves pending submissions for validator's barangay
- **Search**: By username or email with persistence
- **Eco Points Calculation**:
  - Base: 50 points per tree
  - Fairness buffer: 1.2 multiplier (20%)
  - Reward multiplier: 1.1 (10%)
  - Rounded to nearest integer
- **Review Actions**:
  - **Approve**: Updates status, adds points/trees to user, logs validator ID and timestamp
  - **Reject**: Requires reason, updates status with rejection reason
- **Display**: Table with submission details, photos, locations
- **Performance Note (Photos)**: Photos are loaded on-demand via `services/submission_photo.php?submission_id=...` to avoid fetching/base64-encoding `submissions.photo_data` in list queries.
- **Pagination**: 11 items per page
- **Flagging**: Displays flag icon if flagged

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS and JavaScript

**Database Tables**:
- `submissions` - Submission data
- `users` - Submitter and validator data
- `barangays` - Location data

**Services**:
- `update_submission.php` - Handles approval/rejection
- `submission_photo.php` - Streams submission photos on-demand

---

### reviewed_submissions.php (Reviewed Submissions Page)

**Purpose**: Displays history of reviewed submissions for validator's barangay.

**Functionalities**:
- **Authentication**: Restricts to `eco_validator` role
- **Data Fetching**: Approved and rejected submissions
- **Search & Filter**:
  - Search by username or email
  - Filter by status (all, approved, rejected)
  - Persistence across pages
- **Updates**: Uses AJAX fetching for search/filter updates (no aggressive polling by default)
- **Display**: Table with submission details, photos, rejection reasons
- **Pagination**: 10 items per page
- **Photo Modal**: Clickable thumbnails to enlarge
 - **Performance Note (Photos)**: Avoids embedding base64 images or returning photo blobs over JSON; photos are streamed via `services/submission_photo.php`.

**External Dependencies**:
- Font Awesome (CDN)
- Inline CSS and JavaScript

**Database Tables**:
- `submissions` - Reviewed submissions
- `users` - Submitter data
- `barangays` - Location data

**Services**:
- `fetch_reviewed.php` - Data updates
- `submission_photo.php` - Streams submission photos on-demand

---

### barangay_designated_site.php (Barangay Designated Site Page)

**Purpose**: Displays designated planting site for validator's barangay with interactive map.

**Functionalities**:
- **Authentication**: Restricts to `eco_validator` role
- **Data Fetching**: Latest planting site coordinates
- **Map Display**:
  - Interactive Leaflet map
  - Custom marker icon
  - Popup with barangay name
  - OpenStreetMap link
- **Real-Time Updates**: AJAX polling every 5 seconds
- **Display**: Card with coordinates and last updated timestamp

**External Dependencies**:
- Font Awesome (CDN)
- Leaflet.js (CDN: `https://unpkg.com/leaflet@1.9.4/dist/leaflet.js` and CSS)
- Custom marker icon (CDN: `https://cdn-icons-png.flaticon.com/512/684/684908.png`)
- Inline CSS and JavaScript

**Database Tables**:
- `planting_sites` - Site coordinates
- `barangays` - Barangay information
- `users` - Validator data

**Services**:
- `fetch_designated_site.php` - Real-time site data

---

## Admin Functionalities

### admin_dashboard.php (Admin Dashboard Page)

**Purpose**: Intended to provide admins with full system access.

**Status**: Partially implemented. Currently includes basic user and event management. Full implementation pending due to time constraints.

---

### manage_planting_sites.php

**Status**: Pending implementation.

---

### manage_validators.php

**Status**: Pending implementation.

---

### upload_assets.php

**Status**: Pending implementation.

---

## Services

### services/get_locations.php

**Purpose**: Provides cascading location data for registration and filtering.

**Functionalities**:
- Handles AJAX requests for location data
- Supports queries for:
  - Countries
  - Regions (by country)
  - Provinces (by region)
  - Cities (by province)
  - Barangays (by city)
- Returns JSON responses

**Database Tables**:
- `countries`
- `regions`
- `provinces`
- `cities`
- `barangays`

---

### services/update_submission.php

**Purpose**: Handles submission status updates and user data updates for validator actions.

**Functionalities**:
- Accepts JSON input via POST
- Validates input (`submission_id`, `status`, `validated_by`, `validated_at`)
- Updates `submissions` table
- Updates the existing `activities` row for the submission (syncs status + eco points)
- Does not create new activity rows during validation
- For approved submissions: Updates user's `eco_points` and `trees_planted`
- Uses PDO transactions for data consistency
- Returns JSON response

**Database Tables**:
- `submissions` - Status updates
- `activities` - Status/eco points sync for submission activity
- `users` - Points and trees updates

**Security Features**:
- Transaction management
- Error handling with rollback
- Input validation

---

### services/fetch_reviewed.php

**Purpose**: Provides data for reviewed submissions via AJAX.

**Functionalities**:
- Authentication check (`eco_validator` role)
- Accepts GET parameters: `barangay_id`, `status`, `search`
- Retrieves approved/rejected submissions
- Filters by status and search query
- Calculates eco points
- Does not return photo blobs/base64 in JSON (photos are requested separately)
- Returns JSON response

**Database Tables**:
- `submissions` - Submission data
- `users` - Submitter data

---

### services/submission_photo.php

**Purpose**: Streams a submission photo blob for validator pages.

**Functionalities**:
- Restricts access to the `eco_validator` role
- Enforces barangay-level access (validator can only view photos for submissions in their barangay)
- Returns the binary image with a best-effort `Content-Type`

**Query Parameters**:
- `submission_id` (required)

---

### services/fetch_designated_site.php

**Purpose**: Provides real-time planting site data via AJAX.

**Functionalities**:
- Session validation
- Accepts `barangay_id` GET parameter
- Retrieves latest planting site coordinates
- Returns JSON response with site data
- Error handling with appropriate HTTP status codes

**Database Tables**:
- `planting_sites` - Site coordinates
- `barangays` - Barangay information

**Security Features**:
- Session validation (403 if unauthorized)
- Input validation
- Error handling (500 on failure)

---

## Common Security Patterns

All files implement the following security measures:

1. **PDO Prepared Statements** - Prevent SQL injection
2. **Password Hashing** - `password_hash()` and `password_verify()`
3. **Session Management** - Authentication and authorization
4. **CSRF Protection** - Token validation in forms
5. **Input Validation** - Type, format, and range checks
6. **File Upload Security** - Type and size validation
7. **Rate Limiting** - Login attempts, withdrawals
8. **Error Handling** - Graceful error display, no sensitive data exposure

---

**Note**: For feature descriptions, see [FEATURES.md](FEATURES.md). For security details, see [SECURITY.md](SECURITY.md).

# API Documentation

> 🌐 **Production Base URL**: https://green-roots.is-great.net

## Base URLs

- **Production**: `https://green-roots.is-great.net`
- **Local Development**: `http://localhost/green-roots`

---
