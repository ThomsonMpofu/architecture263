#!/bin/bash

# Install PHP dependencies if vendor directory is missing
if [ ! -d "vendor" ]; then
    composer install
fi

# Install Node dependencies if node_modules is missing
if [ ! -d "node_modules" ]; then
    npm install
    npm run build
fi

# Copy .env if it doesn't exist
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Generate app key if not set
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\s*$" .env; then
    php artisan key:generate
fi

# Wait for database to be ready
echo "Waiting for database connection..."
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); } catch (PDOException \$e) { exit(1); }" > /dev/null 2>&1; do
  echo "Database is unavailable - sleeping"
  sleep 1
done

# Run migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm
