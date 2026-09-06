# CampusConnect 2026 — Deployment Guide

## Your existing database
- Database: `studentdb`
- Table: `students` — created by you with:
  ```sql
  CREATE TABLE students (
      Student_id   INT PRIMARY KEY AUTO_INCREMENT,
      Full_Name    VARCHAR(20) NOT NULL,
      email        VARCHAR(20) NOT NULL UNIQUE,
      College_Name VARCHAR(25) NOT NULL,
      Location     VARCHAR(20) NOT NULL,
      Event        VARCHAR(20)
  );
  ```

### ⚠️ Required step before deploying
Your table has no `Password` column, but the exam requires one for login.
Run `database_alter.sql` once in your database (or just this line):
```sql
USE studentdb;
ALTER TABLE students ADD COLUMN Password VARCHAR(255) NOT NULL;
```
`VARCHAR(255)` is used so the password can be stored **hashed** (a hash is
60+ characters) rather than in plain text.

## Folder structure (frontend and backend kept separate)
```
campusconnect4/
├── index.html                 ← homepage
├── register.html              ← registration form (no Student ID field - auto-assigned)
├── login.html                  ← login form (Email or Student ID + Password)
├── css/style.css
├── backend/
│   ├── config.php              ← DB connection - set your password here
│   ├── register.php            ← registration logic, hashes password
│   └── login.php
└── database_alter.sql          ← run this once (adds Password column)
```
HTML files contain markup + a small script to display success/error
messages via a `?status=` URL flag. PHP files contain only backend logic
and redirect back to the HTML pages — no HTML is echoed from PHP.

## 1. Launch & connect to EC2
- Launch Ubuntu EC2, open Security Group for **port 22 (SSH)** and **port 80 (HTTP)**.
- `ssh -i your-key.pem ubuntu@<EC2-Public-IP>`

## 2. Install LAMP stack
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 mariadb-server php php-mysqli libapache2-mod-php -y
sudo systemctl enable apache2 mariadb
sudo systemctl start apache2 mariadb
sudo systemctl status apache2      # screenshot
sudo systemctl status mariadb      # screenshot
php -v                             # screenshot
```

## 3. Run the ALTER TABLE
```bash
sudo mysql -u root -p
```
```sql
USE studentdb;
ALTER TABLE students ADD COLUMN Password VARCHAR(255) NOT NULL;
DESCRIBE students;    -- screenshot: confirms Password column added
```

## 4. Deploy the website files
```bash
sudo rm -rf /var/www/html/*
sudo cp -r ~/campusconnect4/* /var/www/html/
sudo chown -R www-data:www-data /var/www/html
```

## 5. Set your DB password
```bash
sudo nano /var/www/html/backend/config.php    # set $db_pass
sudo systemctl restart apache2
```

## 6. Test (for your Website screenshots)
- `http://<EC2-Public-IP>/` → homepage
- Register → success message shows the auto-assigned Student ID
- Login with that email/Student ID + password → **"Welcome to CampusConnect!"**
- Wrong password → **"Invalid username or password."**
- `SELECT * FROM students;` → confirms the row (password will show as a hash)

## 7. Port/service verification
```bash
sudo ss -tulnp | grep :80
curl -I http://localhost
```
