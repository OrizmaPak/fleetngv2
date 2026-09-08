# Recovery notes

## What was recovered

The archive contains recognizable Laravel source code and Blade/frontend assets, so it is recoverable as a source project. This copy removes deployment residue and supplies the missing project-level files needed for repeatable setup.

The original migration set only described users, drivers, authentication support, SMTP, locations, and a small version of trips. Runtime code also depends on customers, device tokens, expenses, pages, SMS logs, trip payments, trip requests, draft trips, and additional columns on core tables. Those structures are reconstructed in `database/migrations`.

## What cannot be proven from the archive

- Exact production column types, indexes, constraints, defaults, and historical migration order for the reconstructed tables.
- Business rules or integrations that were never included in the code.
- Production parity without an authoritative schema-only export and current service contracts.
- Valid external credentials. The credentials in the archive must be considered compromised.

## Security and maintenance status

- Laravel 8 and parts of the frontend toolchain are end-of-life and require a planned upgrade.
- The original archive exposed database, SMTP, AWS, Firebase, and application secrets through caches, configuration, dumps, or credential files.
- Public notification test routes are now local-only, unconditional HTTPS forcing is production-only, TLS verification is restored, and execution-stopping debug calls were removed.
- Legacy seeders containing personal-looking records and fixed passwords were replaced with an opt-in local administrator seeder.

## Verification performed

- Composer dependencies installed.
- Frontend production assets compiled with the recovered Webpack/Laravel Mix toolchain.
- Laravel generated an application key and listed routes successfully.
- Reconstructed schema test passed with SQLite in memory.
- The homepage served locally with HTTP 200 from the PHP built-in server.

## Recommended next phase

Obtain a schema-only export from the live database, compare it to the reconstructed migrations, then add tests around trip creation, payment confirmation, driver assignment, authentication roles, tracking, and notification failure handling before upgrading Laravel.
