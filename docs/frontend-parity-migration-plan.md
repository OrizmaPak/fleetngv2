# FleetNG frontend parity and migration plan

Prepared: 10 September 2026. Status: baseline scope/register. The current local implementation and verification status is tracked in `docs/frontend-implementation-status.md`.

## 1. Objective and fixed decisions

Create a complete dark-interface equivalent of the working software before integrating the new interfaces. Coverage includes each active page, role-specific variant, form, dialog, table action, dependent selector, download and failure state. A screen is not complete merely because it renders: every existing submission must have an equivalent control and documented outcome.

The primary design reference is the existing `fleetng-modern` theme shown at `/user`, as clarified by the user. The separate FLEETNG React demo is a secondary source of interaction ideas, not a working backend or the primary design specification. The customer preview supplies a proposed customer experience derived from the recovered customer APIs.

Implementation defaults:

- Extend the existing Laravel Blade theme for staff. Preserve URLs, route names and legacy response formats. Do not rewrite the platform in React or upgrade Laravel as part of this migration.
- Create new staff equivalents in the modern theme, with a local/testing-only fixture gallery that can render forms and outcomes without production writes. Keep the running stock application available as the behavioral reference.
- Keep the customer preview isolated until the customer integration prerequisites are satisfied. Preserve the separately hosted customer portal while its replacement is developed; do not change `CUSTOMER_PORTAL_FRONTEND_URL` to the demo.
- Share visual tokens and component contracts across customer and staff interfaces. Do not merge their identities, permissions or authentication mechanisms.
- Use dark as the initial modern appearance. Retain any existing light-mode preference only where both appearances pass testing; changing appearance must not change behavior.
- Retain business functionality and data meaning. Known existing bugs are documented separately and repaired with focused tests; they are not silently accepted as the desired behavior.
- Preserve mobile-driver API consumers. A replacement driver mobile app, new merchant self-service product, speculative notification center, extra analytics and unsupported exports are outside this interface migration.

## 2. Verified baseline and evidence limits

The working repository is `fleetngcom-recovered`. Route definitions, controller return views, Blade forms, page scripts, role/menu configuration, response builders, customer controllers and theme components were inspected. This is a code-grounded inventory, not proof that all recovered routes operate successfully against production data.

| Surface | What exists | What is not yet established |
| --- | --- | --- |
| Stock staff application | Laravel routes, legacy views and domain behavior | End-to-end success for every recovered flow and external service |
| Modern staff theme | Layouts, login/recovery views, two dashboards, client list, public/state pages | Complete operational coverage and authenticated parity tests |
| Modern data table | Structured client-list response; search, sorting, paging, density and UI states | General action handlers, business selection, filters, aggregates and adapters for other lists |
| Original customer frontend | Public entry at `client.fleetng.com/login`; deployment references in archive | Editable source and authenticated screens were not recovered or inspected |
| Customer preview | Fixture-driven overview, trips, drafts, booking, assignment, simulated payments, profile and account entry | Live authentication, live API transport, OTP enforcement, image upload, push registration, token revocation and real payment return |
| Existing verification | Prior PHP run: 3 tests / 11 assertions; customer preview browser workflow checks passed | Full staff-role regression coverage, authoritative production-schema parity and provider integration tests |
| Existing backup | Verified Git bundle at `../backups/before-customer-preview-20260909.bundle` | It excludes ignored files, database data, uploads and the subsequently created untracked preview files |

`config/view.php` globally prepends the modern view directory when `UI_THEME=fleetng-modern`. Missing modern pages fall back to legacy views, but can still inherit modern layouts. That fallback is useful for development, not evidence of equivalent interfaces. Do not enable the global theme on production while required pages remain unreviewed.

### Role coverage

| Identity | Baseline surface | Required treatment |
| --- | --- | --- |
| Superadmin, user type 1 | Superadmin dashboard, merchants, users, policies; shared clients/drivers; password settings | Dedicated fixtures and permission checks for all visible actions and direct URLs |
| Admin, user type 2 | Company dashboard, user management, clients, drivers, locations, trips, payments, tracking, reports and settings | Preserve verified operational scope; reconcile inconsistencies between menus and record-scoping helpers |
| Company user, user type 3 | Assigned-driver operations, clients, trips, locations, tracking, reports, expenses and settings | Never expose admin user-management links or another company's records |
| Payment user | `is_payment_user` and merchant assignment; legacy type-5 references also exist | Test the actual flag/type combinations used by recovered login and creation paths; restricted navigation and payment/trip actions |
| Merchant, user type 4 | Merchant entities and scoping helpers exist; no complete menu/login journey is established | Cover merchant management under superadmin. Do not invent a standalone merchant portal; record any confirmed deployed merchant journey before extending scope |
| Customer | Separate Sanctum customer APIs and separate hosted frontend | Separate customer-only route authorization and customer ownership checks |
| Driver app | Existing driver APIs | Regression-check shared contracts; no new driver-browser navigation inferred from the demo's misleading login label |

