<?php
/**
 * Script para crear la base de datos buy_module
 * Ejecutar con: php create-database.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🗄️ CREANDO BASE DE DATOS\n";
echo "========================\n\n";

try {
    // Conectar sin especificar base de datos
    $config = Config::get('database.connections.mysql');
    $config['database'] = null; // No especificar base de datos
    
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Conexión a MySQL exitosa!\n";
    
    // Crear base de datos
    $sql = "CREATE DATABASE IF NOT EXISTS buy_module CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    
    echo "✅ Base de datos 'buy_module' creada exitosamente!\n";
    
    // Verificar que se creó
    $stmt = $pdo->query("SHOW DATABASES LIKE 'buy_module'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ Verificación: Base de datos existe\n";
    } else {
        echo "❌ Error: No se pudo verificar la creación\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error al crear base de datos:\n";
    echo "   Código: " . $e->getCode() . "\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 BASE DE DATOS CREADA EXITOSAMENTE!\n";
echo "=====================================\n";
echo "Ahora puedes ejecutar:\n";
echo "  php artisan migrate\n";
echo "  php artisan db:seed\n";


