#!/bin/bash
#
# Deploy / update the app on a server. Safe to run repeatedly.
#
#   First time:  cp .env.example .env, fill it in, then run bin/install.sh
#   Updates:     bin/install.sh
#

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# Output running commands and fail on non-zero
set -e
set -x

# Move out of the bin/ directory
cd "$DIR/.."

if [ ! -f .env ]; then
    echo "No .env file found. Copy .env.example to .env and fill it in first."
    exit 1
fi

php artisan down || true

if [ -d .git ]; then
    git pull --ff-only
fi

composer install --no-dev --optimize-autoloader --no-interaction

if ! grep -q '^APP_KEY=base64' .env; then
    php artisan key:generate --force
fi

# SQLite: make sure the database file exists
if grep -q '^DB_CONNECTION=sqlite' .env && [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

php artisan migrate --force

npm ci
npm run build

php artisan optimize
php artisan queue:restart

php artisan up