Menu visibility is not a permission policy. Resolve disagreement using verified intended ownership and deployment evidence; default to no additional privilege until resolved. Unresolved access decisions block the affected integration, not the creation of fixture-based interface equivalents.

## 3. Interface register

Status codes: **P** = modern implementation exists but parity is unproven; **M** = modern equivalent missing; **D** = customer demo only; **B** = known baseline defect; **R** = retain and regression-check rather than redesign. IDs identify review and test cases. These are interface entries, not a claim that each entry is a distinct backend route.

### Staff access and shell

| ID | Current interface / route | Status | Required equivalent and completion condition |
| --- | --- | --- | --- |
| A01 | Company-user login `/user` | P | Email/password, remember preference, password visibility, invalid credentials, inactive/wrong-role outcome, validation and correct dashboard redirect |
| A02 | Admin login `/admin`, alias `/login` | P | Same auth component with admin endpoint/title; preserve both entry URLs |
| A03 | Superadmin login `/superadmin` | P | Role-specific entry and superadmin redirect; no customer identity reuse |
| A04 | Forgot password `/forgot-password` | P | Email submission, success/error feedback, back-to-login path and pending state |
| A05 | Reset password `/reset-password/{token}` | P | Password/confirmation, token and role/type retention, invalid/expired token and post-reset destination |
| A06 | Logout `/logout` | R | Visible account action, role-correct return, ended session and back-button behavior |
| A07 | Sidebar, navbar, account menu and breadcrumbs | P | Role-aware navigation, active parent on detail/edit pages, mobile close/Escape, desktop collapse AND expand, keyboard focus and consistent identity |
| A08 | Global search `/ui/search` | P | Scoped results, keyboard navigation, empty/loading/error states; links only to accessible resources |
| A09 | Common feedback / dialogs | M | Standard success/error notices, loading, retry, validation summary, destructive confirmation, stale-record and unavailable-service states |

### Company operations

| ID | Current interface / route | Status | Required equivalent and completion condition |
| --- | --- | --- | --- |
| O01 | Dashboard `/analytics` | P | All existing trip, driver, user and finance measures; role/payment-user variants; accurate links and charts without invented trends |
| O02 | Users `/user-list` | M | Directory, current filters, status, assignment, actions and row totals; admin-only exposure |
| O03 | Add user `/user/add` | M | Merchant where applicable, names, phone, email, photo/crop inputs and creation outcome; retain conditional field behavior |
| O04 | Edit user `/user/edit/{id}` | M | Prefill existing fields and assignment/image state; validation retains input; successful save returns to the correct directory |
| O05 | View user `/user/view/{id}` | M | Identity, assignment, status and all current linked operations, including trips |
| O06 | User trips `/user-trip-list/{id}` | M/B | Scoped trip table. Controller references `admin/user-management/user-trip-list`, but the inspected view tree contains `driver-trip-list` instead; resolve explicitly before integration |
| O07 | Drivers `/driver-list` | M | Merchant/user, name/phone, vehicle, trip count, PIN-related action, device/activity and status fields with role variants |
| O08 | Add driver `/driver/add` | M | Merchant -> user dependency; names, phone, image/crop, vehicle ID, login PIN, IMEI, voice number and billing term |
| O09 | Edit driver `/driver/edit/{id}` | M | Same supported fields and image retention; preserve current IDs and dependent assignment behavior |
| O10 | View driver `/driver/view/{id}` | M | Driver identity/image, assignment, vehicle/device details, status and links to trips; no fictional documents tab |
| O11 | Driver trips `/driver-trip-list/{id}` | M | Driver-scoped trip history, financial/status columns, filtering/paging and correct trip links |
| O12 | Clients `/client-list` | P | Existing modern directory plus verified search/sort/scope parity; edit/status/delete only where authorized |
| O13 | Edit client `/client-edit/{id}` | M | First/last name, phone and email with duplicate-field errors and superadmin-only submission |
| O14 | Trips `/trip-list` | M | Complete list/filter/action parity, role/payment-user variants, financial columns and eligible payment selection |
| O15 | Add trip `/trip/add` | M | Pickup datetime, driver, pickup and drop location, trip/material/road costs; existing-client selector OR new-client name/country code/phone/email; preserve current permission rules |
| O16 | Trip detail `/trip/detail/{id}` | M | Current route, timestamps, duration, client, driver, financial breakdown, assignment, payment actions and invoice link |
| O17 | Trip location `/trip-live-location/{id}` | M | Existing vehicle-location data with loading, no-device/no-position, stale data and provider failure states |
| O18 | Locations / Geofencing `/location-list` | M | Pickup-location directory, aliases, status and actions; preserve actual location functionality without inventing polygon geofences |
| O19 | Add pickup `/pickup-location/add` | M | Location name, address lookup and supported coordinates/map interaction; preserve current submitted location fields |
| O20 | Edit pickup `/pickup-location/edit/{id}` | M | Prefilled address/alias/coordinates and status; existing map and validation behavior |
| O21 | View pickup `/pickup-location/view/{id}` | M | Read-only location information and actual map data; edit/back links where authorized |
| O22 | Expenses `/expenses` | M | Phone/item/driver/date filters, quantity/cost, filtered expense aggregate and edit/delete dialogs; no invented staff add-expense route |
| O23 | Payments `/payment-list` | M | Current transaction fields, authorized customer scope, trip links and payment states; preserve underlying amounts and references |
| O24 | Live tracking `/live-tracking` | M | Vehicle/device directory, driver/status filters, location/activity/distance fields and detail links |
| O25 | Vehicle tracking `/map-tracking/{id}` | M | Existing provider-backed vehicle view and permitted requests; no illustrative route passed off as telemetry |
| O26 | Map history `/map-history` | M | Existing vehicle/time query and history response; loading, empty history and provider failure; only supported export/playback controls |
| O27 | Reports `/reports` | M | Truck, date range, trip type, customer and payment type; full existing financial summaries, detail rows and PDF request |
| O28 | Report PDF `/report-pdf-download` | R | Preserve filter equivalence, totals, pagination/print layout, empty-result feedback and existing 550-row limit; do not apply dark browser CSS to PDF output |
| O29 | Trip invoice `/trip/{id}/invoice/pdf-download` | R | Preserve actual invoice data and PDF layout; verify record authorization before retaining links |
| O30 | Account settings `/account-settings` | M | Old/new/confirm-password fields, visibility, errors and success; actual old/new payload names remain compatible |

