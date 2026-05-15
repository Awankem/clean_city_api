#!/bin/sh
set -e

# Set Nginx port from Render's $PORT env var
if [ -z "$PORT" ]; then
  PORT=80
fi
sed -i "s/%PORT%/$PORT/g" /etc/nginx/http.d/default.conf

# Link storage
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Run migrations in production
if [ "$APP_ENV" = "production" ]; then
    php artisan migrate --force
fi

exec "$@"
