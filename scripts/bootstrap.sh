#!/usr/bin/env bash
set -euo pipefail

cp .env.example .env
cp laravel-app/.env.example laravel-app/.env
cp lumen-api/.env.example lumen-api/.env

(
  cd laravel-app
  composer install
  npm install
  php artisan key:generate
  php artisan migrate --seed
)

(
  cd lumen-api
  composer install
)

echo "Bootstrap completed. Run docker compose up --build or start both PHP servers manually."