### Superadmin operations

| ID | Current interface / route | Status | Required equivalent and completion condition |
| --- | --- | --- | --- |
| S01 | Dashboard `/superadmin/analytics` | P | Account/merchant counts, active/inactive values, scoped links and meaningful empty data |
| S02 | Users `/superadmin/user-list` | M | Separate superadmin directory and filter endpoints, merchant association, payment-user state and allowed account actions |
| S03 | Add user `/superadmin/user/add` | M | Merchant selection, names, phone, email and payment-user checkbox; preserve account-creation effects |
| S04 | Edit user `/superadmin/user/edit/{id}` | M | Correct prefilled identity, merchant and payment-user fields with retained validation input |
| S05 | View user `/superadmin/user/view/{id}` | M | Account details and supported linked operations; avoid unrouted legacy trip links |
| S06 | Merchants `/superadmin/merchant-list` | M/B | Directory/filter/actions; status/delete routes currently name methods not present in the inspected controller |
| S07 | Add merchant `/superadmin/merchant/add` | M | Contact names, company name/address, phone and email; preserve credential/notification outcome |
| S08 | Edit merchant `/superadmin/merchant/edit/{id}` | M | Existing organization/contact data, validation and successful return |
| S09 | View merchant `/superadmin/merchant/view/{id}` | M | Current contact/company/assignment information and supported actions |
| S10 | Refund-policy editor `/superadmin/pages/refund-policy` | M | Existing rich text, `body` submission, save feedback and public-page rendering |
| S11 | Privacy-policy editor `/superadmin/pages/privacy-policy` | M | Same editor component; distinct content and existing route |
| S12 | Terms editor `/superadmin/pages/terms-conditions` | M | Same editor component; preserve stored legal content and output |
| S13 | Password settings `/superadmin/account-settings` | M | Superadmin shell and current password-change destination; check shared POST authorization and correct back-link |

### Customer reconstruction

Detailed payload evidence and the live-integration gaps are recorded in [customer-portal-preview.md](customer-portal-preview.md). Customer API paths in this table are relative to `/api/customer`; the existing demo uses hash navigation and is not the final payment-return router.

