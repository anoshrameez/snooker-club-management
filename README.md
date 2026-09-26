# 🎱 CueMaster — Professional Snooker Club Management Web App

A production-ready, database-driven **Snooker Club Management Web Application** designed specifically for snooker, pool, and billiards club reception staff and club owners.

Built with a high-efficiency **Neo-Brutalist Business POS Interface** that maximizes operational speed, eliminates counter errors, and ensures zero data loss during active play.

---

## ⚡ Primary Reception Workflow

The entire counter operation is designed for single-screen execution:

$$\text{Customer Name} \longrightarrow \text{Table Selection} \longrightarrow \text{Start Session} \longrightarrow \text{Add Frames} \longrightarrow \text{Live Timer} \longrightarrow \text{Paid/Unpaid} \longrightarrow \text{Checkout} \longrightarrow \text{Save \& Print Slip}$$

* **Shopify-Style Round Selector**: Fast `[-] 1 [+]` quantity controls that scale to any number of frames without artificial caps.
* **Historical Pricing Protection**: Standard frame rates are configurable in Admin Settings (`Rs. 500/frame` by default). When a session starts, its applicable price is locked into the session record so future rate changes never alter historical accounting.
* **Live Playing Time Counter**: Automatic timer display ticking in real time, with server timestamps stored in MySQL as the absolute ground truth.
* **Instant Payment Toggle**: Prominent `[ UNPAID ]` / `[ PAID ]` buttons with instant AJAX database sync.
* **Autosave & Session Recovery**: Survives accidental browser refreshes, disconnects, or tab closures. The system actively detects any unfinished session and prompts staff with a `CONTINUE SESSION` recovery banner.

---

## 🛠️ Technology Stack

* **Backend**: PHP 8.2 / 8.3, Laravel 12/13 Framework
* **Database**: MySQL (Production / Hostinger) • SQLite (Local dev & test)
* **Frontend**: Laravel Blade, Tailwind CSS (Neo-Brutalist Design System), Fetch API (No page reloads for gameplay actions)
* **Icons & Typography**: FontAwesome 6, Space Grotesk, JetBrains Mono

---

## 🎨 Neo-Brutalist Design System

Engineered for fast-paced reception environments:
* **Bold high-contrast borders** (`border-2 border-black` / `border-3 border-black`)
* **Hard drop shadows** (`shadow-[4px_4px_0px_0px_#000]`)
* **Touch-friendly oversized buttons** and large status badges (`[ AVAILABLE ]`, `[ IN PLAY ]`, `[ PAID ]`, `[ UNPAID ]`)
* Clean white & warm neutral backgrounds with a signature electric yellow accent (`#FACC15`)
* **Zero clutter**: No floating gradients, glassmorphism, or distracting animations.

---

## 👥 Role-Based Access Control (RBAC)

1. **Administrator (`admin`)**:
   - Full access to everything
   - Configure frame rates & currency in Settings
   - Manage tables & maintenance states
   - Manage staff accounts and credentials
   - View financial reports and date-range analytics
   - View customer ledgers and session history
2. **Reception Staff (`staff`)**:
   - Start and manage active game sessions
   - Increment/decrement frames
   - Update payment status
   - Perform checkout and print thermal slips
   - View player directory and search past sessions
   - Restricted from altering pricing or administrative settings

---

## 🚀 Local Quickstart Guide

### 1. Requirements
* PHP 8.2 or 8.3 (with `pdo_mysql`, `pdo_sqlite`, `curl`, `mbstring`, `fileinfo`)
* Composer

### 2. Setup
```bash
# Navigate to project folder
cd snooker-club-laravel

# Copy environment file
cp .env.example .env

# Generate application encryption key
php artisan key:generate

# Run database migrations and seed default data
php artisan migrate --seed

# Start the local development server
php artisan serve
```

Access the application in your browser:
👉 **`http://localhost:8000`**

### 3. Demo Credentials
* **Admin**: `admin@snooker.club` / `admin123`
* **Staff**: `staff@snooker.club` / `staff123`

---

## 🌐 Hostinger Deployment

Full step-by-step instructions are provided in **[HOSTINGER_DEPLOYMENT.md](file:///C:/Users/DELL/.gemini/antigravity/scratch/snooker-club-laravel/HOSTINGER_DEPLOYMENT.md)**.

1. Set PHP version to **8.2 or 8.3** in Hostinger hPanel.
2. Create a MySQL database and user in Hostinger.
3. Upload project files (the included root `.htaccess` automatically routes to `public/`).
4. Update `.env` with Hostinger database credentials.
5. Run `php artisan migrate --seed --force`.
6. Run `php artisan config:cache && php artisan route:cache && php artisan view:cache`.

---

## 📂 Architecture Overview

```
snooker-club-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Login, Logout, Active session auth
│   │   │   ├── DashboardController.php  # Daily stats, active tables, recovery
│   │   │   ├── SessionController.php    # Lifecycle, rounds, AJAX, checkout
│   │   │   ├── CustomerController.php   # Ledger, search autocomplete API
│   │   │   ├── TableController.php      # Table inventory & maintenance
│   │   │   ├── ReportController.php     # Revenue analytics & date filters
│   │   │   ├── SettingController.php    # Pricing & business config
│   │   │   └── UserController.php       # Staff account management
│   │   └── Middleware/
│   │       └── AdminMiddleware.php      # Admin route authorization gate
│   └── Models/
│       ├── User.php                     # Admin & Staff roles
│       ├── Customer.php                 # Player profiles & lifetime ledger
│       ├── ClubTable.php                # Table states & active session link
│       ├── GameSession.php              # Frame counts, timer, locked rates
│       ├── Payment.php                  # Payment transaction ledger
│       └── Setting.php                  # System key-value store
├── database/
│   ├── migrations/                      # Relational schema migrations
│   └── seeders/
│       └── DatabaseSeeder.php           # Default tables, users, settings
├── resources/
│   └── views/
│       ├── layouts/app.blade.php        # Neo-Brutalist master shell
│       ├── auth/login.blade.php         # Brutalist login screen
│       ├── dashboard/index.blade.php    # Counter dashboard & active tables
│       ├── sessions/
│       │   ├── create.blade.php         # Check-in & table selection
│       │   ├── active.blade.php         # Live operator console & timers
│       │   ├── receipt.blade.php        # Thermal printable receipt slip
│       │   └── index.blade.php          # Filterable session history
│       ├── customers/                   # Profiles & balance breakdown
│       ├── tables/                      # Tables inventory & maintenance
│       ├── reports/                     # Yield analytics & date range stats
│       ├── settings/                    # Frame price configuration
│       └── users/                       # Staff management
├── routes/
│   └── web.php                          # Protected & role-gated routes
└── HOSTINGER_DEPLOYMENT.md              # Deployment guide for Hostinger
```
