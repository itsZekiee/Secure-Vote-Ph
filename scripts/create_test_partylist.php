<?php
// scripts/create_test_partylist.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Partylist;

try {
    $pl = Partylist::create([
        'name' => 'Script Test Party',
        'acronym' => 'STP',
        'description' => 'Created by script',
        'platform' => 'Platform text',
        'color' => '#3b82f6',
        'organization_id' => 1,
        'election_id' => null,
        'status' => 'active',
        'created_by' => 1,
    ]);

    echo "Created partylist ID: " . $pl->id . PHP_EOL;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
