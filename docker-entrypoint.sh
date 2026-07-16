#!/bin/bash
set -e

# Generate app key if not already set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Ensure SQLite database exists
touch /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/database.sqlite

# Run migrations and seed
php artisan migrate --force --seed 2>/dev/null || php artisan migrate --force

# Cache config and routes for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Apache
exec apache2-foreground
