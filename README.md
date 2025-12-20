# 🗳️ Secure-Vote-Ph

<div align="center">

![GitHub stars](https://img.shields.io/github/stars/itsZekiee/Secure-Vote-Ph?style=social)
![GitHub forks](https://img.shields.io/github/forks/itsZekiee/Secure-Vote-Ph?style=social)
![GitHub issues](https://img.shields.io/github/issues/itsZekiee/Secure-Vote-Ph)
![GitHub license](https://img.shields.io/github/license/itsZekiee/Secure-Vote-Ph)

**The future of Philippine elections. A transparent and accessible e-voting system powered by Laravel.**

[Features](#-features) • [Installation](#-installation--setup) • [Usage](#-usage-guide) • [Documentation](#-documentation) • [Contributing](#-contributing)

</div>

---

## 📖 About The Project

**Secure-Vote-Ph** is a professional, secure electronic voting system built with the **Laravel** framework. It is specifically designed to manage elections, organizations, and voter participation in the Philippines.  The system prioritizes **security**, **integrity**, and **auditable results**, making it ideal for schools, organizations, and institutions looking to modernize their voting processes.

### Why Secure-Vote-Ph?

- ✅ **Transparency** – Every vote is tracked and auditable
- ✅ **Security** – OAuth authentication and one-vote policy prevent fraud
- ✅ **Accessibility** – Easy-to-use interface for all users
- ✅ **Scalability** – Handles multiple concurrent elections
- ✅ **Flexibility** – Customizable for different organizational needs

---

## ✨ Features

### 🏛️ **Election Management**
- Create, update, and monitor multiple election cycles simultaneously
- Set custom election periods with start and end dates
- Real-time election status tracking (Upcoming, Active, Completed)
- Comprehensive election results and analytics dashboard

### 🏢 **Organization Support**
- Multi-organization architecture for independent elections
- Department or group-based election isolation
- Custom organization settings and configurations
- Hierarchical organization management

### 👥 **Candidate & Position Management**
- Manage candidate profiles with photos and descriptions
- Party-list/affiliation support
- Define custom positions (President, Vice President, Secretary, etc.)
- Maximum votes per position configuration
- Candidate eligibility controls

### 📊 **Voter Management**
- Individual voter registration
- **Bulk voter import** via Excel/CSV files
- Voter eligibility verification
- Vote tracking and audit trails
- Voter participation statistics

### 🔐 **Security Features**
- **Google OAuth Integration** for secure authentication
- **One-vote policy** enforcement per user per election
- Session management and timeout controls
- Vote encryption and anonymization
- Audit logging for all system activities

### ⚙️ **System Administration**
- Fully customizable system settings
- User role management (Admin, Organizer, Voter)
- Election result export functionality
- System health monitoring

---

## 🛠️ System Requirements

Before installing, ensure your environment meets the following: 

| Requirement | Version |
|------------|---------|
| **PHP** | 8.1 or higher |
| **Composer** | Latest version |
| **Node.js** | 16.x or higher |
| **npm** | 8.x or higher |
| **Database** | MySQL 8.0+ or MariaDB 10.3+ |
| **Web Server** | Apache/Nginx or Laravel Artisan Serve |

### Recommended Development Environment
- **XAMPP** (Windows/Mac)
- **Laragon** (Windows)
- **Laravel Valet** (Mac)
- **Docker** with Laravel Sail

---

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/itsZekiee/Secure-Vote-Ph. git
cd Secure-Vote-Ph
```

### 2. Install Dependencies

```bash
# Install PHP packages
composer install

# Install Frontend packages
npm install
```

### 3. Environment Configuration

```bash
# Create your local environment file
cp .env.example .env

# Generate the application encryption key
php artisan key:generate
```

### 4. Database Setup

1. **Create a database** named `secure_vote_ph` in your SQL manager (e.g., phpMyAdmin)

2. **Update your `.env` file** with your database credentials: 

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=secure_vote_ph
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Google OAuth Configuration

To enable Google Authentication:

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback` (development)
   - Your production URL + `/auth/google/callback`

6. Update your `.env` file:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 6. Run Migrations & Seeders

```bash
# Run database migrations
php artisan migrate

# (Optional) Seed the database with sample data
php artisan db: seed
```

### 7. Build Frontend Assets

```bash
# For production
npm run build

# For development (with hot reload)
npm run dev
```

### 8. Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser. 

---

## 📂 Project Structure

![image1](image1)

| Directory | Description |
|-----------|-------------|
| `app/Http/` | Controllers, Middleware, and Form Requests |
| `app/Imports/` | Logic for bulk voter Excel/CSV imports |
| `app/Models/` | Eloquent models and data relationships |
| `database/migrations/` | Database schema and table definitions |
| `resources/views/` | Blade templates and frontend UI |
| `routes/web.php` | Application URL routing |
| `storage/` | Logs, cache, and uploaded voter files |
| `config/` | Application configuration files |
| `public/` | Publicly accessible assets (CSS, JS, images) |

---

## 💻 Development Commands

### Start the Local Server
```bash
php artisan serve
```

### Watch for Frontend Changes (Development)
```bash
npm run dev
```

### Build for Production
```bash
npm run build
```

### Run Tests
```bash
php artisan test
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route: clear
php artisan view:clear
```

### Database Commands
```bash
# Fresh migration (drops all tables)
php artisan migrate: fresh

# Migrate with seeders
php artisan migrate --seed

# Rollback last migration
php artisan migrate:rollback
```

---

## 📱 Usage Guide

### For Administrators

1. **Create an Organization**
   - Navigate to Organization Management
   - Fill in organization details
   - Set organization-specific settings

2. **Set Up an Election**
   - Go to Election Management
   - Create a new election with dates and description
   - Define positions and maximum votes per position
   - Add candidates with their information

3. **Import Voters**
   - Use the bulk import feature
   - Upload Excel/CSV file with voter information
   - Verify imported voters
   - Send authentication links

4. **Monitor Elections**
   - Track real-time participation rates
   - View live results (if enabled)
   - Export results after election closes

### For Voters

1. **Authentication**
   - Click "Sign in with Google"
   - Authorize the application
   - Verify your voter eligibility

2. **Casting a Vote**
   - Select your organization's election
   - Review candidates and positions
   - Make your selections
   - Submit your ballot
   - Receive confirmation

3. **View Results**
   - Access results after election closes
   - View detailed statistics and charts

---

## 🔒 Security Considerations

- **OAuth Authentication**: All users authenticate via Google OAuth 2.0
- **Vote Integrity**: One-vote-per-election policy strictly enforced
- **Data Encryption**:  Sensitive data encrypted in transit and at rest
- **Audit Trails**:  Comprehensive logging of all system activities
- **Session Security**: Automatic timeout and CSRF protection
- **SQL Injection Prevention**: Laravel's Eloquent ORM prevents SQL injection
- **XSS Protection**: Blade templating auto-escapes output

---

## 🗺️ Roadmap

- [ ] Multi-language support (Tagalog, English)
- [ ] SMS notification integration
- [ ] Mobile application (iOS/Android)
- [ ] Blockchain verification layer
- [ ] Advanced analytics dashboard
- [ ] API for third-party integrations
- [ ] Biometric authentication support

---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` file for more information.

---

## 👤 Author

**Developed by [@itsZekiee](https://github.com/itsZekiee)**

- GitHub: [@itsZekiee](https://github.com/itsZekiee)
- Project Link: [https://github.com/itsZekiee/Secure-Vote-Ph](https://github.com/itsZekiee/Secure-Vote-Ph)

---

## 🙏 Acknowledgments

- [Laravel Framework](https://laravel.com/)
- [Blade Templating Engine](https://laravel.com/docs/blade)
- [Google OAuth](https://developers.google.com/identity)
- [Tailwind CSS](https://tailwindcss.com/) (if used)
- All contributors who help improve this project

---

<div align="center">

**⭐ If you find this project useful, please consider giving it a star!  ⭐**

Made with ❤️ for the future of Philippine elections

</div>
