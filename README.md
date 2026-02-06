# Art Noir - Artistic Online Museum

A comprehensive art gallery management system that enables artists to showcase their work, users to explore artwork collections, and administrators to manage the entire platform through an advanced analytics dashboard.

![Project Status](https://img.shields.io/badge/status-active-success.svg)
![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [Usage](#usage)
- [Future Enhancements](#future-enhancements)
- [Author](#author)

## 🎨 Overview

Art Noir is a full-stack web application built as a final project for CSC 440 - Web Development at the American University of Technology (AUT). The system features a complete approval workflow for artworks, user registrations, and an advanced analytics dashboard with real-time data visualization.

**Key Objectives:**

- Convert all interactions to asynchronous AJAX-based operations
- Implement robust session management for authentication and authorization
- Create an advanced Admin Dashboard with statistics and data visualization
- Provide seamless user experience without page reloads

## ✨ Features

### User Management

- ✅ User registration and authentication
- ✅ Role-based access control (User/Admin)
- ✅ Profile management with image upload
- ✅ Password change functionality

### Artist Profiles

- ✅ Create and manage artist portfolios
- ✅ Biographical information management
- ✅ Artist type categorization (Historical/Community)

### Artwork Management

- ✅ Upload artworks with detailed metadata
- ✅ Image upload and storage
- ✅ Category-based organization
- ✅ Approval workflow (Pending/Approved/Rejected)
- ✅ CRUD operations for artwork management

### Subscription System

- ✅ Three-tier plans: Canvas, Studio, Gallery
- ✅ Upload limits based on subscription
- 🔄 Payment integration (Future Work)

### Community Features

- ✅ Artwork voting system
- ✅ Real-time notification system
- ✅ Contact messaging to administrators

### Admin Dashboard

- 📊 Comprehensive statistics and KPIs
- 📈 Interactive data visualizations (Chart.js)
- 🔍 Advanced filtering options
- 📅 Date range analysis
- 📉 Monthly growth trends
- 🎯 Category and status breakdowns
- ⚡ Quick action buttons

## 🛠️ Tech Stack

**Frontend:**

- HTML5
- CSS3
- Bootstrap 5
- JavaScript (ES6+)
- jQuery
- Chart.js (Data Visualization)

**Backend:**

- PHP 7.4+
- AJAX (Asynchronous Operations)
- MVC Architecture Pattern

**Database:**

- MySQL / MariaDB 10.4.28
- phpMyAdmin

**Development Tools:**

- XAMPP / WAMP
- Git & GitHub

## 📁 Project Structure

```
ArtNoir/
├── admin/
│   ├── index.php                 # Admin main page
│   ├── DashboardContent.php      # Dashboard statistics
│   ├── ArtistsContent.php        # Artist management
│   ├── ArtworksContent.php       # Artwork management
│   ├── UsersContent.php          # User management
│   ├── CategoriesContent.php     # Category management
│   └── NotificationsContent.php  # Notifications
│
├── assets/
│   ├── css/
│   │   ├── style.css             # Main styles
│   │   ├── admin.css             # Admin panel styles
│   │   └── dashboard.css         # Dashboard specific styles
│   ├── js/
│   │   ├── login.js              # Login functionality
│   │   ├── register.js           # Registration
│   │   ├── artists.js            # Artist AJAX operations
│   │   ├── artworks.js           # Artwork AJAX operations
│   │   ├── users.js              # User management
│   │   ├── categories.js         # Category management
│   │   ├── profile.js            # Profile operations
│   │   ├── edit-profile.js       # Profile editing
│   │   ├── contact.js            # Contact messages
│   │   ├── logout.js             # Logout functionality
│   │   └── dashboard.js          # Dashboard charts & data
│   └── images/
│       ├── artworks/             # Uploaded artwork images
│       ├── profiles/             # User profile pictures
│       └── artists/              # Artist images
│
├── ws/                           # Web Services (AJAX Endpoints)
│   ├── WsLogin.php               # Login API
│   ├── WsLogout.php              # Logout API
│   ├── WsRegister.php            # Registration API
│   ├── WsArtists.php             # Artist CRUD API
│   ├── WsArtworks.php            # Artwork CRUD API
│   ├── WsUsers.php               # User management API
│   ├── WsCategories.php          # Category CRUD API
│   ├── WsProfile.php             # Profile operations API
│   ├── WsMessages.php            # Contact messages API
│   ├── WsNotifications.php       # Notifications API
│   └── WsDashboard.php           # Dashboard data API
│
├── models/
│   ├── Database.php              # Database connection
│   ├── User.php                  # User model
│   ├── Artist.php                # Artist model
│   ├── Artwork.php               # Artwork model
│   ├── Category.php              # Category model
│   └── Message.php               # Message model
│
├── includes/
│   ├── CheckSession.php          # Session validation
│   └── config.php                # Configuration settings
│
├── database/
│   └── artnoir.sql               # Database schema & sample data
│
├── index.php                     # Homepage / Gallery
├── login.php                     # Login page
├── register.php                  # Registration page
├── gallery.php                   # Public gallery
├── profile.php                   # User profile
├── contact.php                   # Contact page
├── .gitignore                    # Git ignore file
└── README.md                     # Project documentation
```

## 🗄️ Database Schema

**Database Name:** `ArtNoir`  
**Engine:** MariaDB 10.4.28

### Main Tables:

- `users` - User accounts and authentication
- `artists` - Artist profiles and information
- `artworks` - Artwork entries with metadata
- `categories` - Artwork categories
- `messages` - Contact messages
- `notifications` - System notifications
- `plans` - Subscription plans (Future)
- `user_plans` - User subscription mapping (Future)
- `votes` - Artwork voting system (Future)

**Note:** The complete Entity Relationship Diagram is included in the project report.

## 🚀 Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ / MariaDB 10.4+
- Apache server (XAMPP/WAMP recommended)
- Web browser (Chrome, Firefox, Safari)

### Step-by-Step Setup

1. **Clone the repository**
   
   ```bash
   git clone https://github.com/zahraazahraman/art-noir.git
   cd art-noir
   ```
1. **Set up the database**
- Open phpMyAdmin
- Create a new database named `ArtNoir`
- Import the SQL file: `database/ArtNoir.sql`
1. **Configure database connection**
- Open `includes/config.php`
- Update database credentials:
   
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'ArtNoir');
   ```
1. **Set up file permissions**
- Ensure upload directories are writable:
   
   ```bash
   chmod 755 assets/images/artworks
   chmod 755 assets/images/profiles
   chmod 755 assets/images/artists
   ```
1. **Start your server**
- Start Apache and MySQL via XAMPP/WAMP
- Navigate to `http://localhost/ArtNoir`
1. **Default Admin Credentials** (if included in SQL)
- Email: `admin@artnoir.com`
- Password: `123456`

## 💻 Usage

### For Users:

1. **Register** an account at `/register.php`
1. **Browse** the gallery at `/gallery.php`
1. **View** artwork details and artist profiles
1. **Vote** on your favorite artworks
1. **Contact** administrators via `/contact.php`

### For Admins:

1. **Login** with admin credentials
1. **Access Dashboard** at `/admin/index.php`
1. **Manage** users, artists, artworks, and categories
1. **Approve/Reject** pending artworks
1. **View Analytics** with interactive charts
1. **Filter Data** by date range, category, status

### Key Features:

**AJAX Operations (No Page Reload):**

- All form submissions
- Login/Logout
- CRUD operations
- Search and filtering
- Data visualization updates
- Notification polling

**Session Management:**

- Secure authentication
- Role-based authorization
- Automatic session validation
- Timeout handling

## 🔮 Future Enhancements

- [ ] Payment integration for subscription plans
- [ ] Advanced voting system with leaderboards
- [ ] Email notifications
- [ ] Social media sharing
- [ ] Artwork search with filters
- [ ] Mobile responsive design improvements
- [ ] API for third-party integrations
- [ ] Multi-language support

## 👨‍💻 Author

**Zahraa Zahraman**  
Computer Science Student - American University of Technology (AUT)

- 📧 Email: zahraazahraman@gmail.com
- 💼 LinkedIn: [Your LinkedIn Profile]
- 🌐 Portfolio: [Your Portfolio URL]

**Project Information:**

- Course: CSC 440 - Web Development
- Instructor: Dr. Georges Abboudeh
- Student ID: 202300306
- Completion Date: December 2025

-----

## 📄 License

This project was developed as an academic assignment for educational purposes.

-----

## 🙏 Acknowledgments

- Dr. Georges Abboudeh for guidance and instruction
- American University of Technology (AUT)
- Chart.js for data visualization library
- Bootstrap for responsive framework

-----

**⭐ If you find this project interesting, please consider giving it a star!**
