#!/bin/sh
set -e

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Link storage
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Run migrations (only if DB is available)
# php artisan migrate --force

exec "$@"
