#!/bin/bash
set -e

echo "=== Jewelry Store - Starting ==="

# Database directory (Railway volume or local)
DB_DIR="${RAILWAY_VOLUME_PATH:-database}"
mkdir -p "$DB_DIR/database"

# Run migration if DB doesn't exist
if [ ! -f "$DB_DIR/database/jewelry.db" ] || [ ! -s "$DB_DIR/database/jewelry.db" ]; then
    echo "Setting up database..."
    php setup.php
else
    echo "Database exists, skipping setup"
fi

echo "Starting PHP server on 0.0.0.0:$PORT"
php -S "0.0.0.0:${PORT:-8000}" -t .
