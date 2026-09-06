# CampusConnect 2026 — Full Deployment Command Log
## 1. Creating the AWS EC2 Instance
Done via the AWS Console (EC2 → Launch Instance):
- AMI: **Amazon Linux 2023**
- Instance type: `t2.micro` (or your chosen size)
- Key pair: created/selected a `.pem` key pair for SSH access
- Storage: default 8GB gp3 (or as configured)
- Launch the instance and wait for **Instance State: Running**

📸 Screenshot: instance creation wizard, instance list showing "Running"

---

## 2. Security Group Configuration
Inbound rules added to the instance's Security Group:

| Type  | Protocol | Port | Source    |
|-------|----------|------|-----------|
| SSH   | TCP      | 22   | My IP / 0.0.0.0/0 |
| HTTP  | TCP      | 80   | 0.0.0.0/0 |

📸 Screenshot: Security Group inbound rules page

---

## 3. SSH Connection
```bash
chmod 400 your-key.pem
ssh -i your-key.pem ec2-user@54.83.160.102
```

📸 Screenshot: successful SSH login banner

---

## 4. Linux User Creation
```bash
sudo useradd aashu
sudo passwd aashu
sudo usermod -aG wheel aashu      # grants sudo privileges (wheel group on Amazon Linux)
su - aashu                        # switch to the new user
```

Verify:
```bash
id aashu
groups aashu
```

📸 Screenshot: `id aashu` output, successful `su - aashu`

---

## 5. Installing Nginx, MariaDB, PHP 8.5, PHP-FPM
```bash
sudo dnf update -y

# Nginx
sudo dnf install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx

# MariaDB
sudo dnf install mariadb105-server -y
sudo systemctl enable mariadb
sudo systemctl start mariadb
sudo mysql_secure_installation

# PHP 8.5 + PHP-FPM + MySQL extension
sudo dnf install php8.5 php8.5-fpm php8.5-mysqlnd -y
sudo systemctl enable php-fpm
sudo systemctl start php-fpm
```

Verify each service:
```bash
sudo systemctl status nginx        # screenshot: active (running)
sudo systemctl status mariadb      # screenshot: active (running)
sudo systemctl status php-fpm      # screenshot: active (running)
php -v                             # screenshot: PHP 8.5.x
php -m | grep -i mysqli            # confirms mysqli extension loaded
```

📸 Screenshots: each `systemctl status` output, `php -v`

---

## 6. Database Creation
```bash
mysql -u root -paashu
```
```sql
CREATE DATABASE studentdb;
SHOW DATABASES;
```

📸 Screenshot: `SHOW DATABASES;` output including `studentdb`

---

## 7. Table Creation
```sql
USE studentdb;

CREATE TABLE students (
    Student_id   INT PRIMARY KEY AUTO_INCREMENT,
    Full_Name    VARCHAR(20) NOT NULL,
    email        VARCHAR(20) NOT NULL UNIQUE,
    College_Name VARCHAR(25) NOT NULL,
    Location     VARCHAR(20) NOT NULL,
    Event        VARCHAR(20),
    Password     VARCHAR(255) NOT NULL;
);

DESCRIBE students;
```

📸 Screenshots: `CREATE TABLE` success, `DESCRIBE students;` (before and after the ALTER)



## 8. Deploying the Website Files
```bash
cd /usr/share/nginx/html
sudo unzip campusconnect4.zip
```
File layout deployed:
```
/usr/share/nginx/html/campusconnect4/
├── index.html
├── register.html
├── login.html
├── css/style.css
└── backend/
    ├── config.php
    ├── register.php
    └── login.php
```

`backend/config.php` holds the DB credentials:
```php
$db_host = "";
$db_user = "";
$db_pass = "";
$db_name = "";
```

📸 Screenshot: `ls` of deployed folder structure

---

## 10. Final Testing
```bash
# Confirm PHP is executing (not 405/404)
curl -i -X POST http://localhost/backend/register.php

# Confirm Nginx and PHP-FPM are listening
sudo ss -tulnp | grep -E ':80|php-fpm'

# Confirm the database is reachable with the app's credentials
mysql -u root -paashu -e "USE studentdb; SELECT * FROM students;"
```

Website tested in browser at `http://54.83.160.102/`:
- Homepage loads
- Registration form submits → "Registration successful!" with generated Student ID
- Login with correct email/Student ID + password → "Welcome to CampusConnect!"
- Login with wrong password → "Invalid username or password."

📸 Screenshots: homepage, registration success, login success, invalid login,
`SELECT * FROM students;` showing the registered row
