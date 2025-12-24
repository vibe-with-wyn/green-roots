# Green Roots - Installation Guide

This guide will help you set up the Green Roots application on your local development environment.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation Steps](#installation-steps)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Accessing the Application](#accessing-the-application)
- [Troubleshooting](#troubleshooting)

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **XAMPP** (or similar LAMP/WAMP stack)
  - Apache web server
  - MySQL database server
  - PHP 7.4 or higher
- **Git** (for cloning the repository)
- **Web Browser** (Chrome, Firefox, Edge, Safari, etc.)
- **Text Editor** (VS Code recommended)

## Installation Steps

### 1. Clone the Repository

Open your terminal or command prompt and run:

```bash
git clone https://github.com/vibe-with-wyn/green-roots.git
```

Alternatively, you can download the ZIP file from GitHub:
1. Go to [https://github.com/vibe-with-wyn/green-roots](https://github.com/vibe-with-wyn/green-roots)
2. Click the green "Code" button
3. Select "Download ZIP"
4. Extract the ZIP file to your desired location

### 2. Set Up the Web Server

#### Using XAMPP:

1. **Install XAMPP** if not already installed:
   - Download from [https://www.apachefriends.org](https://www.apachefriends.org)
   - Run the installer and follow the installation wizard
   - Install to default location (usually `C:\xampp` on Windows)

2. **Move the Project to htdocs**:
   - Copy the `green-roots` folder to the XAMPP `htdocs` directory
   - Path should be: `C:\xampp\htdocs\green-roots` (Windows) or `/opt/lampp/htdocs/green-roots` (Linux)

3. **Start Apache and MySQL**:
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

### 3. Database Setup

#### Option A: Using phpMyAdmin (Recommended)

1. **Access phpMyAdmin**:
   - Open your web browser
   - Navigate to `http://localhost/phpmyadmin`

2. **Create the Database**:
   - Click on "New" in the left sidebar
   - Enter database name: `greenroots_db`
   - Select collation: `utf8mb4_general_ci`
   - Click "Create"

3. **Import the Database**:
   - Click on the `greenroots_db` database in the left sidebar
   - Click on the "Import" tab
   - Click "Choose File" and navigate to `green-roots/database/greenroots_db.sql`
   - Click "Go" at the bottom of the page
   - Wait for the import to complete (you should see a success message)

#### Option B: Using MySQL Command Line

```bash
# Navigate to the database folder
cd /path/to/green-roots/database

# Login to MySQL (default password is empty)
mysql -u root -p

# Create the database
CREATE DATABASE greenroots_db;

# Use the database
USE greenroots_db;

# Import the SQL file
SOURCE greenroots_db.sql;

# Exit MySQL
exit;
```

## Configuration

### Configure Database Connection

1. **Open the Configuration File**:
   - Navigate to `green-roots/includes/config.php`

2. **Update Database Credentials** (if needed):

```php
<?php
// Default XAMPP configuration
$host = 'localhost';
$dbname = 'greenroots_db';
$username = 'root';
$password = ''; // Default is empty for XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

3. **Save the file** if you made any changes.

### Verify File Permissions (Linux/Mac)

If you're on Linux or Mac, ensure the web server has proper permissions:

```bash
# Navigate to the project directory
cd /opt/lampp/htdocs/green-roots

# Set appropriate permissions
chmod -R 755 .
chown -R www-data:www-data .
```

## Accessing the Application

1. **Start Your Web Server**:
   - Ensure Apache and MySQL are running in XAMPP Control Panel

2. **Open the Application**:
   - Open your web browser
   - Navigate to: `http://localhost/green-roots/index.php`

3. **Landing Page**:
   - You should see the Green Roots landing page
   - Click "Get Started" or use the navigation bar to access Login/Register

## Database Setup Details

The application uses a MySQL database (`greenroots_db`) with the following structure:

### Core Tables

- **users** - User accounts and profiles
- **submissions** - Tree planting submissions
- **events** - Community events
- **event_participants** - Event participation tracking
- **feedback** - User feedback
- **rewards** - Available rewards
- **redeemed_rewards** - User reward redemptions
- **activities** - User activity log

### Reference Tables

- **barangays** - Barangay (neighborhood) data
- **cities** - City data
- **provinces** - Province data
- **regions** - Region data
- **countries** - Country data
- **planting_sites** - Designated planting locations
- **rankings** - Community rankings
- **assets** - System assets (logos, favicons)

### Default Data

The SQL import includes:
- Location data (Philippines regions, provinces, cities, barangays)
- Sample events
- Sample rewards
- Default assets (logos, favicons)

## Creating Test Accounts

### User Account

To create a regular user account:
1. Navigate to `http://localhost/green-roots/register.php`
2. Fill in the registration form:
   - Username: (choose your own)
   - Email: (choose your own)
   - Password: (minimum 8 characters)
   - Location: Select from dropdowns
3. Click "Register"
4. Log in at `http://localhost/green-roots/login.php`

### Validator Account

Validator accounts require manual database entry:

```sql
-- Update an existing user to validator role
UPDATE users 
SET role = 'eco_validator', 
    barangay_id = 1  -- Set to appropriate barangay ID
WHERE username = 'your_username';
```

### Admin Account

Admin accounts require manual database entry:

```sql
-- Update an existing user to admin role
UPDATE users 
SET role = 'admin'
WHERE username = 'your_username';
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Failed

**Error**: "Database connection failed"

**Solutions**:
- Verify MySQL is running in XAMPP Control Panel
- Check database credentials in `includes/config.php`
- Ensure database `greenroots_db` exists in phpMyAdmin
- Verify database user has proper permissions

#### 2. Page Not Found (404)

**Error**: 404 Not Found

**Solutions**:
- Verify Apache is running in XAMPP Control Panel
- Check that the project is in the correct htdocs folder
- Ensure URL is correct: `http://localhost/green-roots/index.php`
- Clear browser cache

#### 3. Blank White Page

**Error**: Blank page with no errors

**Solutions**:
- Enable PHP error reporting by adding to the top of `index.php`:
  ```php
  <?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ?>
  ```
- Check PHP error logs in XAMPP control panel
- Verify PHP version is 7.4 or higher

#### 4. Import Failed in phpMyAdmin

**Error**: Import error or timeout

**Solutions**:
- Increase upload limits in `php.ini`:
  ```ini
  upload_max_filesize = 50M
  post_max_size = 50M
  max_execution_time = 300
  ```
- Restart Apache after changing `php.ini`
- Try importing via MySQL command line instead

#### 5. Session Errors

**Error**: "Session could not start"

**Solutions**:
- Check session folder permissions
- Verify `session.save_path` in `php.ini`
- Clear browser cookies and cache

#### 6. File Upload Errors

**Error**: Profile picture or submission photo upload fails

**Solutions**:
- Check file upload settings in `php.ini`:
  ```ini
  file_uploads = On
  upload_max_filesize = 20M
  post_max_size = 25M
  ```
- Restart Apache after changes
- Verify folder permissions for uploads

### Getting Help

If you encounter issues not covered here:

1. Check the [GitHub Issues](https://github.com/vibe-with-wyn/green-roots/issues) page
2. Review the [ARCHITECTURE.md](ARCHITECTURE.md) for system design
3. See [API.md](API.md) for technical implementation details
4. Contact the development team

## Next Steps

After successful installation:

1. **Explore the Application**:
   - Create a user account
   - Submit a tree planting record
   - Browse events
   - Check the leaderboard

2. **Review Documentation**:
   - [FEATURES.md](FEATURES.md) - Feature descriptions
   - [ARCHITECTURE.md](ARCHITECTURE.md) - System architecture
   - [API.md](API.md) - API and file functionalities
   - [SECURITY.md](SECURITY.md) - Security features

3. **Customize the Application**:
   - Add your own location data
   - Create custom events
   - Configure rewards
   - Upload custom assets

## Development Environment

For development purposes:

### Recommended Tools

- **VS Code** - Code editor with PHP extensions
- **Git** - Version control
- **Postman** - API testing (for AJAX endpoints)
- **Browser DevTools** - Debugging JavaScript

### Useful VS Code Extensions

- PHP Intelephense
- PHP Debug
- MySQL
- Live Server (for static testing)

### Database Management

- **phpMyAdmin** - Web-based interface
- **MySQL Workbench** - Desktop application
- **DBeaver** - Universal database tool

---

**Note**: This is a development setup guide. For production deployment, additional security measures and server configuration are required.
