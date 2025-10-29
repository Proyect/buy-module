<?php
/**
 * Test rápido de conexión a base de datos
 * Ejecutar con: php test-db.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TEST DE CONEXIÓN A BASE DE DATOS\n";
echo "=====================================\n\n";

// Test 1: Verificar configuración
echo "📋 1. CONFIGURACIÓN:\n";
echo "─" . str_repeat("─", 30) . "\n";

$config = Config::get('database.connections.mysql');
echo "Host: " . ($config['host'] ?? 'No configurado') . "\n";
echo "Puerto: " . ($config['port'] ?? 'No configurado') . "\n";
echo "Base de datos: " . ($config['database'] ?? 'No configurado') . "\n";
echo "Usuario: " . ($config['username'] ?? 'No configurado') . "\n";
echo "Contraseña: " . (empty($config['password']) ? 'Vacía' : 'Configurada') . "\n";
echo "Driver: " . ($config['driver'] ?? 'No configurado') . "\n\n";

// Test 2: Conexión básica
echo "📡 2. CONEXIÓN BÁSICA:\n";
echo "─" . str_repeat("─", 30) . "\n";

try {
    $pdo = DB::connection('mysql')->getPdo();
    echo "✅ Conexión exitosa!\n";
    echo "Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "Versión del servidor: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
} catch (Exception $e) {
    echo "❌ Error de conexión:\n";
    echo "   Código: " . $e->getCode() . "\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

echo "\n";

// Test 3: Verificar base de datos
echo "🗄️ 3. VERIFICACIÓN DE BASE DE DATOS:\n";
echo "─" . str_repeat("─", 30) . "\n";

try {
    $dbName = DB::select('SELECT DATABASE() as db_name');
    echo "✅ Base de datos actual: " . $dbName[0]->db_name . "\n";
    
    $tables = DB::select('SHOW TABLES');
    echo "✅ Tablas encontradas: " . count($tables) . "\n";
    
    if (count($tables) > 0) {
        echo "   Tablas:\n";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "     - {$tableName}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error al verificar base de datos:\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test de consulta simple
echo "🔍 4. TEST DE CONSULTA:\n";
echo "─" . str_repeat("─", 30) . "\n";

try {
    $result = DB::select('SELECT 1 as test_value, NOW() as current_time');
    echo "✅ Consulta exitosa!\n";
    echo "   Valor de prueba: " . $result[0]->test_value . "\n";
    echo "   Hora actual: " . $result[0]->current_time . "\n";
} catch (Exception $e) {
    echo "❌ Error en consulta:\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Verificar tablas específicas
echo "📊 5. VERIFICACIÓN DE TABLAS ESPECÍFICAS:\n";
echo "─" . str_repeat("─", 30) . "\n";

$requiredTables = ['users', 'departments', 'sessions', 'cache', 'jobs'];

foreach ($requiredTables as $table) {
    try {
        $exists = DB::select("SHOW TABLES LIKE '{$table}'");
        if (count($exists) > 0) {
            $count = DB::table($table)->count();
            echo "✅ Tabla '{$table}': Existe ({$count} registros)\n";
        } else {
            echo "⚠️  Tabla '{$table}': No existe\n";
        }
    } catch (Exception $e) {
        echo "❌ Error al verificar tabla '{$table}': " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "🎉 TEST COMPLETADO\n";
echo "==================\n";


