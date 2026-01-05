<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking database tables...\n\n";

$tables = [
    'users',
    'businesses',
    'products',
    'stock_entries',
    'sales',
    'roles',
    'permissions',
    'model_has_roles',
];

foreach ($tables as $table) {
    try {
        $count = DB::table($table)->count();
        echo "✓ {$table}: {$count} records\n";
    } catch (\Exception $e) {
        echo "✗ {$table}: MISSING or ERROR - {$e->getMessage()}\n";
    }
}

echo "\nChecking owner user and business setup...\n";
$owner = DB::table('users')->where('phone', '01711111111')->first();
if ($owner) {
    echo "✓ Owner user exists (ID: {$owner->id}, Business ID: {$owner->business_id})\n";
    
    if ($owner->business_id) {
        $business = DB::table('businesses')->find($owner->business_id);
        if ($business) {
            echo "✓ Business exists (ID: {$business->id}, Name: {$business->name})\n";
        } else {
            echo "✗ Business NOT FOUND\n";
        }
    } else {
        echo "⚠ Owner has no business_id assigned\n";
    }
    
    $role = DB::table('model_has_roles')->where('model_id', $owner->id)->first();
    if ($role) {
        $roleName = DB::table('roles')->find($role->role_id);
        echo "✓ Owner has role: {$roleName->name}\n";
    } else {
        echo "✗ Owner has NO ROLE assigned\n";
    }
} else {
    echo "✗ Owner user NOT FOUND\n";
}