| ID | Proposed customer interface | Status | Existing contract and remaining equivalent |
| --- | --- | --- | --- |
| C01 | Existing-customer entry | D | `POST /verify-phone`, `/login`; build pending/error/session-expired states and a genuine identity-verification integration before enabling real login |
| C02 | New-customer registration | D | `POST /verify-details`, `/register`; field-level uniqueness, name/email/phone/country code and verified identity |
| C03 | OTP challenge and resend | M | Original public portal shows OTP; no recovered OTP-verification endpoint. Create the interface states in fixtures; source/provider/server verification is an explicit integration dependency |
| C04 | Overview | D | `GET /stats`, `/trips`, `/trip-payments`; accurate totals, loading/empty/error and recent bookings/payments; do not label base-trip totals as paid spend |
| C05 | My trips | D | `GET /trips`; all status variants, search/filter/sort/paging and record selection; null driver/payment relationships |
| C06 | Booking: trip details | D | `GET /pickup-locations`; pickup ID, datetime, drop-off and proposed cost; validation and retained progress |
| C07 | Booking: merchant/driver | D | `GET /merchants`; optional assignment, no available driver, merchant change and dependent selection clearing |
| C08 | Booking: review/confirmation | D | Draft create/update followed by `/draft/trips/{id}/confirm`; create pending/success/failure/uncertain-result equivalents and duplicate-click handling |
| C09 | Draft directory | D | `GET /draft/trips`; do not invent delete-draft action without a backend route |
| C10 | Open/edit draft | D | `GET` and `POST /draft/trips/{id}`; retain IDs including drop-location ID; save and reload all fields |
| C11 | Trip detail | D | `GET /trips/{id}`; actual route, status, financial components, payment and assignment; missing/forbidden record states |
| C12 | Driver assignment dialog | D | `PATCH /trips/{id}/driver`, draft-specific assignment endpoint; agree and enforce allowed trip states server-side |
| C13 | Checkout review / gateway handoff | D | `POST /trips/payment-link` with `trip_ids`; real handoff, paid/invalid selections, amount verification, cancellation and failure equivalents |
| C14 | Payment return / result | M | Backend web callback `/customer/trips/payments` redirects to frontend `/trip/payments`; handle success/pending/failure and refetch authoritative payment state |
| C15 | Payment history | D | `GET /trip-payments`; amount/reference/status/date/trip relationships and empty/error states |
| C16 | Profile and image | D/M | `GET/POST /profile`; text fields have a demo; add photo selection/preview/upload/error with multipart `profile_image` support |
| C17 | Logout and expired session | D/M | Demo sign-out exists; add real revocation contract and return-to-login handling before production |
| C18 | Push-permission/device registration states | M | `/save-device-token`; consent/decline/unsupported/error equivalents only for confirmed push behavior; do not fabricate a notification inbox |

### Shared/public and retained outputs

| ID | Surface | Status | Treatment |
| --- | --- | --- | --- |
| X01 | Public homepage `/` and its login links | R/P | Leave current marketing experience intact; verify theme isolation and correct staff/customer entry links |
| X02 | Contact `/contact-us` and `POST /submit-contact-us` | P | Preserve form fields, validation, submit outcome and mail behavior; regression coverage required |
| X03 | Public refund policy `/refund-policy` | P | Correct saved content, typography and navigation |
| X04 | Public privacy policy `/privacy-policy` | P | Correct saved content; duplicate route declaration is baseline cleanup, not a new screen |
| X05 | Public terms `/terms-conditions` | P | Correct saved content and navigation |
| X06 | `/error`, 403, 404, 419/session expired, 500 and maintenance | P/M | Existing decorative state templates are not automatically wired to framework exceptions; create/verify actual error response views and safe destinations |
| X07 | Reset-password, password-changed, credentials and contact email templates | R | Preserve links and message variables; test with captured mail, not live recipients; no dark-theme restyling required |

### Inactive and ambiguous artifacts

Do not count unused files as delivered features or silently activate them. Retain these files during the migration and record explicit disposition in the implementation inventory:

- `auth-payment-user-login` and its controller exist, but the dedicated web route is commented out. Preserve the current payment-user entry behavior; do not invent an additional production login.
- `superadmin/payment-user-management/*` has templates but no active CRUD route family was established. Payment-user behavior in the active user form remains in scope.
- Superadmin merchant/user trip-history methods and several `driver-trip-list` templates exist without matching active superadmin routes, and some returned view names are missing. Reuse only when an active caller or deployment evidence establishes the flow.
- Alternate tracking templates (`tracking-list`, `tracking-detail`, `tracking-live-location`) and duplicate Superadmin controller families are not replacements for tracing active route bindings.
- Stock Laravel `auth/*`, locale, coming-soon and unused horizontal/detached layout templates are not new user journeys. Preserve compatibility where reachable; do not add their links to navigation.
- `/reports-api`, `/Vehicle/no`, and the driver filter route reference methods not found in the inspected active controllers. Record callers and repair a required contract or explicitly retire an unused one; never leave a newly exposed control bound to a missing handler.

## 4. Submission and action register

Every action below gets a visible equivalent, applicable permission/state rules, a pending state, success/failure feedback and a regression case. Existing field names, CSRF, multipart/crop data, HTTP verbs, IDs and redirects are retained unless a separately tested correction is required.

