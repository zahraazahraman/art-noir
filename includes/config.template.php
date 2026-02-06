<?php
/**
 * Art Noir - Configuration File Template
 * 
 * Instructions:
 * 1. Copy this file to 'config.php'
 * 2. Update the values below with your actual credentials
 * 3. Never commit config.php to Git (it's in .gitignore)
 */

// ============================================
// Database Configuration
// ============================================
define('DB_HOST', 'localhost');           // Database host
define('DB_USER', 'root');                // Database username
define('DB_PASS', '');                    // Database password
define('DB_NAME', 'ArtNoir');             // Database name
define('DB_CHARSET', 'utf8mb4');          // Character set

// ============================================
// Application Settings
// ============================================
define('SITE_URL', 'http://localhost/art-noir');  // Your site URL
define('SITE_NAME', 'Art Noir');                  // Application name
define('ADMIN_EMAIL', 'admin@artnoir.com');       // Admin email

// ============================================
// Session Configuration
// ============================================
define('SESSION_TIMEOUT', 3600);          // Session timeout in seconds (1 hour)
define('SESSION_NAME', 'ARTNOIR_SESSION'); // Session cookie name

// ============================================
// File Upload Settings
// ============================================
define('UPLOAD_PATH_ARTWORKS', 'assets/images/artworks/');
define('UPLOAD_PATH_PROFILES', 'assets/images/profiles/');
define('UPLOAD_PATH_ARTISTS', 'assets/images/artists/');

define('MAX_FILE_SIZE', 5242880);         // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ============================================
// Subscription Plans (Future Implementation)
// ============================================
define('PLAN_CANVAS_LIMIT', 5);           // Canvas plan upload limit
define('PLAN_STUDIO_LIMIT', 15);          // Studio plan upload limit
define('PLAN_GALLERY_LIMIT', 999);        // Gallery plan upload limit (unlimited)

// ============================================
// Error Reporting (Development Only)
// ============================================
// Set to 0 in production!
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================
// Timezone
// ============================================
date_default_timezone_set('Asia/Beirut');  // Change to your timezone

?>