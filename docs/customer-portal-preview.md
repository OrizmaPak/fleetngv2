# Customer portal reconstruction and integration map

## What this preview establishes

Design target: the dark `fleetng-modern` interface at `/user`, adapted into a customer workspace. The separate FLEETNG React demo was inspected as a secondary reference. It has a mock customer login, but its routes otherwise describe staff operations; it is not a complete customer portal implementation.

The original deployed customer site at `https://client.fleetng.com/login` exposes a trip-request entry flow with existing/new customer tabs, phone verification, pickup/drop-off details, and merchant/driver selection. We did not submit an OTP request, create a production booking, or inspect authenticated customer data. Its editable source was not found in the original backend ZIP.

This preview reconstructs customer screens from `Modules/Customers/Routes/api.php`, its controllers, and entity relationships. It is a reviewable proposal, not a claim to reproduce the unseen authenticated frontend exactly.

- Open `/customer-preview/` on the running Laravel server, or open `public/customer-preview/index.html` directly. No asset build or separate server is required.
- All records are fictional and all writes stay in browser session storage. A prominent demo label remains visible. The CSP blocks network connections and native form submissions. There are no real tokens, OTP requests, messages, gateway requests, or database writes.
- Implemented: overview; searchable/filterable/sortable/paginated trips; draft directory; three-step booking; draft save/edit/confirmation; trip details; driver reassignment for new trips; payment selection and explicitly simulated checkout; transaction history; profile edit; simulated existing/new account entry; logout; reset.
- The current staff login, public customer-login destination, controllers, and database remain untouched. Existing tracked source was backed up to `../backups/before-customer-preview-20260909.bundle` relative to the repository. This Git bundle does not contain ignored environment files, uploads, or database data. No database backup is claimed.

## Screen and endpoint alignment

Customer API paths below are relative to `/api/customer`. Preview navigation uses hash routes so it can run without server route changes.

| Proposed screen or action | Existing endpoint | Current preview and integration requirement |
| --- | --- | --- |
| Existing-customer entry, `#/login` | `POST /verify-phone`, `POST /login` | Simulated sample-account entry. The backend checks a phone number and issues a Sanctum token; it does not accept verified OTP proof. Do not treat the preview as production authentication. |
| New-customer entry | `POST /verify-details`, `POST /register` | Sample-account form for first/last name, email, country code, phone. A new demo account starts with empty trips/payments. Connect duplicate-field validation and verified identity before activation. |
| Overview, `#/overview` | `GET /stats`, `GET /trips`, `GET /trip-payments` | Derives account totals, active/completed trips, recent trips, recent payments and draft count from the same fixture records. The backend `total_payment` actually sums base trip costs, so it is not labelled paid spend. |
| My trips, `#/trips` | `GET /trips` | Search, trip-status/payment filters, date/amount sort, row density, pagination, row selection and details. Production endpoint returns all customer trips; pagination here is client-side. |
| Booking step 1, `#/book` | `GET /pickup-locations` | Existing pickup ID, drop-off text, pickup datetime and whole-naira proposed cost. The draft API expects a pickup ID, unlike direct trip creation. |
| Booking step 2 | `GET /merchants` | Merchant and driver selection; supports saving without assignment. Normalize PHP's possibly sparse merchant array to a list in a future real adapter. |
| Save booking | `POST /draft/trips/create` | Uses the draft-first workflow. Posts `pickup_location`, `pickup_datetime`, `drop_off_location`, `cost`, and optional `driver`. All demo fields and selected IDs survive reopening a draft. |
| Draft directory, `#/drafts` | `GET /draft/trips` | Lists saved bookings; labels these Draft even though list API may serialize a status name from an inconsistent numeric status map. |
| Open/edit draft, `#/draft/:id` | `GET /draft/trips/{id}`, `POST /draft/trips/{id}` | Restore all form fields and update the saved draft. Live adapter must retain and resend `drop_location_id` to update the existing drop-off record. |
| Draft driver change | `POST /draft/trips/{id}/driver` | Preview saves assignment through the general draft-update form; this dedicated endpoint is another backend-supported path, not a required extra screen. |
| Confirm booking | `POST /draft/trips/{id}/confirm` | Creates a New trip and removes the draft once confirmed. The real endpoint sends driver notifications; the preview does not. |
| Direct creation alternative | `POST /trips/create` | Available in backend, not used by this draft-first UX. Accepts pickup text and creates a new pickup record; do not substitute the draft's pickup ID into this contract. |
| Trip details, `#/trip/:id` | `GET /trips/{id}` | Route, scheduled pickup, driver/merchant/vehicle, status, base/material/road charges, payment status/reference. No invented customer tracking feed or trip-cancellation action. |
| Change trip driver | `PATCH /trips/{id}/driver` | Preview exposes reassignment on New trips. Backend currently does not enforce this state restriction; agree/enforce the business rule server-side before live use. |
| Pay one/multiple trips | `POST /trips/payment-link` | Explicitly simulated checkout from selected trip IDs. The real endpoint takes `trip_ids` and returns a gateway response, not the standard customer envelope. It has not been called. |
| Payment return | Web `GET /customer/trips/payments` | Backend verifies Flutterwave and redirects to the configured frontend's `/trip/payments` with status/reference parameters. A live frontend must support that exact return path and refetch server truth. Preview hash URLs are not a payment-return implementation. |
| Payment history, `#/payments` | `GET /trip-payments` | Amount, trip, transaction ID, reference, date, method and status. Demo payments alter only fictional records. |
| Account, `#/profile` | `GET /profile`, `POST /profile` | First/last name, email, country code and phone. Real endpoint also supports multipart `profile_image` and S3 storage; production upload integration remains outstanding. |
| Device notifications | `POST /save-device-token` | Backend capability only; not exposed as a pretend notification preference. Requires the original push-provider setup and consent flow. |
| Logout | No customer logout route found | Preview clears its simulated session. Production needs server token revocation as well as clearing frontend identity. |

