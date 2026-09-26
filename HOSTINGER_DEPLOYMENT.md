# 🚀 Hostinger Deployment Guide for Snooker Club Management App

This application is built with **PHP 8.2/8.3**, **Laravel**, and **MySQL**, designed to run smoothly on standard **Hostinger Web Hosting (Cloud, Premium, Business)** and **Hostinger VPS**.

---

## 📋 Pre-Flight Checklist

1. A Hostinger hosting plan with domain/subdomain.
2. PHP version set to **8.2 or 8.3** in Hostinger hPanel.
3. A MySQL database created in Hostinger hPanel.

---

## 🛠️ Step-by-Step Deployment Instructions

### Step 1: Set PHP Version in Hostinger
1. Log in to **Hostinger hPanel**.
2. Go to **Advanced** ➔ **PHP Configuration**.
3. Select **PHP 8.2** or **PHP 8.3**.
4. Verify that the following extensions are enabled (enabled by default in Hostinger):
   - `pdo_mysql`, `curl`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`.
5. Click **Save**.

---

### Step 2: Create MySQL Database
1. In hPanel, go to **Databases** ➔ **Management**.
2. Create a new MySQL database:
   - **Database Name**: e.g., `u123456789_snooker`
   - **Username**: e.g., `u123456789_admin`
   - **Password**: *[Create a strong password]*
3. Copy these credentials for your `.env` configuration.

---

### Step 3: Upload the Application Files

#### Method A: Git Deployment (Recommended)
If you have Git enabled in Hostinger hPanel:
1. In hPanel, go to **Advanced** ➔ **GIT**.
2. Create repository deployment linked to your branch.
3. Set install path to `public_html` or a root folder.

#### Method B: Zip & Upload via File Manager
1. In your local project directory, zip the contents (exclude `node_modules` and `.git`).
2. In hPanel, open **Files** ➔ **File Manager**.
3. Upload and extract the zip archive into `public_html/`.
4. *(A root `.htaccess` is already included that seamlessly maps requests to `/public`)*.

---

### Step 4: Configure the `.env` File
In Hostinger File Manager:
1. Locate or create `.env` in your project root.
2. Paste the following configuration, updating with your real Hostinger database credentials:

```dotenv
APP_NAME="CueMaster Snooker Club"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Hostinger MySQL Database Settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_snooker
DB_USERNAME=u123456789_admin
DB_PASSWORD=YourDatabasePasswordHere

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

---

### Step 5: Run Database Migrations & Seeds

#### Using Hostinger SSH Terminal:
1. Connect via SSH (hPanel ➔ **Advanced** ➔ **SSH Access**):
   ```bash
   cd public_html
   
   # Generate key if not already set
   php artisan key:generate --force
   
   # Run migrations and seed default tables, settings & staff accounts
   php artisan migrate --seed --force
   ```

#### If SSH is Not Available (Web Route Alternative):
You can also run migrations via a temporary cron job in Hostinger:
- Command: `cd /home/u123456789/domains/your-domain.com/public_html && php artisan migrate --seed --force`
- Run once and delete the cron job.

---

### Step 6: Production Performance Optimization
Run the following standard Laravel optimization commands in SSH:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔑 Default Login Credentials

Once seeded, you can immediately log in:

| Role | Email | Password | Permissions |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@snooker.club` | `admin123` | Full access (Rates, Settings, Staff, Tables, Reports) |
| **Reception Staff**| `staff@snooker.club` | `staff123` | Reception operations (Check-in, Rounds, Payments, Checkout) |

> [!IMPORTANT]
> Immediately change default passwords from the **Staff & Users** section after first login.
