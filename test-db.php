<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Database Config:\n";
echo "Connection: " . config('database.default') . "\n";
echo "Host: " . config('database.connections.pgsql.host') . "\n";
echo "Port: " . config('database.connections.pgsql.port') . "\n";
echo "Database: " . config('database.connections.pgsql.database') . "\n";
echo "Username: " . config('database.connections.pgsql.username') . "\n";
echo "Password: " . config('database.connections.pgsql.password') . "\n";
