<?php

// Mechanical migration: preserve working Blade forms/scripts and add the shared modern shell.
if (PHP_SAPI !== 'cli') exit(1);
$root = dirname(__DIR__) . '/resources/views/';
$count = 0;
foreach (['admin', 'superadmin'] as $directory) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . $directory, FilesystemIterator::SKIP_DOTS)) as $file) {
        if (substr($file->getFilename(), -10) !== '.blade.php') continue;
        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root)));
        if (preg_match('~payment-user-management/|report-pdf|trip-payment-invoice~', $relative)) continue;
        $target = $root . 'themes/fleetng-modern/' . $relative;
        if (file_exists($target)) continue;
        $source = file_get_contents($file->getPathname());
        if (strpos($source, "@section('content')") === false) continue;
        $source = str_replace("@section('content')", "@section('content')\n<x-fleetng.page-heading>@yield('title')</x-fleetng.page-heading>\n<x-fleetng.feedback />", $source);
        if (!is_dir(dirname($target))) mkdir(dirname($target), 0775, true);
        file_put_contents($target, $source);
        $count++;
    }
}
echo "Created $count modern view candidates. Existing overrides were preserved.\n";
