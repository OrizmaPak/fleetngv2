# fleetNG recovered source

This is a cleaned, recoverable Laravel source project derived from a production-style server archive. The untouched archive remains in the sibling `fleetngcom` directory.

## Current state

- Laravel 8 application with admin, merchant, driver, customer, trip, payment, expense, tracking, policy, SMS, and notification features.
- Dependencies, production logs, uploaded files, generated caches, database data, nested archives, and embedded private keys were removed from this copy.
- Missing domain migrations were reconstructed from model fields, controller writes, validation rules, and joins. See `RECOVERY_NOTES.md` for the limits of that reconstruction.
- Laravel 8 is end-of-life. This recovery keeps the original framework version to reduce behavioral changes; upgrading should be a separate project.

## Local setup with Docker

1. Start Docker Desktop.
2. Run `docker compose up -d --build`.
3. Run `docker compose exec app php artisan migrate`.
4. Open `http://localhost:8000`.

The container creates `.env`, installs Composer packages, and generates a local application key on first start. MySQL is exposed on host port `3307` for database tools.

To create a local administrator, set `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` in `.env`, then run:

```bash
docker compose exec app php artisan db:seed
```

Frontend assets can be rebuilt with Node.js:

```bash
npm ci
npm run production
```

The build scripts enable Node's legacy OpenSSL provider because the recovered Webpack version predates OpenSSL 3. This is limited to asset compilation and should be removed when the frontend toolchain is upgraded.

## Verified local launch

On Windows, the recovered app was also verified with PHP 8.2.33 by running the PHP built-in server directly:

```powershell
php -S localhost:8020 -t public server.php
```

The homepage returned HTTP 200 with the title `FleetNG - Your trusted logistic partner`. In this environment, Laravel's `artisan serve` wrapper could not bind to a port, while the direct PHP server command worked.

## External services

The application requires new credentials for the services actually used in a deployment. Configure them only in `.env`: Firebase/FCM, Flutterwave, Twilio, AWS S3, Sentry, and SMTP. Set `FIREBASE_CREDENTIALS` to a service-account JSON file stored outside the repository.

Never reuse credentials found in the original archive. Treat every original credential as compromised and rotate it at the provider.
