Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

Copy-Item .env.example .env -Force
Copy-Item laravel-app/.env.example laravel-app/.env -Force
Copy-Item lumen-api/.env.example lumen-api/.env -Force

Push-Location laravel-app
composer install
npm install
php artisan key:generate
php artisan migrate --seed
Pop-Location

Push-Location lumen-api
composer install
Pop-Location

Write-Host "Bootstrap completed. Run docker compose up --build or start both PHP servers manually."