| Action family | Contract / payload anchor | Interface requirements |
| --- | --- | --- |
| Staff login/recovery | A01-A05 GET/POST routes; `email`, `password`, `remember_me`; reset token/type/confirmation | One owning visibility handler; no password in display state, URLs or remembered credential cookies |
| User create/edit | O03/O04 and S03/S04 routes | Distinguish admin photo/crop and superadmin payment-user variants; preserve merchant relationships and captured credential-mail outcomes |
| User status/delete | `POST /user/status`, `/user/delete`, corresponding `/superadmin/user/*` | Status confirmation, record-specific deletion dialog, last-row paging adjustment and already-changed/deleted feedback |
| Driver create/edit | O08/O09 | Image preview/crop, retained existing image, dependent merchant/user, vehicle/device fields and correct numeric/string treatment for phone/IMEI |
| Driver PIN/status/delete | `POST /driver/change-login-pin`, `/driver/status`, `/driver/delete` | Separate PIN dialog and action permissions; preserve leading zeros where domain permits; confirmation and error states |
| Client edit/status/delete | `/client-edit/{id}`, GET `/client-status-change/{id}`, POST `/client/delete` with `client_id` | Superadmin-only controls; plan a POST status mutation with compatibility handling rather than triggering state changes through prefetchable links |
| Staff trip creation | `POST /trip/add` | New/existing-client switch clears incompatible validation; driver's availability and total cost are checked server-side |
| Trip cancel/delete | `POST /trip/cancel`, `/trip/delete` | Distinct consequences and confirmations, eligibility, stale state and forbidden response |
| Trip assignment | `POST /trip/change-driver`, `trip_id`, `driver_id` | Current assignment, valid alternatives, save/cancel and preserved trip identity |
| Single bank/POS confirmation | `POST /trip/{id}/bank-payment-received`, `/pos-payment-confirm` | Separate explicit payment-method dialogs showing trip and amount; disable repeat confirmation while pending |
| Bulk bank/POS confirmation | `POST /trip/bulk-bank-payment-received`, `/bulk-pos-payment-received` | Exact existing payload array from the stock form, eligible IDs, selected count/amount, final confirmation and atomic/retry-safe result |
| SMS payment alert/link | `POST /trip/send-sms-payment-alert-to-client` and current trip action links | Preserve recipient/trip relation and link behavior; never send automatically from opening a row, refreshing or retrying a read |
| Location create/edit/status/delete | Pickup-location route family | Preserve address/alias/coordinates and current request names; intentional destructive action and unavailable maps state |
| Expense edit/delete | `POST /expense-edit`, `/expense-delete`; `expense_id`, `item_name`, `item_cost`, `item_quantity` | Prefilled edit dialog, numeric validation and full filtered aggregate refresh; no phantom add button |
| Merchant create/edit/status/delete | `/superadmin/merchant/*` | Implement missing bound methods using merchant-specific behavior; do not merely alias user status/delete without checking target identity and API contract |
| Dependent selectors | `/superadmin/merchant/list`, `/superadmin/merchant/data`, `/superadmin/user-list-byMerchant`, `/Vehicle/no` | Preserve lookup inputs/outputs; validate access from admin and company workflows without broadening unrelated superadmin permissions |
| Tracking/history requests | `/tracking-list-detail[-filter]`, GET/POST `/map-tracking/{id}`, `/map-history` | Existing query names, cancellation of stale reads, last-known state, explicit stale timestamp and safe retry |
| Report generation/download | GET `/reports`, `/report-pdf-download` | Same `truck_id`, `daterange`, `trip_type`, `customer`, `payment_type`; totals and download represent the same filtered records |
| Password change | `POST /change-password`; `old_password`, `new_password`, current confirmation input | Server confirmation validation, shared authenticated route and role-correct shell/return |
| Policy save | POST each `/superadmin/pages/*`, `body` | Rich-text value sync, preserved formatting, save errors and public rendering checks |
| Customer mutations | C01-C18 and linked customer endpoint map | Distinct customer adapter and identity; draft-first flow, real verified auth and gateway return; fixture success never counts as integration |

During implementation, expand this register into a checked-in route/action manifest containing method, URI/name, bound handler, source view, field names, role, side effects, proposed component and test IDs. Route existence alone is insufficient: verify the handler and referenced view exist. Diff this manifest against registered routes and all form/AJAX callers so newly discovered reachable flows cannot be omitted.

## 5. Design and component arrangement

Use the existing dark theme's semantic tokens, compact typography, fixed sidebar/topbar, restrained blue primary actions, cyan informational states, green success, amber pending and red failure. Do not carry the donor demo's oversized glass cards or fake data into operational screens. Keep headings proportional to tool surfaces and preserve dense scanning.

| Component group | Reuse | Build or complete |
| --- | --- | --- |
| Application shell | Existing modern layouts, sidebar/navbar and role menu data | One navigation owner; collapse recovery control; accessible mobile drawer; consistent breadcrumbs and authorized search |
| Page structure | Existing headings, metrics and finance patterns | `page-heading`, `metric`, `detail-section`, `detail-row`, tabs and action toolbar; use unframed sections rather than nested cards |
| Forms | Existing Laravel validation, selectors and required vendor widgets | Shared field/error/hint/required treatment, phone/date/money/select inputs, image/crop adapter, dependent selector, dirty form and pending submission behavior |
| Dialogs | Bootstrap 4 modal where already required | Record-aware confirmation, action/form dialog, focus return, Escape/cancel, disabled pending submit and business error messages |
| Feedback | Existing flash messages and state templates | Consistent success/error summary, inline errors, empty/no-match, loading, retry, permission denied and unavailable provider states |
| Tables | `x-fleetng.data-table` and existing client modern response | Module-neutral actions, filter schema, selection eligibility, totals, typed cells, URL state and backward-compatible endpoint adapters |
| Maps/charts/editor | Existing providers and chart/editor libraries | Adapter lifecycle, container sizing, theme contrast, empty/error states and cleanup; no framework replacements solely for appearance |
| Customer workflow | Preview stepper, summary, detail rows and fixture transport | Production components based on accepted design; pending/failure screens, OTP challenge, photo upload and payment return equivalents |