## Component and data boundaries

- `index.html`: isolated application entry, local logo/icons, strict no-network CSP.
- `styles.css`: dark semantic tokens, fixed sidebar/topbar, compact page headings, flat metric bands, semantic trip/payment badges, responsive forms and table scrolling. Keeps FleetNG's existing Feather icons, blue action color, cyan/green/amber states and restrained 6-8px radii.
- `app.js`: application shell; overview; reusable table rendering; booking stepper; review summary; detail rows; forms; dialogs; status feedback. Tables keep row selection by record ID across pagination/sorting, clear it when filters change, and provide page-only select-all with an indeterminate state. No unsupported export buttons or sample map pretending to show a real vehicle.
- `data.js`: isolated mock repository. `request(method, path, payload)` uses customer-relative endpoint paths and the normal `{status, message, errors, data}` envelope. `simulatePayment` is deliberately separate from that transport: a local success must never be mistaken for a gateway payment response. No production transport or live-mode toggle is included.
- Preserve API raw fields in an eventual adapter, including IDs, null driver/payment relations and numeric money values. Display total cost as base cost + material cost + road money. Persist only display-safe UI preferences when live; do not copy demo account persistence into production token handling.

## Concrete blockers before live integration

1. Recover the customer source/identity integration if available. Publicly visible OTP UI does not prove server enforcement: the recovered login currently accepts an existing phone number alone, deletes prior tokens and returns a new token. Establish server-verified identity before enabling real sign-in; do not fabricate an OTP route or validation result.
2. Scope all record reads/writes to the authenticated customer. Trip detail, driver changes, draft detail/update/confirm and payment-link lookups currently find records by ID without consistently applying customer ownership. Ensure customer-only identity on these routes. UI hiding is insufficient.
3. Make draft confirmation and payment recording transactional and retry-safe. Confirmation creates a trip, sends a notification, then deletes the draft; callback repeats can create additional payment rows. Test retries, notification failures, duplicate submissions, paid trips and empty billable selections before connecting confirmation/payment buttons.
4. Reconcile payment eligibility. This preview excludes Canceled/Declined trips and already-paid trips. The recovered customer gateway endpoint merely checks `payment_id`; staff payment selection has different status restrictions. Resolve and enforce a single customer policy before rollout. Server amounts and verification must remain authoritative.
5. Restore real URL/environment wiring, payment return routes, customer token lifecycle, cross-origin policy where relevant, and provider integrations. The local `CUSTOMER_PORTAL_FRONTEND_URL` points at port 3000, while the archived deployment used `client.fleetng.com`. Do not change that setting merely to expose this demo.
6. Handle real transport failures explicitly: expired identity/401, forbidden/403, missing records/404, validation envelopes with HTTP 400 (not only 422), provider/server failures and offline state. Real requests need pending states, field-error mapping and draft retention. The sample adapter is immediate and cannot establish network reliability.
7. Complete remaining integration surfaces: OTP, profile image upload, device token registration, real checkout return and token revocation. An authenticated customer tracking endpoint, customer invoice download contract and customer cancellation endpoint were not established; omit those actions until supported.

## Verification and next step

`tests/customer-preview.test.cjs` uses Playwright to exercise desktop/mobile rendering, logo loading, search/filter/pagination, selected-payment simulation, draft save/reopen/confirm, driver change, reload persistence, profile save, account entry/exit and sample reset. It also asserts no business API requests or uncaught browser errors. Screenshots are written to the OS temporary directory. Run with the workspace Playwright dependency on `NODE_PATH`; optionally set `CUSTOMER_PREVIEW_URL`.

Review this preview's customer workflow and presentation first. Once accepted, convert its shared visual patterns into the existing Blade theme, recover or intentionally replace the separate customer frontend, and wire one tested customer contract at a time in an isolated environment. Keep production activation separate from visual approval and preserve the staff and customer rollback paths.
