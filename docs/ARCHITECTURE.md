# Green Roots - Architecture Documentation

This document describes the technical architecture and folder structure of the Green Roots application.

## Table of Contents

- [Architecture Overview](#architecture-overview)
- [Folder Structure](#folder-structure)
- [Design Pattern](#design-pattern)
- [Technology Stack](#technology-stack)
- [Database Schema](#database-schema)

## Architecture Overview

The application employs a **monolithic procedural programming approach**, where PHP backend logic, HTML templates, CSS styles, and JavaScript functionality are tightly integrated within single files (e.g., `events.php`, `feedback.php`, `account_settings.php`). This structure combines server-side processing (PHP with PDO for database interactions) and client-side rendering (HTML, CSS, JavaScript) to deliver a cohesive user experience.

Configuration is managed via `includes/config.php`, and backend services (e.g., `get_locations.php`) handle specific data operations.

### Development Status

This represents an **initial, simplified structure for prototyping purposes** and is not the final architecture. As a prototype, it prioritizes rapid development and testing over modularity. Future iterations may transition to a more robust framework, such as:
- A full **MVC (Model-View-Controller) architecture**
- A modular, **API-driven design**
- Enhanced scalability and maintainability

## Folder Structure

```
green-roots/
├── index.php                 # Homepage with login/signup links
├── LICENSE                   # Project license
├── README.md                 # Main project documentation
│
├── access/                   # Access control pages
│   └── access_denied.php     # Access denied page for unauthorized users
│
├── admin/                    # Admin-specific pages
│   ├── admin_dashboard.php   # Admin dashboard (partially implemented)
│   ├── manage_planting_sites.php  # Manage planting sites (pending)
│   ├── manage_validators.php      # Manage validators (pending)
│   └── upload_assets.php          # Upload assets (pending)
│
├── assets/                   # Frontend assets
│   ├── css/                  # Stylesheets
│   │   ├── account_settings.css
│   │   ├── dashboard.css
│   │   ├── events.css
│   │   ├── feedback.css
│   │   ├── history.css
│   │   ├── index.css
│   │   ├── leaderboard.css
│   │   ├── manage_validators.css
│   │   ├── password_security.css
│   │   ├── payment_methods.css
│   │   ├── pending_reviews.css
│   │   ├── planting_site.css
│   │   ├── profile.css
│   │   ├── rewards.css
│   │   └── submit.css
│   │
│   └── js/                   # JavaScript libraries
│       └── jsPDF-3.0.1/      # PDF generation library
│
├── database/                 # Database files
│   └── greenroots_db.sql     # Database schema and initial data
│
├── docs/                     # Documentation (created for better organization)
│   ├── ARCHITECTURE.md       # Architecture documentation
│   ├── API.md                # API and file functionalities
│   ├── FEATURES.md           # Detailed feature documentation
│   ├── INSTALLATION.md       # Installation guide
│   └── SECURITY.md           # Security features documentation
│
├── includes/                 # Configuration files
│   └── config.php            # Database connection configuration
│
├── services/                 # Backend services
│   ├── fetch_designated_site.php  # Fetch planting site data
│   ├── fetch_reviewed.php         # Fetch reviewed submissions
│   ├── get_locations.php          # Get location data (cascading)
│   └── update_submission.php      # Update submission status
│
├── validator/                # Validator-specific pages
│   ├── barangay_designated_site.php  # View barangay planting site
│   ├── pending_reviews.php           # Review pending submissions
│   ├── reviewed_submissions.php      # View reviewed submissions
│   └── validator_dashboard.php       # Validator dashboard
│
├── vendor/                   # Composer dependencies
│   ├── autoload.php
│   └── composer/             # Composer autoload files
│
└── views/                    # User-facing pages
    ├── account_settings.php  # Account settings page
    ├── dashboard.php         # User dashboard
    ├── events.php            # Events page
    ├── feedback.php          # Feedback page
    ├── history.php           # Activity history page
    ├── leaderboard.php       # Rankings/leaderboard page
    ├── login.php             # Login page
    ├── logout.php            # Logout functionality
    ├── password_security.php # Password & security page
    ├── payment_methods.php   # Payment methods page
    ├── planting_site.php     # Designated planting site page
    ├── profile.php           # Profile management page
    ├── register.php          # Registration page
    ├── rewards.php           # Rewards page
    └── submit.php            # Submit tree planting page
```

## Design Pattern

### Monolithic Procedural Approach

The current implementation uses a **monolithic procedural design** where:
- Each page file combines **presentation** (HTML), **business logic** (PHP), and **styling** (CSS)
- Database interactions use **PDO** with prepared statements
- Client-side interactivity uses vanilla **JavaScript** and AJAX
- Session management for authentication and authorization

### Component Breakdown

1. **Entry Points**
   - `index.php` - Landing page
   - `login.php` / `register.php` - Authentication
   - Role-based dashboards (user, validator, admin)

2. **Views Layer**
   - User-facing pages in `views/` directory
   - Validator pages in `validator/` directory
   - Admin pages in `admin/` directory

3. **Services Layer**
   - Backend services in `services/` directory
   - Handle AJAX requests and data processing
   - Return JSON responses for dynamic updates

4. **Data Layer**
   - Database configuration in `includes/config.php`
   - Direct PDO connections in each file
   - Prepared statements for security

5. **Assets**
   - Static CSS files for styling
   - JavaScript libraries (jsPDF, QRCode.js, Chart.js, Leaflet.js)
   - External CDN resources (Font Awesome)

## Technology Stack

### Frontend Technologies

- **HTML5** - Structure and markup
- **CSS3** - Styling and responsive design
- **JavaScript (ES6)** - Client-side interactivity
- **AJAX** - Asynchronous data fetching
- **Font Awesome** (CDN) - Icons
- **Chart.js** (CDN) - Data visualization
- **QRCode.js** (CDN) - QR code generation
- **jsPDF** (CDN) - PDF generation
- **Leaflet.js** (CDN) - Interactive maps

### Backend Technologies

- **PHP 7.4+** - Server-side programming
- **PDO** - Database abstraction layer
- **MySQL** - Relational database
- **Session Management** - User authentication

### Development Tools

- **XAMPP** - Local development environment
- **phpMyAdmin** - Database management
- **VS Code** - Code editor
- **Git** - Version control

### External Services

- **OpenStreetMap** - Mapping service for location display
- **PayPal** - Payment integration (prototype)
- **CDN Resources** - External libraries and fonts

## Database Schema

The application uses a MySQL database named `greenroots_db` with the following key tables:

### Core Tables

- **users** - User accounts and profiles
  - Stores: username, email, password (hashed), role, location, eco_points, trees_planted, profile_picture, paypal_email
  
- **submissions** - Tree planting submissions
  - Stores: user_id, trees_planted, photo, location (lat/long), status, notes, validated_by, validated_at, rejection_reason
  
- **events** - Community events
  - Stores: title, description, date, location, capacity, barangay_id
  
- **event_participants** - Event participation tracking
  - Stores: user_id, event_id, qr_code, joined_at, confirmed_at
  
- **feedback** - User feedback
  - Stores: user_id, category, rating, comments, is_anonymous, status, response, submitted_at
  
- **rewards** - Available rewards
  - Stores: name, points_required, type (voucher/cash), value
  
- **redeemed_rewards** - User reward redemptions
  - Stores: user_id, reward_id, code, qr_code, redeemed_at, expires_at
  
- **activities** - User activity log
  - Stores: user_id, activity_type, details, created_at

### Reference Tables

- **barangays** - Barangay locations
- **cities** - City data
- **provinces** - Province data
- **regions** - Region data
- **countries** - Country data
- **planting_sites** - Designated planting locations
- **rankings** - Community rankings
- **assets** - System assets (logos, favicons)

### Database Setup

For detailed database setup instructions, see [INSTALLATION.md](INSTALLATION.md).

---

## Production Environment

The application is currently deployed on InfinityFree with the following architecture:

- **Web Server**: Apache (managed by InfinityFree)
- **Database**: MySQL 5.7+ (Remote access via phpMyAdmin)
- **PHP Version**: 8.x
- **SSL/TLS**: Free SSL certificate enabled
- **Domain**: green-roots.is-great.net

---

**Note**: For detailed feature descriptions, see [FEATURES.md](FEATURES.md). For API documentation, see [API.md](API.md).
