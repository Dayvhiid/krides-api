#!/bin/bash

# Install PHP and Composer
apt-get update
apt-get install -y php-cli php-mbstring unzip curl composer

# Install dependencies
composer install --optimize-autoloader --no-dev

# Laravel-specific tasks
php artisan config:cache
php artisan route:cache
php artisan view:cache
