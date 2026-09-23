# FleetNG frontend themes

The recovered Laravel application supports two Blade frontends:

- `legacy`: the original recovered production interface.
- `fleetng-modern`: the isolated FleetNG redesign with automatic fallback to a legacy view when no modern override exists.

The active theme is selected in `.env`:

```dotenv
UI_THEME=legacy
```

Use `legacy` for the initial deployment. To enable the redesign after acceptance testing:

```dotenv
UI_THEME=fleetng-modern
```

After changing the value, rebuild only this application's caches:

```bash
php artisan config:clear
php artisan view:clear
php artisan config:cache
php artisan view:cache
```

Rollback is the same process with `UI_THEME=legacy`. The theme switch changes view lookup and isolated assets only; it does not change routes, authentication, database schema, shared web-server configuration, or any other application on the host.

Modern Blade overrides live in `resources/views/themes/fleetng-modern`. Modern browser assets live in `public/themes/fleetng-modern`. Do not place modern files over the legacy view or asset trees.

Before production deployment, back up the `fleetngv2` application directory and its dedicated database. Upload only files belonging to `fleetngv2` and run cache commands from that application's root directory.
