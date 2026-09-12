<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class InterfaceAudit extends Command
{
    protected $signature = 'fleetng:interface-audit {--write : Write the generated manifest to storage/app/interface-audit.json}';
    protected $description = 'Inventory route handlers, view references and submission fields without executing business actions';

    public function handle()
    {
        $routes = [];
        $missing = [];
        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();
            $valid = true;
            if (strpos($action, '@') !== false) {
                [$class, $method] = explode('@', $action, 2);
                $valid = class_exists($class) && method_exists($class, $method);
            }
            $entry = ['methods' => $route->methods(), 'uri' => $route->uri(), 'name' => $route->getName(), 'handler' => $action, 'middleware' => $route->middleware(), 'handler_exists' => $valid];
            $routes[] = $entry;
            if (!$valid) $missing[] = $entry;
        }
        $views = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (substr($file->getFilename(), -10) !== '.blade.php') continue;
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen(resource_path('views')) + 1));
            if (strpos($relative, 'themes/') === 0) continue;
            $source = file_get_contents($file->getPathname());
            preg_match_all('/\bname\s*=\s*[\'"]([^\'"{}]+)[\'"]/', $source, $fields);
            preg_match_all('/\broute\(\s*[\'"]([^\'"]+)[\'"]/', $source, $links);
            $views[] = ['view' => $relative, 'modern_override' => is_file(resource_path('views/themes/fleetng-modern/' . $relative)), 'fields' => array_values(array_unique($fields[1])), 'named_routes' => array_values(array_unique($links[1])), 'form_count' => preg_match_all('/<form\b/i', $source), 'table_count' => preg_match_all('/<table\b/i', $source)];
        }
        $manifest = ['generated_at' => now()->toIso8601String(), 'routes' => $routes, 'missing_handlers' => $missing, 'views' => $views];
        $json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        if ($this->option('write')) {
            file_put_contents(storage_path('app/interface-audit.json'), $json . PHP_EOL);
            $this->info('Manifest written to storage/app/interface-audit.json');
            $this->line(count($routes) . ' routes; ' . count($views) . ' views; ' . count($missing) . ' missing handlers.');
        } else {
            $this->line($json);
        }
        return count($missing) ? 1 : 0;
    }
}
