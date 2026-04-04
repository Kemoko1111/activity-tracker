# Activity Tracker

A Laravel-based web application for tracking daily activities of an application support team. Built for shift handover clarity, audit trails, and team accountability.

---

## Features

### Core
- **Activity Management** — Create, edit, and manage trackable daily activities (admin only)
- **Status Updates** — Team members update each activity as Done or Pending with remarks
- **Immutable Audit Trail** — Every update is appended as a new log entry, never overwritten
- **Daily Activity View** — View all activities for any date with full log history
- **Shift Handover Summary** — At-a-glance Done/Pending/Total counts for the current day

### Reports
- **Date Range Filtering** — Filter activity logs by custom date ranges
- **Activity & Status Filters** — Narrow reports to specific activities or statuses
- **Summary Statistics** — Total logs, done/pending counts, unique days, unique users
- **CSV Export** — Download filtered reports as CSV files

### Authentication & Access Control
- **User Registration** — Self-service account creation (defaults to `member` role)
- **Login / Logout** — Secured via Laravel Breeze
- **Role-Based Access** — `admin` and `member` roles
  - **Admin**: Full CRUD on activities + all member capabilities
  - **Member**: View activities, submit status updates, view reports

---

## Tech Stack

| Layer        | Technology                   |
|-------------|------------------------------|
| Framework   | Laravel 13.x                 |
| PHP         | 8.3+                         |
| Frontend    | Blade Templates, Tailwind CSS |
| JS          | Alpine.js (via Breeze)       |
| Database    | SQLite (default)             |
| Auth        | Laravel Breeze               |
| Build       | Vite                         |

---

## Installation

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+ & npm

### Setup

```bash
# 1. Clone the repository
git clone <repo-url> activity-tracker
cd activity-tracker

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Create database & run migrations + seed
touch database/database.sqlite
php artisan migrate --seed

# 6. Build frontend assets
npm run build

# 7. Start the development server
php artisan serve
```

The app will be available at `http://localhost:8000`.

---

## Default Accounts

| Role   | Email               | Password  |
|--------|---------------------|-----------|
| Admin  | admin@tracker.com   | password  |
| Member | john@tracker.com    | password  |
| Member | jane@tracker.com    | password  |

> **Note**: The seeder creates 10 sample activities with log entries for today and yesterday.

---

## Project Structure

```
activity-tracker/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ActivityController.php      # CRUD for activities (admin)
│   │   │   ├── ActivityLogController.php   # Status updates & daily view
│   │   │   ├── DashboardController.php     # Dashboard summary
│   │   │   ├── ReportController.php        # Reports & CSV export
│   │   │   └── ProfileController.php       # User profile (Breeze)
│   │   └── Middleware/
│   │       └── AdminMiddleware.php         # Admin-only route protection
│   └── Models/
│       ├── Activity.php                    # Trackable activity
│       ├── ActivityLog.php                 # Immutable status log entry
│       └── User.php                        # User with role support
├── database/
│   ├── migrations/                         # Schema definitions
│   └── seeders/
│       └── DatabaseSeeder.php              # Demo data
├── resources/views/
│   ├── activities/                         # CRUD views (index, create, edit)
│   ├── auth/                               # Login, register, etc.
│   ├── components/                         # Breeze UI components
│   ├── daily/
│   │   └── show.blade.php                  # Daily activity view
│   ├── layouts/
│   │   └── navigation.blade.php            # Main navigation bar
│   ├── profile/                            # Profile management
│   ├── reports/
│   │   └── index.blade.php                 # Reports with filters
│   ├── dashboard.blade.php                 # Main dashboard
│   └── welcome.blade.php                   # Landing page
└── routes/
    └── web.php                             # All application routes
```

---

## Routes

### Public
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/` | Landing page (redirects to dashboard if logged in) |

### Authenticated (all users)
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/dashboard` | Today's activity overview |
| GET | `/daily/{date?}` | Daily view for a specific date |
| POST | `/activities/{id}/log` | Submit a status update |
| GET | `/activities` | List all activities |
| GET | `/reports` | Reports page |
| POST | `/reports` | Generate filtered report |
| POST | `/reports/export` | Export report as CSV |

### Admin Only
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/activities/create` | Create new activity form |
| POST | `/activities` | Store new activity |
| GET | `/activities/{id}/edit` | Edit activity form |
| PUT | `/activities/{id}` | Update activity |
| DELETE | `/activities/{id}` | Delete activity |

---

## Database Schema

### users
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | User's full name |
| email | string | Unique email |
| password | string | Hashed password |
| role | string | `admin` or `member` |

### activities
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | string | Activity name |
| description | text | Optional description |
| category | string | Grouping category (e.g., Monitoring, Support) |
| is_active | boolean | Whether the activity appears in daily views |

### activity_logs
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| activity_id | foreignId | Links to activities table |
| user_id | foreignId | Who made the update |
| date | date | The date this log is for |
| status | string | `done` or `pending` |
| remark | text | Optional remark/note |
| created_at | timestamp | Exact time of update |

---

## Key Design Decisions

### Immutable Logs
Activity logs are **append-only**. When a user updates a status, a new `ActivityLog` record is created — existing logs are never modified. This provides:
- A complete audit trail of every change
- Clear history of who did what and when
- Reliable shift handover documentation

### Role-Based Access
- `AdminMiddleware` gates sensitive CRUD operations
- New registrations default to `member` role for security
- Admin badge displayed in navigation for admin users

### Date Handling
The `ActivityLog` model casts the `date` column to a Carbon instance. All query scopes use `whereDate()` instead of `where()` to ensure correct date comparison regardless of timezone or formatting.

---

## Development

```bash
# Run the dev server with hot-reload
php artisan serve &
npm run dev

# Rebuild CSS after changing Blade templates
npm run build

# Reset and re-seed the database
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear
```

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
