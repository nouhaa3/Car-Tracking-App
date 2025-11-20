<?php
// Database Schema Inspector - Shows all tables and their columns
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=================================================\n";
    echo "     CAR TRACKING APP - DATABASE SCHEMA\n";
    echo "=================================================\n\n";
    
    // Get database name
    $dbName = DB::connection()->getDatabaseName();
    echo "Database: {$dbName}\n\n";
    
    // Get all tables
    $tables = DB::select('SHOW TABLES');
    $tableKey = "Tables_in_{$dbName}";
    
    echo "Total Tables: " . count($tables) . "\n\n";
    echo "=================================================\n\n";
    
    foreach ($tables as $table) {
        $tableName = $table->$tableKey;
        
        echo "📋 TABLE: {$tableName}\n";
        echo str_repeat("-", 50) . "\n";
        
        // Get columns for this table
        $columns = DB::select("DESCRIBE {$tableName}");
        
        echo sprintf("%-25s %-20s %-10s %-10s\n", "Column", "Type", "Null", "Key");
        echo str_repeat("-", 50) . "\n";
        
        foreach ($columns as $column) {
            echo sprintf(
                "%-25s %-20s %-10s %-10s\n",
                $column->Field,
                $column->Type,
                $column->Null,
                $column->Key
            );
        }
        
        // Count records
        try {
            $count = DB::table($tableName)->count();
            echo "\nRecords: {$count}\n";
        } catch (\Exception $e) {
            echo "\nRecords: N/A\n";
        }
        
        echo "\n" . str_repeat("=", 50) . "\n\n";
    }
    
    echo "\n✅ Schema inspection complete!\n\n";
    
    // Show relationships summary
    echo "=================================================\n";
    echo "            FOREIGN KEY RELATIONSHIPS\n";
    echo "=================================================\n\n";
    
    $foreignKeys = DB::select("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME,
            CONSTRAINT_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            TABLE_SCHEMA = '{$dbName}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ORDER BY TABLE_NAME
    ");
    
    foreach ($foreignKeys as $fk) {
        echo "{$fk->TABLE_NAME}.{$fk->COLUMN_NAME}\n";
        echo "  └─> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        echo "  ({$fk->CONSTRAINT_NAME})\n\n";
    }
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
