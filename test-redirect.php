<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test user redirect logic
echo "Testing redirect logic...\n\n";

// Get a user by phone
$phone = $argv[1] ?? '01711111111';
echo "Looking for user with phone: {$phone}\n";

$user = App\Models\User::where('phone', $phone)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->name}\n";
echo "User ID: {$user->id}\n";
echo "Business ID: {$user->business_id}\n\n";

// Check roles
echo "Checking roles...\n";
$roles = $user->roles()->pluck('name');
echo "Roles: " . $roles->implode(', ') . "\n\n";

// Check role methods
echo "Role check methods:\n";
echo "  isSuperAdmin(): " . ($user->isSuperAdmin() ? 'YES' : 'NO') . "\n";
echo "  isOwner(): " . ($user->isOwner() ? 'YES' : 'NO') . "\n";
echo "  isManager(): " . ($user->isManager() ? 'YES' : 'NO') . "\n";
echo "  isSalesman(): " . ($user->isSalesman() ? 'YES' : 'NO') . "\n\n";

// Test getDashboardRoute
try {
    $route = $user->getDashboardRoute();
    echo "✅ Dashboard route: {$route}\n";
    
    // Check if route exists
    $routeExists = \Illuminate\Support\Facades\Route::has($route);
    echo "Route exists: " . ($routeExists ? 'YES' : 'NO') . "\n";
    
    if ($routeExists) {
        $routeInfo = \Illuminate\Support\Facades\Route::getRoutes()->getByName($route);
        echo "Route URI: " . $routeInfo->uri() . "\n";
        echo "Route Action: " . $routeInfo->getActionName() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error getting dashboard route: " . $e->getMessage() . "\n";
}

// Check business relationship
if ($user->business) {
    echo "\n✅ Business relationship loaded\n";
    echo "Business name: {$user->business->name}\n";
} else {
    echo "\n⚠️ No business associated with this user\n";
}
