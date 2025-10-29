<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use PDO;
use PDOException;

class TestDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test {--connection=mysql : Connection to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test database connection and show detailed information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = $this->option('connection');
        
        $this->info("🔍 Testing database connection: {$connection}");
        $this->newLine();

        // Test 1: Basic connection test
        $this->testBasicConnection($connection);
        
        // Test 2: Detailed connection info
        $this->testDetailedConnection($connection);
        
        // Test 3: Test specific database operations
        $this->testDatabaseOperations($connection);
        
        // Test 4: Show configuration
        $this->showConfiguration($connection);
    }

    private function testBasicConnection($connection)
    {
        $this->info("📡 Test 1: Basic Connection Test");
        $this->line("─" . str_repeat("─", 50));
        
        try {
            DB::connection($connection)->getPdo();
            $this->info("✅ Connection successful!");
            $this->line("   Connection: {$connection}");
            $this->line("   Status: Connected");
        } catch (\Exception $e) {
            $this->error("❌ Connection failed!");
            $this->error("   Error: " . $e->getMessage());
            $this->error("   Code: " . $e->getCode());
        }
        
        $this->newLine();
    }

    private function testDetailedConnection($connection)
    {
        $this->info("🔧 Test 2: Detailed Connection Information");
        $this->line("─" . str_repeat("─", 50));
        
        try {
            $pdo = DB::connection($connection)->getPdo();
            
            // Get database info
            $databaseName = DB::connection($connection)->getDatabaseName();
            $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            $serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            $clientVersion = $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
            
            $this->info("✅ Detailed connection successful!");
            $this->line("   Database: {$databaseName}");
            $this->line("   Driver: {$driverName}");
            $this->line("   Server Version: {$serverVersion}");
            $this->line("   Client Version: {$clientVersion}");
            
        } catch (\Exception $e) {
            $this->error("❌ Detailed connection failed!");
            $this->error("   Error: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function testDatabaseOperations($connection)
    {
        $this->info("⚙️ Test 3: Database Operations Test");
        $this->line("─" . str_repeat("─", 50));
        
        try {
            // Test simple query
            $result = DB::connection($connection)->select('SELECT 1 as test_value');
            $this->info("✅ Simple query successful!");
            $this->line("   Query result: " . $result[0]->test_value);
            
            // Test database name query
            $dbName = DB::connection($connection)->select('SELECT DATABASE() as db_name');
            $this->info("✅ Database name query successful!");
            $this->line("   Current database: " . $dbName[0]->db_name);
            
            // Test tables existence
            $tables = DB::connection($connection)->select('SHOW TABLES');
            $this->info("✅ Tables query successful!");
            $this->line("   Tables found: " . count($tables));
            
            if (count($tables) > 0) {
                $this->line("   Table names:");
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    $this->line("     - {$tableName}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Database operations failed!");
            $this->error("   Error: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function showConfiguration($connection)
    {
        $this->info("⚙️ Test 4: Configuration Information");
        $this->line("─" . str_repeat("─", 50));
        
        $config = Config::get("database.connections.{$connection}");
        
        if ($config) {
            $this->line("   Host: " . ($config['host'] ?? 'Not set'));
            $this->line("   Port: " . ($config['port'] ?? 'Not set'));
            $this->line("   Database: " . ($config['database'] ?? 'Not set'));
            $this->line("   Username: " . ($config['username'] ?? 'Not set'));
            $this->line("   Password: " . (empty($config['password']) ? 'Empty' : 'Set'));
            $this->line("   Driver: " . ($config['driver'] ?? 'Not set'));
            $this->line("   Charset: " . ($config['charset'] ?? 'Not set'));
            $this->line("   Collation: " . ($config['collation'] ?? 'Not set'));
        } else {
            $this->error("❌ Configuration not found for connection: {$connection}");
        }
        
        $this->newLine();
    }
}