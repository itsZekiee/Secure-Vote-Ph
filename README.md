<div align="center">

# 🗳️ Secure-Vote-Ph

**The Future of Philippine Elections**

[![GitHub stars](https://img.shields.io/github/stars/itsZekiee/Secure-Vote-Ph?style=for-the-badge)](https://github.com/itsZekiee/Secure-Vote-Ph/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/itsZekiee/Secure-Vote-Ph?style=for-the-badge)](https://github.com/itsZekiee/Secure-Vote-Ph/network/members)
[![GitHub issues](https://img.shields.io/github/issues/itsZekiee/Secure-Vote-Ph?style=for-the-badge)](https://github.com/itsZekiee/Secure-Vote-Ph/issues)
[![GitHub license](https://img.shields.io/github/license/itsZekiee/Secure-Vote-Ph?style=for-the-badge)](https://github.com/itsZekiee/Secure-Vote-Ph/blob/main/LICENSE)

**A transparent, secure, and accessible e-voting system powered by Laravel**

[Features](#-features) • [Quick Start](#-quick-start) • [Documentation](#-documentation) • [Contributing](#-contributing) • [License](#-license)

</div>

---

## 📋 Table of Contents

- [About The Project](#-about-the-project)
- [Key Features](#-key-features)
- [Tech Stack](#-tech-stack)
- [System Requirements](#️-system-requirements)
- [Installation & Setup](#-installation--setup)
- [Project Structure](#-project-structure)
- [Usage Guide](#-usage-guide)
- [Development](#-development)
- [Security](#-security)
- [Roadmap](#️-roadmap)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

---

## 📖 About The Project

**Secure-Vote-Ph** is an enterprise-grade electronic voting system built with the Laravel framework, specifically designed to revolutionize the Philippine electoral process. This platform prioritizes **security**, **transparency**, and **accessibility** while providing a scalable solution for organizations of all sizes.

### 🎯 Mission

To provide a secure, transparent, and accessible digital voting platform that strengthens democratic processes through technology.

### ✨ Why Secure-Vote-Ph? 

<table>
  <tr>
    <td align="center">🔒<br/><strong>Security First</strong><br/>OAuth authentication & vote encryption</td>
    <td align="center">📊<br/><strong>Transparent</strong><br/>Auditable trails & real-time monitoring</td>
    <td align="center">♿<br/><strong>Accessible</strong><br/>User-friendly interface for all</td>
  </tr>
  <tr>
    <td align="center">📈<br/><strong>Scalable</strong><br/>Multiple concurrent elections</td>
    <td align="center">⚡<br/><strong>Efficient</strong><br/>Bulk import & instant results</td>
    <td align="center">🔧<br/><strong>Customizable</strong><br/>Flexible organizational settings</td>
  </tr>
</table>

---

## 🚀 Key Features

### 🏛️ Election Management
- ✅ Create and manage multiple election cycles simultaneously
- ✅ Configure custom election periods with start and end dates
- ✅ Real-time election status tracking (Upcoming, Active, Completed)
- ✅ Comprehensive results dashboard with analytics and visualizations
- ✅ Export election results in multiple formats

### 🏢 Multi-Organization Support
- ✅ Independent elections for different organizations
- ✅ Department or group-based election isolation
- ✅ Custom organization settings and configurations
- ✅ Hierarchical organization management structure

### 👥 Candidate & Position Management
- ✅ Manage candidate profiles with photos and detailed descriptions
- ✅ Party-list and political affiliation support
- ✅ Define custom positions (President, Vice President, Secretary, etc.)
- ✅ Configure maximum votes per position
- ✅ Candidate eligibility verification and controls

### 📊 Advanced Voter Management
- ✅ Individual voter registration system
- ✅ **Bulk voter import** via Excel/CSV files
- ✅ Automated voter eligibility verification
- ✅ Complete vote tracking and audit trails
- ✅ Detailed voter participation statistics and analytics

### 🔐 Enterprise-Grade Security
- ✅ **Google OAuth 2.0 Integration** for secure authentication
- ✅ **One-vote policy** enforcement per user per election
- ✅ Session management with automatic timeout
- ✅ Vote encryption and anonymization
- ✅ Comprehensive audit logging for compliance
- ✅ CSRF protection and XSS prevention

### ⚙️ System Administration
- ✅ Fully customizable system settings
- ✅ Role-based access control (Admin, Organizer, Voter)
- ✅ Election result export functionality
- ✅ System health monitoring and diagnostics
- ✅ Activity logs and audit trails

---

## 🛠️ Tech Stack

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Blade](https://img.shields.io/badge/Blade-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/blade)
[![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)](https://getcomposer.org)

</div>

### Core Technologies

| Technology | Purpose |
|------------|---------|
| **Laravel 11.x** | Backend framework & application logic |
| **PHP 8.1+** | Server-side programming language |
| **MySQL 8.0+** | Relational database management |
| **Blade** | Templating engine for views |
| **Vite** | Frontend build tool & asset bundling |
| **Google OAuth** | Secure authentication provider |
| **Maatwebsite Excel** | Excel import/export functionality |

---

## 🖥️ System Requirements

| Requirement | Minimum Version | Recommended |
|------------|-----------------|-------------|
| **PHP** | 8.1 | 8.2+ |
| **Composer** | 2.0+ | Latest |
| **Node.js** | 16.x | 18.x or 20.x |
| **npm** | 8.x | Latest |
| **MySQL** | 8.0 | 8.0+ |
| **MariaDB** | 10.3+ | 10.6+ |
| **Web Server** | Apache/Nginx | Nginx (recommended) |

### 💻 Recommended Development Environments

<table>
  <tr>
    <td><strong>Windows</strong></td>
    <td>XAMPP, Laragon, Laravel Herd</td>
  </tr>
  <tr>
    <td><strong>macOS</strong></td>
    <td>Laravel Valet, Laravel Herd, MAMP</td>
  </tr>
  <tr>
    <td><strong>Linux</strong></td>
    <td>Native LAMP/LEMP stack</td>
  </tr>
  <tr>
    <td><strong>Cross-platform</strong></td>
    <td>Docker with Laravel Sail</td>
  </tr>
</table>

---

## 🚀 Installation & Setup

### Quick Start

```bash
# Clone the repository
git clone https://github.com/itsZekiee/Secure-Vote-Ph.git
cd Secure-Vote-Ph

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database and run migrations
php artisan migrate --seed

# Build assets and start server
npm run build
php artisan serve
```

### Detailed Installation Steps

#### 1️⃣ Clone the Repository

```bash
git clone https://github.com/itsZekiee/Secure-Vote-Ph.git
cd Secure-Vote-Ph
```

#### 2️⃣ Install Dependencies

```bash
# Install PHP dependencies via Composer
composer install

# Install Node.js dependencies
npm install
```

#### 3️⃣ Environment Configuration

```bash
# Create environment file from example
cp .env.example .env

# Generate application encryption key
php artisan key:generate
```

#### 4️⃣ Database Setup

**Create a new database:**

```sql
CREATE DATABASE secure_vote_ph CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Configure your `.env` file:**

```env
APP_NAME="Secure Vote Ph"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=secure_vote_ph
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 5️⃣ Google OAuth Configuration

1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the **Google+ API**
4. Navigate to **Credentials** → **Create Credentials** → **OAuth client ID**
5. Select **Web application** as the application type
6. Add authorized redirect URIs:
   - Development: `http://localhost:8000/auth/google/callback`
   - Production: `https://yourdomain.com/auth/google/callback`

**Update your `.env` file:**

```env
GOOGLE_CLIENT_ID=your-client-id. apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

#### 6️⃣ Run Database Migrations

```bash
# Run migrations to create database tables
php artisan migrate

# (Optional) Seed database with sample data for testing
php artisan db:seed
```

#### 7️⃣ Build Frontend Assets

```bash
# For production deployment
npm run build

# For development with hot module replacement
npm run dev
```

#### 8️⃣ Start the Development Server

```bash
php artisan serve
```

🎉 Visit `http://localhost:8000` in your browser to access the application!

---

## 📂 Project Structure

```
Secure-Vote-Ph/
├── 📁 app/                         # Application core
│   ├── 📁 Console/                 # Artisan commands
│   ├── 📁 Exceptions/              # Exception handling
│   ├── 📁 Http/                    # HTTP layer
│   │   ├── 📁 Controllers/         # Request controllers
│   │   │   ├── Auth/               # Authentication controllers
│   │   │   ├── Admin/              # Admin panel controllers
│   │   │   ├── ElectionController. php
│   │   │   ├── VoterController.php
│   │   │   └── CandidateController.php
│   │   ├── 📁 Middleware/          # HTTP middleware
│   │   └── 📁 Requests/            # Form request validation
│   ├── 📁 Imports/                 # Excel/CSV import logic
│   │   └── VotersImport.php        # Bulk voter import handler
│   ├── 📁 Models/                  # Eloquent ORM models
│   │   ├── User.php
│   │   ├── Election.php
│   │   ├── Candidate.php
│   │   ├── Vote.php
│   │   ├── Organization.php
│   │   └── Position.php
│   └── 📁 Providers/               # Service providers
│
├── 📁 bootstrap/                   # Application bootstrap
│   ├── app.php                     # Application initialization
│   └── cache/                      # Compiled services cache
│
├── 📁 config/                      # Configuration files
│   ├── app.php                     # Core application config
│   ├── auth. php                    # Authentication config
│   ├── database.php                # Database configuration
│   ├── filesystems.php             # File storage config
│   ├── mail.php                    # Email configuration
│   └── services.php                # Third-party services (OAuth)
│
├── 📁 database/                    # Database assets
│   ├── 📁 factories/               # Model factories for testing
│   ├── 📁 migrations/              # Database schema migrations
│   │   ├── *_create_users_table.php
│   │   ├── *_create_elections_table.php
│   │   ├── *_create_candidates_table.php
│   │   ├── *_create_votes_table.php
│   │   ├── *_create_organizations_table.php
│   │   └── *_create_positions_table.php
│   └── 📁 seeders/                 # Database seeders
│       └── DatabaseSeeder.php
│
├── 📁 public/                      # Publicly accessible files
│   ├── 📁 css/                     # Compiled stylesheets
│   ├── 📁 js/                      # Compiled JavaScript
│   ├── 📁 images/                  # Public images & assets
│   ├── . htaccess                   # Apache configuration
│   ├── favicon.ico                 # Site favicon
│   └── index.php                   # Application entry point
│
├── 📁 resources/                   # Raw assets & views
│   ├── 📁 css/                     # Source CSS files
│   │   └── app.css
│   ├── 📁 js/                      # Source JavaScript files
│   │   └── app.js
│   └── 📁 views/                   # Blade templates
│       ├── 📁 auth/                # Authentication views
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── 📁 admin/               # Admin panel views
│       │   ├── dashboard.blade. php
│       │   ├── elections/          # Election management
│       │   ├── candidates/         # Candidate management
│       │   ├── voters/             # Voter management
│       │   └── organizations/      # Organization management
│       ├── 📁 elections/           # Public election views
│       │   ├── index.blade.php     # Election list
│       │   ├── show.blade.php      # Election details
│       │   └── vote.blade.php      # Voting interface
│       ├── 📁 layouts/             # Layout templates
│       │   ├── app.blade.php       # Main application layout
│       │   └── guest.blade.php     # Guest/public layout
│       └── 📁 components/          # Reusable components
│
├── 📁 routes/                      # Application routing
│   ├── api.php                     # API routes (if used)
│   ├── console.php                 # Artisan console routes
│   ├── web.php                     # Web application routes
│   └── channels.php                # Broadcasting channels
│
├── 📁 storage/                     # Generated files & storage
│   ├── 📁 app/                     # Application storage
│   │   ├── 📁 public/              # Publicly accessible storage
│   │   └── 📁 imports/             # Uploaded voter files
│   ├── 📁 framework/               # Framework generated files
│   │   ├── 📁 cache/               # Application cache
│   │   ├── 📁 sessions/            # Session files
│   │   └── 📁 views/               # Compiled Blade views
│   └── 📁 logs/                    # Application logs
│       └── laravel.log
│
├── 📁 tests/                       # Automated tests
│   ├── 📁 Feature/                 # Feature tests
│   └── 📁 Unit/                    # Unit tests
│
├── 📁 vendor/                      # Composer dependencies
│
├── . env.example                    # Environment template
├── . gitignore                      # Git ignore rules
├── artisan                         # Artisan CLI
├── composer.json                   # PHP dependencies
├── composer.lock                   # Locked PHP dependencies
├── package.json                    # Node.js dependencies
├── package-lock.json               # Locked Node.js dependencies
├── phpunit.xml                     # PHPUnit configuration
├── README.md                       # Project documentation
└── vite.config.js                  # Vite build configuration
```

### 🗂️ Key Directory Descriptions

| Directory | Purpose |
|-----------|---------|
| `app/Http/Controllers/` | Business logic and request handling |
| `app/Models/` | Database models and relationships |
| `app/Imports/` | Excel/CSV bulk import functionality |
| `database/migrations/` | Database schema version control |
| `resources/views/` | Frontend Blade templates |
| `routes/web.php` | Application URL routing definitions |
| `storage/app/imports/` | Uploaded voter import files |
| `config/` | Application configuration files |
| `public/` | Web-accessible assets |

---

## 💻 Usage Guide

### 👨‍💼 For Administrators

#### 1. Create an Organization

1. Navigate to **Admin Panel** → **Organizations**
2. Click **Create New Organization**
3. Fill in organization details:
   - Organization name
   - Description
   - Settings and preferences
4. Click **Save**

#### 2. Set Up an Election

1. Go to **Election Management**
2. Click **Create New Election**
3. Configure election details:
   - Election title and description
   - Start and end dates
   - Associated organization
4. Define **Positions**:
   - Add positions (e.g., President, Vice President)
   - Set maximum votes per position
5. Add **Candidates**:
   - Upload candidate photos
   - Fill in candidate information
   - Assign to positions
6. Click **Publish Election**

#### 3. Import Voters

##### Single Voter Registration
- Navigate to **Voters** → **Add Voter**
- Fill in voter details
- Assign to organization/election

##### Bulk Voter Import
1. Go to **Voters** → **Bulk Import**
2. Download the Excel/CSV template
3. Fill in voter information: 
   - Name
   - Email
   - Organization
   - Voter ID (if applicable)
4. Upload the completed file
5. Review imported voters
6. Confirm import

#### 4. Monitor Elections

- **Real-time Dashboard**: Track voter participation
- **Live Results**: View vote counts (if enabled)
- **Analytics**: Access detailed statistics
- **Export Results**: Download results in Excel/PDF format

### 🗳️ For Voters

#### 1. Authentication

1. Visit the application URL
2. Click **Sign in with Google**
3. Authorize the application with your Google account
4. System verifies your voter eligibility

#### 2. Casting Your Vote

1. Select your **Organization's Active Election**
2. Review available **Candidates** and **Positions**
3. Make your selections: 
   - Click on candidate cards to select
   - Respect maximum votes per position
4. Review your choices
5. Click **Submit Ballot**
6. Receive confirmation of successful vote

#### 3. View Results

- Access **Results** page after election closes
- View detailed statistics and charts
- Download official results (if permitted)

---

## 🔧 Development

### Essential Commands

#### Start Local Development Server

```bash
# Start Laravel development server
php artisan serve

# Server will run at http://localhost:8000
```

#### Frontend Development

```bash
# Watch for file changes (development mode with HMR)
npm run dev

# Build optimized assets for production
npm run build
```

#### Database Operations

```bash
# Run all pending migrations
php artisan migrate

# Rollback the last migration batch
php artisan migrate:rollback

# Drop all tables and re-run migrations
php artisan migrate:fresh

# Migrate and seed database
php artisan migrate: fresh --seed
```

#### Clear Application Cache

```bash
# Clear all caches
php artisan optimize:clear

# Or clear individually: 
php artisan cache:clear       # Clear application cache
php artisan config:clear      # Clear configuration cache
php artisan route:clear       # Clear route cache
php artisan view:clear        # Clear compiled views
```

#### Run Tests

```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage
```

#### Code Quality

```bash
# Run PHP linter
composer lint

# Format code (if configured)
composer format
```

### 🐛 Debugging

Enable debug mode in `.env`:

```env
APP_DEBUG=true
APP_ENV=local
```

View logs: 

```bash
tail -f storage/logs/laravel.log
```

---

## 🔒 Security

### Security Best Practices Implemented

| Feature | Implementation |
|---------|----------------|
| **Authentication** | Google OAuth 2.0 with token refresh |
| **Authorization** | Role-based access control (RBAC) |
| **Vote Integrity** | One-vote-per-election enforcement |
| **Data Encryption** | TLS/SSL for data in transit |
| **Password Security** | bcrypt hashing with salt |
| **Session Security** | HTTP-only cookies, CSRF tokens |
| **SQL Injection Prevention** | Laravel Eloquent ORM parameterized queries |
| **XSS Protection** | Blade template auto-escaping |
| **Audit Logging** | Comprehensive activity tracking |
| **Input Validation** | Form requests with validation rules |

### 🛡️ Security Recommendations

- ✅ Always use HTTPS in production
- ✅ Keep Laravel and dependencies updated
- ✅ Use strong, unique database passwords
- ✅ Enable database backups
- ✅ Implement rate limiting on sensitive endpoints
- ✅ Regular security audits
- ✅ Monitor error logs for suspicious activity

### Reporting Security Vulnerabilities

If you discover a security vulnerability, please send an email to **[your-email@example.com]**. All security vulnerabilities will be promptly addressed. 

---

## 🗺️ Roadmap

### 🚀 Upcoming Features

- [ ] **Multi-language Support** (Tagalog, English, Cebuano)
- [ ] **SMS Notification Integration** for vote confirmations
- [ ] **Mobile Applications** (iOS & Android native apps)
- [ ] **Blockchain Verification Layer** for enhanced transparency
- [ ] **Advanced Analytics Dashboard** with data visualization
- [ ] **RESTful API** for third-party integrations
- [ ] **Biometric Authentication** support
- [ ] **Live Video Broadcasting** for election announcements
- [ ] **Ranked-choice Voting** support
- [ ] **Accessibility Improvements** (WCAG 2.1 compliance)

### 🔮 Future Enhancements

- [ ] AI-powered fraud detection
- [ ] Real-time election monitoring dashboard
- [ ] Voter education module
- [ ] Integration with government ID systems
- [ ] Multi-factor authentication options
- [ ] Offline voting capability with sync

---

## 🤝 Contributing

Contributions make the open-source community an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**!

### How to Contribute

1. **Fork the Project**
   ```bash
   # Click the "Fork" button at the top right of this page
   ```

2. **Clone Your Fork**
   ```bash
   git clone https://github.com/your-username/Secure-Vote-Ph.git
   cd Secure-Vote-Ph
   ```

3. **Create a Feature Branch**
   ```bash
   git checkout -b feature/AmazingFeature
   ```

4. **Make Your Changes**
   - Write clean, documented code
   - Follow Laravel coding standards
   - Add tests for new features

5. **Commit Your Changes**
   ```bash
   git add .
   git commit -m 'Add some AmazingFeature'
   ```

6. **Push to Your Fork**
   ```bash
   git push origin feature/AmazingFeature
   ```

7. **Open a Pull Request**
   - Go to the original repository
   - Click "New Pull Request"
   - Provide a clear description of your changes

### 📋 Contribution Guidelines

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Update documentation for new features
- Add tests for bug fixes and new features
- Ensure all tests pass before submitting PR

### 🐛 Bug Reports

If you find a bug, please open an issue with: 
- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)
- Environment details

---

## 📄 License

Distributed under the **MIT License**. See `LICENSE` file for more information.

```
MIT License

Copyright (c) 2025 itsZekiee

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction... 
```

---

## 👤 Contact

**Developer:** [@itsZekiee](https://github.com/itsZekiee)

- 💼 **GitHub:** [@itsZekiee](https://github.com/itsZekiee)
- 📧 **Email:** [your-email@example.com] (Update this)
- 🔗 **Project Link:** [https://github.com/itsZekiee/Secure-Vote-Ph](https://github.com/itsZekiee/Secure-Vote-Ph)

---

## 🙏 Acknowledgments

Special thanks to the following projects and resources: 

- [Laravel Framework](https://laravel.com/) - The elegant PHP framework
- [Blade Templating Engine](https://laravel.com/docs/blade) - Laravel's powerful templating
- [Google OAuth](https://developers.google.com/identity) - Secure authentication
- [Maatwebsite Laravel-Excel](https://laravel-excel.com/) - Excel import/export
- [Vite](https://vitejs.dev/) - Next generation frontend tooling
- All contributors and supporters of this project

### 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PHP:  The Right Way](https://phptherightway.com/)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

---

<div align="center">

### ⭐ Star this repository if you find it helpful!

**Made with ❤️ for the future of Philippine elections**

[![GitHub stars](https://img.shields.io/github/stars/itsZekiee/Secure-Vote-Ph?style=social)](https://github.com/itsZekiee/Secure-Vote-Ph/stargazers)

</div>