Place reusable staff components under `resources/views/components/fleetng`; modern page composition stays under `resources/views/themes/fleetng-modern`. Keep form validation and business rules in controllers/services; components render supplied permissions and data. Split modern JavaScript into shell, table, form/dialog and module handlers through the existing asset build when extraction is warranted. Retain required legacy vendor widgets until each owning screen is replaced; never bind legacy and modern handlers to the same control.

Do not promote the standalone customer preview's entire application script to a production shared framework. Extract only reviewed visual patterns and use its mock data solely in fixture galleries/tests.

## 6. Table parity specification

The current client table is not a drop-in replacement for legacy tables. Legacy APIs commonly return HTML cell arrays with DataTables counts; the modern client endpoint returns structured records and `meta`. Changing markup without adapting that contract will break filters, paging or actions.

### Interface and transport

- Keep DataTables requests/responses unchanged for stock callers. Add the existing opt-in `format=modern` branch to required list endpoints only after the interface gate passes.
- Use the established modern request: `page`, `per_page` (10/25/50/100), `search`, allowlisted `sort`, `direction`; carry module-specific existing filters explicitly. Preserve stock default sorting unless a reviewed change is recorded.
- Modern response: `data` containing raw records with stable IDs and permitted action descriptors; `meta` containing `page`, `per_page`, filtered `total`, authorized `unfiltered_total`, `last_page`, `sort`, `direction`. Add named `aggregates` where a screen needs totals. Do not change the meaning of existing metadata.
- Validate the modern request before performing legacy pagination arithmetic. Bound page sizes and sort columns, add a stable ID tie-breaker and clamp pages after deletion.
- Build URLs with a URL/query API, preserving existing parameters. Namespace table state when a page contains more than one table; support browser back/forward. Escape ordinary cells; restrict rich renderers to known types and safe links.
- Customer list APIs currently return complete lists. Preserve that contract initially and apply the reviewed client-side table behavior; introduce server pagination only through a separately versioned/opt-in contract when measured volume requires it.

### Behavior by list family

| Lists | Required filters/columns/actions beyond a generic table |
| --- | --- |
| Users and merchants | Existing identity/contact/type/merchant/status filters; account actions and role-specific columns; independent admin/superadmin endpoints |
| Drivers | Merchant/user, vehicle, name/phone and status/deep links; device fields, trip links, PIN and status/delete actions |
| Clients | Authorized scope, name/phone/email, status and superadmin actions; retain meaningful ordering and any stock row numbering |
| Trips and related histories | `trip_type`, `search_driver`, `search_client`, `search_location`, `daterange`; pickup/drop, dates, driver/client links, base/material/road/commission totals and payment/trip status |
| Expenses | `phone`, `item_name`, `driver_id`, `daterange`; quantity/unit cost and response-level `total_expenses`, independent of current page |
| Tracking | `vehicle_status`, `search_driver`; vehicle/device/contact/location/distance/activity fields and map links |
| Reports | Existing report query fields, detail rows and complete filtered financial summaries; PDF equivalent must not silently export only the visible table page |
| Payments | Current transaction attributes, authorized scope, trip/reference links, numeric amounts and real status labels |
| Customer trips/drafts/payments | Preserve the preview's customer-oriented filters/details but reconcile payment eligibility with backend policy before integration |

Selection rules: store selected IDs independently of rendered checkboxes; page-level select-all only; show an indeterminate header state; retain selection across pagination/sorting and clear it on search/filter/account changes. Enable selection only on screens with a real bulk action. Staff bank/POS eligibility must preserve the stock unpaid-completed/payment-user rules. Show selected count and sum from selected eligible records, then reauthorize and recalculate amounts server-side at submit time. Never infer IDs or money from displayed HTML.

Complete typed renderers for text, identity/image, dates, currency, trip/payment status, links and action menus. Replace the current hardcoded `delete-client` callback with registered module actions. Keep existing loading/empty/error/retry behavior; distinguish 401/403/validation from temporary transport errors. Clear stale totals on failed reload, reject stale responses and avoid accidental repeated side effects.

Accessibility and responsive acceptance: sort state belongs on column headers; density buttons expose pressed state; checkbox labels identify records; dialogs trap and restore focus. Tables may scroll horizontally inside their container on narrow screens, but forms, toolbars, text and the page must not overflow. Do not add fake CSV/Excel/KML exports just because the donor contains a button.

## 7. Known choke points and required handling

