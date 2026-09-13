# Frontend implementation status

Updated: 2026-09-12. This is local implementation and verification evidence, not production release approval.

## Local review entries

- Review app: http://127.0.0.1:8022/
- Modern staff login: http://127.0.0.1:8022/user
- Offline customer preview: http://127.0.0.1:8022/customer-preview/
- Backend-connected customer portal: http://127.0.0.1:8022/customer-portal

The review homepage uses the recovered public homepage template from `resources/views/front/index.blade.php`, matching the separate legacy homepage served on port 8095. The modern theme does not override `/`; it only supplies the staff/customer operational surfaces.

The review server uses an isolated SQLite database at `storage/app/review/review.sqlite`, a separate session cookie and local/test provider configuration. It does not mutate the original recovered database.

## Review identities

All seeded review users use password `FleetNG-Review-2026!`.

| Role | Login | Entry |
| --- | --- | --- |
| Superadmin | `superadmin@review.fleetng.test` | `/superadmin` |
| Admin | `admin@review.fleetng.test` | `/admin` |
| Company user | `company@review.fleetng.test` | `/user` |
| Payment user | `payment@review.fleetng.test` | `/user` |

Customer review login:

- Phone: `8000000000`
- Mock OTP: `123456`

The OTP hint is intentionally visible in local/testing because no original OTP provider or source repository was available. Production customer login remains closed until a real OTP provider contract replaces the mock.

## Completed implementation work

| Area | Current state |
| --- | --- |
| Review environment | `scripts/start-review.ps1` and `php artisan fleetng:prepare-review` build an isolated review database with synthetic users, customers, drivers, trips, policies, expenses and manifests |
| Route/view audit | 224 registered routes, 107 audited views and zero missing controller handlers |
| Public homepage | `/` renders the recovered MasterSlider homepage with `User Login` and `Customer Login`, not the modern placeholder homepage |
| Staff theme | Modern staff shell renders the inspected operational staff pages while preserving legacy URLs, forms and route names |
| Staff access | The public staff entry is one User Login at `/user`; superadmin, admin, company user and payment user are detected after sign-in and routed by role. Legacy role-specific login URLs remain as compatibility aliases, not separate public products |
| Customer preview | Offline customer preview covers overview, trips, drafts, booking, assignment, payment states, profile, login/logout/reset, filtering, paging and responsive states without business writes |
| Customer portal | Backend-connected customer session flow uses local mock OTP, separate customer session identity, CSRF refresh after session rotation and no browser bearer-token storage |
| Customer ownership | Customer reads and mutations are scoped by active customer identity for trips, drafts, driver assignment, payment IDs and profile image access |
| Trip/draft workflow | Draft confirmation is transactional and idempotent; direct staff trip creation validates cost fields, existing/new customer branches and driver ownership |
| Payments | Customer checkout intent storage, provider verification service, payment-return UI, disabled-local checkout messaging and staff bank/POS payment retry handling are implemented and tested with mocked providers |
| Staff self-HTTP | Internal staff API calls now execute through local controller dispatch instead of calling the app's own HTTP server |
| Uploads | Staff/customer image upload paths validate image type/size and serve protected local files in review |
| Locations | Pickup add/edit use explicit latitude/longitude fields and no longer geocode through hardcoded Google calls |
| Tracking | Provider credentials now come from config, staff tracking views/API results are scoped by authorized active vehicle serials and provider-disabled states are handled |
| Reports/PDFs | Report filters reject foreign driver-ID bypasses; invoice and report PDF downloads render with real Dompdf output in tests |
| Public/retained pages | Public policy/contact/password surfaces compile under the current route/view audit; retained PDF outputs are visually inspected from rendered PNGs |

## Verification evidence

