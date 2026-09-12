param([int]$Port = 8022)
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$database = Join-Path $root 'storage\app\review\review.sqlite'
if (-not (Test-Path -LiteralPath $database)) { throw 'Run php artisan fleetng:prepare-review first.' }
if (Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue) { throw "Port $Port is already in use. Choose another port." }
$php = (Get-Command php -ErrorAction Stop).Source
$extensions = Join-Path (Split-Path -Parent $php) 'ext'
$env:APP_ENV = 'local'
$env:APP_DEBUG = 'false'
$env:APP_URL = "http://127.0.0.1:$Port"
$env:DB_CONNECTION = 'sqlite'
$env:DB_DATABASE = $database
$env:UI_THEME = 'fleetng-modern'
$env:MAIL_MAILER = 'log'
$env:CUSTOMER_PAYMENTS_ENABLED = 'false'
$env:TRACKING_ENABLED = 'false'
$env:CUSTOMER_PROFILE_DISK = 'local'
$env:STAFF_IMAGE_DISK = 'local'
$env:SESSION_COOKIE = "fleetng_review_$Port"
$arguments = @('-d', "extension_dir=`"$extensions`"", '-d','extension=openssl','-d','extension=pdo_sqlite','-d','extension=sqlite3','-d','extension=mbstring','-d','extension=fileinfo','-S',"127.0.0.1:$Port",'-t','public','server.php')
$process = Start-Process -FilePath $php -ArgumentList $arguments -WorkingDirectory $root -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $root "storage\logs\review-$Port.log") -RedirectStandardError (Join-Path $root "storage\logs\review-$Port-error.log")
Write-Output "Local review running at http://127.0.0.1:$Port (PID $($process.Id))."
