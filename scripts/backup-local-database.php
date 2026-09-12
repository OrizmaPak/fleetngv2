<?php

if (PHP_SAPI !== 'cli' || empty($argv[1])) exit(1);
$source = dirname(__DIR__) . '/database/database.sqlite';
if (!is_file($source)) throw new RuntimeException('Local SQLite database not found.');
$db = new SQLite3($source, SQLITE3_OPEN_READONLY);
$target = new SQLite3($argv[1]);
if (!$db->backup($target)) throw new RuntimeException('SQLite backup failed.');
$integrity = $target->querySingle('PRAGMA integrity_check');
$target->close();
$db->close();
if ($integrity !== 'ok') throw new RuntimeException('Backup integrity check failed.');
echo "Consistent SQLite backup verified.\n";