| Check | Result |
| --- | --- |
| PHPUnit feature suite | 37 tests, 435 assertions passed; focused frontend migration subset: 7 tests, 271 assertions |
| Interface audit | 224 routes, 107 views, zero missing controller handlers |
| View compilation | `php artisan view:cache` passed |
| Offline customer browser suite | Passed desktop, mobile, assets, search/filter/paging, demo payments, drafts, save/edit/confirm, driver assignment, reload, profile, login/logout/reset and empty states |
| Customer OTP browser suite | Passed real local OTP request, wrong code, `123456` verification, failure recovery, visible hint and responsive checks |
| Full review browser suite | Passed 39 staff pages, six mobile staff screens, all four staff roles, real customer session, photo upload, booking confirmation, disabled checkout, forged payment return and separate staff/customer sessions |
| PDF rendering | Invoice and report PDFs rendered to PNG and were visually checked for layout, totals and reference/detail content |
| Diff hygiene | `git -c core.autocrlf=false diff --check` passed at the final checkpoint |

Generated review artifacts live under `storage/app/review/` and are intentionally ignored from source control.

## Remaining release gates

These are not ordinary user-click-through tasks; they are external or production-grade release prerequisites.

1. Replace mock customer OTP with a real provider integration and keep the visible `123456` hint out of production.
2. Configure and verify live payment, SMS, map/tracking, image storage, mail and push providers in staging with real credentials.
3. Run the migrations and workflow tests on the production database engine/schema, not only SQLite review fixtures.
4. Complete a full restore rehearsal from the final checkpoint, including dependencies, storage, database, caches and review workflows.
5. Run stakeholder acceptance against every required interface ID in `frontend-parity-migration-plan.md`; the automated suite covers the major routes and submissions but is not a manual sign-off for all 77 interface entries.
6. Test high-volume report/PDF boundaries, especially long content and the legacy 550-row report limit.
7. Reconcile any live provider edge cases: duplicate checkout-intent reuse, provider failed/pending callbacks, notification delivery failures and tracking-provider timeout behavior.
8. Rotate any recovered legacy credentials outside this repo before production use.

## Reproduce checks

Use the review server on port 8022. On this Windows installation the PHP tests require explicit extension flags:

```text
php -d extension_dir="C:/Users/Oreva/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe/ext" -d extension=openssl -d extension=pdo_sqlite -d extension=sqlite3 -d extension=mbstring -d extension=fileinfo vendor/phpunit/phpunit/phpunit
php artisan fleetng:interface-audit --write
php artisan view:cache
node tests/customer-preview.test.cjs
node tests/customer-portal-otp.test.cjs
node tests/review-browser.test.cjs
```

When using the bundled Node runtime, set `NODE_PATH` to:

```text
C:\Users\Oreva\.cache\codex-runtimes\codex-primary-runtime\dependencies\node\node_modules
```

## Rollback and checkpoint notes

Final checkpoint:

- `C:\Users\Oreva\Desktop\oreva\MIRA\FLEETNG V2\backups\login-unification-20260913-final\source.zip`
- `C:\Users\Oreva\Desktop\oreva\MIRA\FLEETNG V2\backups\login-unification-20260913-final\database.sqlite`
- `C:\Users\Oreva\Desktop\oreva\MIRA\FLEETNG V2\backups\login-unification-20260913-final\review.sqlite`
- `C:\Users\Oreva\Desktop\oreva\MIRA\FLEETNG V2\backups\login-unification-20260913-final\environment.private`
- `C:\Users\Oreva\Desktop\oreva\MIRA\FLEETNG V2\backups\login-unification-20260913-final\checkpoint-manifest.json`

The source archive excludes `.git`, `vendor`, `node_modules`, `.env`, live SQLite files and generated storage/cache/log directories. The local and review SQLite backups passed `PRAGMA integrity_check`; selected checkpoint files were extracted into the checkpoint's `restore-check` directory as a sanity check.

`UI_THEME=legacy` selects stock views and `UI_THEME=fleetng-modern` selects the modern view overrides. Theme rollback does not undo controller, route, migration or service changes; use a matching source checkpoint for a true code rollback.

Never restore an old database over legitimate new bookings/payments merely to revert a frontend. Roll back code/assets/theme separately, then reconcile any affected financial or booking events.

No public hosting target, customer-domain switch or live-provider activation was supplied or performed.
