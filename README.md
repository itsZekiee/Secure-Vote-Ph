# 🗳️ Secure-Vote-Ph

**Secure-Vote-Ph** is a professional, secure electronic voting system built with the **Laravel** framework. It is specifically designed to manage elections, organizations, and voter participation in the Philippines, ensuring a transparent and tamper-proof voting process.

---

## ✨ Features

* **Election Management** – Create, update, and monitor multiple election cycles.
* **Organization Support** – Handle independent elections for various departments or groups.
* **Candidate Registration** – Manage candidate profiles and partylist affiliations.
* **Position Management** – Define custom roles (e.g., President, Secretary, etc.).
* **Bulk Voter Import** – Quickly onboard large numbers of voters via spreadsheet (Excel/CSV).
* **Google Authentication** – Secure and easy login via Google OAuth integration.
* **One-Vote Policy** – Strict logic to ensure one vote per user per election.
* **System Settings** – Fully customizable configuration for tailored election rules.

---

## 🛠️ System Requirements

Before installing, ensure your environment meets the following:
* **PHP:** 8.1 or higher
* **Composer** (PHP Package Manager)
* **Node.js & npm** (For asset compilation)
* **Database:** MySQL or MariaDB
* **Local Server:** XAMPP, Laragon, or PHP Artisan Serve

---

## 🚀 Installation & Setup

### 1. Clone the Repository

git clone [https://github.com/itsZekiee/Secure-Vote-Ph.git](https://github.com/itsZekiee/Secure-Vote-Ph.git)
cd Secure-Vote-Ph 

### 2. Install Dependencies

# Install PHP packages
composer install

# Install Frontend packages
npm install

### 3. Environment Configuration

# Create your local environment file
cp .env.example .env

# Generate the application encryption key
php artisan key:generate

### 4. Database Setup

* **Create a database named secure_vote_ph in your SQL manager (e.g., phpMyAdmin).
* **Update your .env file with your database credentials:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=secure_vote_ph
DB_USERNAME=root
DB_PASSWORD=

### 5. Run Migrations & Build

# Run database migrations and seeders
php artisan migrate

# Compile frontend assets
npm run build

📂 Project Structure


💻 Development Commands
Start the local server:

php artisan serve

Watch for frontend changes (Development):

👤 Author
Developed by @itsZekiee
npm run dev