| Finding | Risk | Planned handling / release condition |
| --- | --- | --- |
| Global modern view fallback | Mixed old/new shells can hide missing interfaces | Fixture gallery and route/view manifest must show complete coverage; dedicated preview environment; no incomplete global production switch |
| Overlapping legacy/modern JS | Password toggle can fire twice; navigation handlers compete | Assign one owner per widget; verify password visibility and menu behavior in real browsers |
| Collapsed sidebar hides expand control | Persisted inaccessible desktop navigation | Keep an always-reachable expand control; test reload with collapsed preference |
| User/merchant/driver/report/vehicle route-view inconsistencies | A styled button may still reach a 500 or missing method | Classify each manifest defect; repair required handlers/views with role tests before attaching new controls |
| Wrong superadmin user-filter target | Incorrect records or permissions | Bind the superadmin filter to its own route and assert returned scope |
| Menu, dashboard and record-scope disagreement | Dead links or cross-account exposure | Test navigation and direct URLs separately; resolve admin client scope and hide company-user admin links |
| Staff controllers call the app's own API | Single-thread local server can stall on self-request; incorrect APP_URL concatenation | Use a concurrent server for integration tests, a correct local APP_URL and required cURL extensions; normalize inconsistent API URL joining with narrow tests |
| Plaintext remembered credential cookie in recovered auth | Sensitive credentials retained by old behavior | Preserve remember-session intent using a proper session/remember-token mechanism; never reproduce password-cookie storage in the modern frontend |
| Customer login has no recovered OTP proof | Public OTP-looking UI alone is not authentication | Recover original identity implementation or specify server-verified challenge flow; no live customer activation before this dependency is resolved |
| Customer detail/update/payment lookups lack consistent ownership scope | Another customer's record may be addressed by ID | Apply customer identity and ownership at the server and test foreign IDs across every read/mutation |
| Payment/confirmation retries and provider failures | Duplicate trips, payments or notifications; partial writes | Transactions, verified amounts/status, idempotent handling and post-write refetch; isolated gateway/notification tests |
| Different direct-trip and draft pickup contracts | Pickup IDs can be written as text or new records duplicated | Use the draft-first customer flow; retain direct creation only as its existing separate contract |
| Maps, image storage, SMS, email and payments depend on external services | False success or dead UI with local credentials | Inject fakes for equivalence/testing; verify real configured staging providers before release; never display simulated live data |
| Reconstructed database schema | SQLite success does not prove production compatibility | Compare an authoritative schema-only export and run workflows on a production-compatible isolated database |
| Unprotected-looking invoice and broad auth route groups | Navigation restrictions may not secure resources | Verify intended access and record ownership on actual routes before enabling equivalent links |

These are bounded integration prerequisites, not authorization to perform an unrelated framework rewrite. Unresolved identity-provider, merchant-access or payment-policy decisions must be surfaced explicitly; interface completion must not be misrepresented as production readiness.

## 8. Execution sequence and gates

### Phase 0: baseline and restore checkpoint

1. Record the current commit and worktree status; preserve untracked customer preview/docs/tests and any user changes. Create a fresh source snapshot before implementation, since the existing Git bundle predates the preview.
2. Back up environment configuration privately, database and uploaded files before any integration/data changes. Use a consistent database dump or SQLite backup, not a live file copy. Keep backups outside public directories and source control.
3. Restore source and data into an isolated test location and verify the restore. Establish stock and modern preview URLs with separated sessions/configuration; no production services in fixture mode.
4. Produce the route/action manifest described above, including inactive and broken entries. Capture stock screens and valid/invalid outcomes with synthetic fixtures for each established role.

Gate 0: recoverable baseline, test environment, documented defects and role/data fixtures exist. A homepage HTTP 200 alone does not satisfy this gate.

### Phase 1: shared equivalent components

1. Finish shell/navigation and semantic design tokens, then feedback, form fields, image/crop, dialogs, typed table cells and table state.
2. Build the local/testing-only fixture gallery. Each page must render its role variants and success, validation, empty, loading, forbidden and provider-error examples as applicable.
3. Correct purely frontend defects in the isolated modern layer and add focused component/browser coverage. Keep new submission outcomes simulated at this stage.

Gate 1: shared controls work across target viewports, field values persist correctly, no duplicate event ownership, and no preview control makes an external mutation.

### Phase 2: create every equivalent before integration

Build in this order to reuse components, but do not integrate early batches while later required interfaces are absent:

1. A01-A09, O01, S01: access, shell, shared states and dashboards.
2. O02-O13, S02-S09: user, driver, client and merchant directories/forms/details.
3. O14-O17, O22-O23, O28-O29: trips, financial dialogs, expenses, payments and retained document outputs.
4. O18-O21, O24-O27: location forms, tracking, map history and reports.
5. O30, S10-S13 and X01-X07: settings, policy editing and shared/public regression surfaces.
6. C01-C18: finish the customer demo's missing interface states, photo upload, OTP challenge, payment return and session/device flows using fixtures only.

For each ID attach a preview URL, screenshots, action references, fields/filters/columns checklist, role variants and test evidence. A confirmation modal counts as its own checked interaction even if it shares a page. A PDF marked retained requires output verification rather than a new dark design.

Gate 2, the user's principal requirement: every active interface and submission has a reviewed equivalent or an explicitly documented retained output. No missing navigation destination, blank action, fixture-only success presented as live success, or silently omitted field. Inactive artifacts have an explicit disposition. Visual/workflow approval is recorded before new backend integration starts.

