# Green Roots 🌱

Green Roots is a web application designed to encourage tree planting and promote environmental protection. This app allows users to participate in tree planting activities, earn points, connect with community events, compete with others through rankings, and redeem rewards, all contributing to a greener planet.

## Table of Contents

- [About](#about)
- [Key Features](#key-features)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [Technology Stack](#technology-stack)
- [Contributing](#contributing)
- [License](#license)

## About

Green Roots makes environmental action accessible and rewarding through:
- **Tree Planting Verification**: Submit and track your tree planting efforts
- **Community Events**: Join local tree planting events with QR code tickets
- **Gamification**: Earn points, climb leaderboards, and redeem rewards
- **Community Impact**: See your barangay's environmental contribution

The platform features three user roles:
- **Users**: Plant trees, join events, earn rewards
- **Validators**: Review and verify tree planting submissions
- **Admins**: Manage the platform (partial implementation)

## Key Features

### For Users
- ✅ **Secure Authentication** - Create accounts and log in safely with encrypted passwords
- 🌳 **Tree Planting Records** - Submit photos and details for verification
- 📍 **Designated Planting Sites** - View community-specific planting locations
- 🎉 **Community Events** - Browse, join events, and get QR code tickets
- 🏆 **Rewards System** - Earn points and redeem for vouchers or cash (PayPal)
- 📊 **Rankings & Leaderboards** - Compare your impact with your community
- 📜 **Activity History** - Track all your submissions, events, and rewards
- 💬 **Feedback System** - Submit bug reports, feature requests, and comments
- ⚙️ **Account Management** - Update profile, password, and payment methods

### For Validators
- 📋 **Review Dashboard** - Overview of pending and reviewed submissions
- ✔️ **Approve/Reject Submissions** - Verify tree planting with eco points calculation
- 🗺️ **Barangay Planting Sites** - View designated planting locations on interactive maps
- 📊 **Submission Statistics** - Track barangay planting progress

### For Admins
- 🔧 **Platform Management** - Manage users, events, and validators (partial implementation)

## Quick Start

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/vibe-with-wyn/green-roots.git
   ```

2. **Set up XAMPP**:
   - Install [XAMPP](https://www.apachefriends.org)
   - Move project to `htdocs/green-roots`
   - Start Apache and MySQL

3. **Create database**:
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Create database: `greenroots_db`
   - Import: `database/greenroots_db.sql`

4. **Configure database** (if needed):
   - Edit `includes/config.php` with your credentials

5. **Access the app**:
   ```
   http://localhost/green-roots/index.php
   ```

For detailed installation instructions, see **[INSTALLATION.md](docs/INSTALLATION.md)**.

### Usage

1. **Register** - Create a user account at `register.php`
2. **Login** - Access your dashboard at `login.php`
3. **Submit Tree Planting** - Upload photos and details
4. **Join Events** - Browse and participate in community events
5. **Earn Rewards** - Redeem points for vouchers or cash
6. **Track Progress** - View your impact on the leaderboard

For detailed usage guide, see **[FEATURES.md](docs/FEATURES.md)**.

## Documentation

Comprehensive documentation is available in the `docs/` folder:

| Document | Description |
|----------|-------------|
| **[FEATURES.md](docs/FEATURES.md)** | Detailed feature descriptions and how they work |
| **[INSTALLATION.md](docs/INSTALLATION.md)** | Complete installation and setup guide |
| **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** | System architecture and folder structure |
| **[API.md](docs/API.md)** | API documentation and file functionalities |
| **[SECURITY.md](docs/SECURITY.md)** | Security features and best practices |

## Technology Stack

### Frontend
- HTML5, CSS3, JavaScript (ES6)
- Font Awesome (icons)
- Chart.js (data visualization)
- QRCode.js (QR code generation)
- jsPDF (PDF generation)
- Leaflet.js (interactive maps)

### Backend
- PHP 7.4+ with PDO
- MySQL database
- Session-based authentication

### Development Tools
- XAMPP (local server)
- VS Code (recommended editor)
- Git (version control)

For detailed technology information, see **[ARCHITECTURE.md](docs/ARCHITECTURE.md)**.

## Contributing

We welcome contributions to Green Roots! Here's how you can help:

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/your-feature-name`
3. **Commit your changes**: `git commit -m 'Add some feature'`
4. **Push to the branch**: `git push origin feature/your-feature-name`
5. **Open a Pull Request**

Please ensure your code follows the existing style and includes appropriate documentation.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- OpenStreetMap for mapping services
- Font Awesome for icons
- Chart.js for data visualization
- All contributors and testers

## Contact

For questions, issues, or suggestions:
- **GitHub Issues**: [Create an issue](https://github.com/vibe-with-wyn/green-roots/issues)
- **Repository**: [Green Roots on GitHub](https://github.com/vibe-with-wyn/green-roots)

---

**Note**: This is a prototype application prioritizing rapid development. Admin features are partially implemented, and validator features may contain bugs due to limited testing time. See **[SECURITY.md](docs/SECURITY.md)** for known limitations and planned improvements.
