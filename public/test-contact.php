<?php
// Simple test script to check contact message insertion
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test database connection
    echo "Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "✓ Database connected\n\n";
    
    // Check if table exists
    echo "Checking if contact_messages table exists...\n";
    $tableExists = Schema::hasTable('contact_messages');
    echo $tableExists ? "✓ Table exists\n\n" : "✗ Table does NOT exist\n\n";
    
    if ($tableExists) {
        // Describe table structure
        echo "Table structure:\n";
        $columns = DB::select('DESCRIBE contact_messages');
        foreach ($columns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
        echo "\n";
        
        // Try to insert a test record
        echo "Attempting to insert test record...\n";
        $id = DB::table('contact_messages')->insertGetId([
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'message' => 'This is a test message',
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "✓ Record inserted successfully with ID: {$id}\n\n";
        
        // Verify the record
        echo "Verifying inserted record...\n";
        $record = DB::table('contact_messages')->where('id', $id)->first();
        echo "✓ Record found:\n";
        echo "  Name: {$record->prenom} {$record->nom}\n";
        echo "  Email: {$record->email}\n";
        echo "  Message: {$record->message}\n\n";
        
        // Clean up test record
        DB::table('contact_messages')->where('id', $id)->delete();
        echo "✓ Test record cleaned up\n";
    }
    
    echo "\n=== All tests passed! ===\n";
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