### Phase 3: integrate in an isolated environment

1. Resolve required baseline handler/view defects and role contracts. Add modern opt-in serializers without altering stock response behavior.
2. Integrate reads first: identity/navigation, dashboards, directories/details, then reports/tracking. Compare authorized record IDs, statuses, ordering, totals and filters between stock and modern.
3. Integrate writes: account forms and uploads, locations, assignments, trips, expenses, policy/settings, then payments/notifications last. Run the paired stock/new contract tests after each group.
4. Integrate customer identity only after server verification and ownership gates pass; connect customer reads, draft writes, confirmation and profile, then payment gateway/return and revocation.
5. Preserve the public customer destination until the real replacement deployment and return routes are verified. Do not point production links at `/customer-preview/`.

Gate 3: every required contract has authenticated, role-scoped success/failure tests. Stock API responses remain compatible; no production data was used for destructive tests.

### Phase 4: release rehearsal and rollout

1. Build assets using the existing lockfiles/build process; verify manifest resolution, CSS/fonts/icons, no stale mixed bundles and no accidental fixture assets in the live entry.
2. Rehearse the theme/config change and rollback on a production-like instance, including compiled views/config caches and worker reload where required. Confirm public pages, emails and PDFs remain stable.
3. Review all-role acceptance evidence, customer prerequisites and production-compatible data tests. Release only after these pass; deploy customer replacement independently of staff theme selection.
4. Monitor login failures, authorization errors, failed submissions, duplicate payment/trip events, provider failures and missing assets during rollout. Any unexplained financial mismatch, foreign-record access or broken critical journey stops rollout and triggers rollback.
5. Roll back code/assets/theme and customer frontend destination to verified prior versions. Do not restore an old database over legitimate new bookings/payments as a frontend rollback. Retain backward-compatible data changes and reconcile affected transactions separately.

Gate 4: verified rollout, rollback rehearsal and acceptance sign-off; the old source remains recoverable.

## 9. Test and acceptance plan

| Suite | Required scenarios |
| --- | --- |
| Manifest coverage | Every active handler/view resolves; every form/AJAX/action has an interface/test ID; modern-required page cannot pass solely through legacy fallback |
| Auth/session | Valid/invalid/inactive/wrong-role login, remember behavior, password visibility, reset invalid token, confirmation mismatch, logout, expired CSRF/session and role-correct returns |
| Authorization | Each established staff role/payment-user variant, anonymous and customer identities; foreign-company/customer IDs, direct URLs, hidden actions and lookup access |
| Lists | Empty/one/many records, combined filters, stable sort ties, paging boundaries, last-row deletion, back navigation, multiple tables, null relations and long content |
| Financial tables | Full-filter aggregates versus visible-page totals; material/road/base cost; page selection, eligibility, filter changes, duplicate submit and server recalculation |
| Forms/uploads | Required/duplicate/invalid fields, leading-zero identifiers, merchant-user dependencies, new/existing-client switch, image crop/retain/fail, dirty state and failed-submit input retention |
| Trips/drafts | Save/edit/reopen/confirm, optional driver, invalid location/date, record changed concurrently, duplicate confirmation and notification failure |
| Payments | Bank and POS separately, bulk eligible/ineligible mix, already paid, zero billable selection, gateway failure/cancel/pending/success, repeated callback and ownership enforcement |
| Maps/reports | No location/history, stale provider timestamp, invalid coordinates/query, provider timeout; report/PDF filter equivalence, no results, totals and 550-row boundary |
| Customer | Every preview flow plus real identity/session, profile multipart, API HTTP 400 error envelope, missing/forbidden record, sparse merchant arrays and payment return routing |
| Visual/accessibility | Desktop 1440/1920, tablet 768 and mobile 320/390; keyboard navigation, focus/labels, dialog return, collapsed reload, long names and horizontal table containment |
| Regression/release | Stock and modern response contracts, public pages, credentials/reset/contact mail capture, PDF rendering, driver API consumers, production-compatible DB, cache rebuild and restore/rollback rehearsal |

No promise that nothing can ever break substitutes for these checks. Acceptance requires matching observable behavior on the identified interfaces, no unresolved critical workflow failures, and explicit evidence for external integrations.

## 10. Deliverables and immediate next work

- This register is the scope baseline. Maintain status by ID as equivalents are created; do not mark P or D entries integrated without backend evidence.
- Produce the machine-checkable route/action manifest and stock fixture evidence first, then the component gallery and complete dark interface set.
- Keep the customer endpoint map as the detailed customer contract appendix; update it when identity/payment/source discoveries change the integration design.
- Deliver paired contract/browser tests, screenshots, production-compatible validation evidence, a restored backup and a release/rollback runbook with the implementation.
- Immediate implementation task: Phase 0 baseline/manifest followed by Phase 1 shared components. No production theme or customer-link switch is part of drafting this plan.
