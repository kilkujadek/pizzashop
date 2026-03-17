#!/bin/bash

# Jeśli brakuje Slim Framework (folder vendor), instalujemy
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Pobieram Slim Framework (Composer install)..."
    if [ ! -f "composer.phar" ]; then
        curl -sS https://getcomposer.org/installer | php -- --quiet
    fi
    php composer.phar install --no-interaction --no-dev --prefer-dist
fi

# Worker w tle (10 min na pizzę)
(
  while true; do
    sleep 600
    curl -s http://localhost:8080/api/cron/process > /dev/null
  done
) &

# Odpalenie serwera PHP
echo "Backend gotowy na porcie 8080."
php -S 0.0.0.0:8080 -t .
