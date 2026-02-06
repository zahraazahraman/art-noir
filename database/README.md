# Database Setup Instructions

## Quick Setup

1. **Open phpMyAdmin** in your browser (usually `http://localhost/phpmyadmin`)
1. **Create Database:**
- Click “New” in the left sidebar
- Database name: `ArtNoir`
- Collation: `utf8mb4_general_ci`
- Click “Create”
1. **Import Schema:**
- Select the `ArtNoir` database
- Click “Import” tab
- Choose file: `ArtNoir.sql`
- Click “Go”
1. **Verify Tables:**
   After import, you should see these tables:
- users
- artists
- artworks
- categories
- messages
- notifications
- plans (future)
- user_plans (future)
- votes (future)

## Configuration

Update database credentials in `includes/config.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Change if different
define('DB_PASS', '');              // Add password if set
define('DB_NAME', 'ArtNoir');
define('DB_CHARSET', 'utf8mb4');
?>
```

## Default Credentials

If sample data is included:

**Admin Account:**

- Email: admin@artnoir.com
- Password: 123456

**Test User Account:**

- Email: user@artnoir.com
- Password: user123

**Important:** Change default passwords in production!

## Troubleshooting

**Connection Failed:**

- Verify MySQL/MariaDB is running
- Check credentials in `config.php`
- Ensure database exists

**Import Errors:**

- Check file encoding (should be UTF-8)
- Verify MySQL version compatibility
- Check for syntax errors in SQL file

**Permission Issues:**

- Ensure MySQL user has proper privileges:
  
  ```sql
  GRANT ALL PRIVILEGES ON ArtNoir.* TO 'root'@'localhost';
  FLUSH PRIVILEGES;
  ```

## Database Backup

To backup your database:

```bash
mysqldump -u root -p ArtNoir > backup_ArtNoir_$(date +%Y%m%d).sql
```

## Fresh Installation

To reset database:

```sql
DROP DATABASE IF EXISTS ArtNoir;
CREATE DATABASE ArtNoir CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Then re-import `ArtNoir.sql`.