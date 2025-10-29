# ⚡ Comandos Rápidos - Sistema de Compras

## 🚀 **Inicio Rápido**

```bash
# Levantar el sistema completo
cd c:\Users\amdiaz\Desktop\code\php\buy-module\src\buy
php artisan serve --port=5003
# En otra terminal:
npm run dev
```

## 🗄️ **Base de Datos**

```bash
# Resetear base de datos con datos de prueba
php artisan migrate:fresh --seed

# Crear solo migraciones
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Ver estado de migraciones
php artisan migrate:status
```

## 🏗️ **Crear Nuevo Módulo**

```bash
# 1. Crear migración
php artisan make:migration create_[tabla]_table

# 2. Crear modelo con todo
php artisan make:model [NombreModelo] -mfsp

# 3. Crear recurso Filament
php artisan make:filament-resource [NombreModelo]

# 4. Crear policy
php artisan make:policy [NombreModelo]Policy --model=[NombreModelo]

# 5. Crear tests
php artisan make:test [NombreModelo]Test
```

## 🧹 **Limpieza y Optimización**

```bash
# Limpiar todos los caches
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear

# Optimizar para producción
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Limpiar logs
echo "" > storage/logs/laravel.log
```

## 🔍 **Debugging**

```bash
# Abrir Tinker
php artisan tinker

# Ver configuración
php artisan config:show database

# Ver rutas
php artisan route:list | grep filament

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

## 🧪 **Testing**

```bash
# Ejecutar todos los tests
php artisan test

# Tests específicos
php artisan test --filter=DepartmentTest

# Tests con coverage
php artisan test --coverage

# Tests de navegador
php artisan dusk
```

## 📊 **Datos de Prueba**

```bash
# Crear departamentos
php artisan tinker
>>> Department::factory(5)->create()

# Crear usuarios
>>> User::factory(3)->create()

# Ver datos
>>> Department::with('manager')->get()
```

## 🔐 **Permisos y Roles**

```bash
# Generar permisos Shield
php artisan shield:generate --all

# Ver roles de usuario
php artisan tinker
>>> User::first()->roles
>>> User::first()->permissions
```

## 📦 **Dependencias**

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias Node
npm install

# Actualizar dependencias
composer update
npm update
```

## 🌐 **Servidor**

```bash
# Puerto 5003 (recomendado)
php artisan serve --port=5003

# Puerto personalizado
php artisan serve --port=8000

# Con host específico
php artisan serve --host=0.0.0.0 --port=5003
```

## 📁 **Estructura de Archivos**

```
buy/
├── app/Filament/Resources/        # Recursos CRUD
├── app/Models/                    # Modelos Eloquent
├── database/migrations/           # Migraciones
├── database/factories/            # Factories
├── database/seeders/              # Seeders
├── resources/lang/es/             # Traducciones
└── tests/                         # Tests
```

## 🎨 **Filament**

```bash
# Crear recurso completo
php artisan make:filament-resource Supplier --generate

# Crear página personalizada
php artisan make:filament-page Settings

# Crear widget
php artisan make:filament-widget StatsWidget

# Crear tema
php artisan make:filament-theme
```

## 🔧 **Configuración Regional**

```bash
# Verificar configuración de idioma
php artisan tinker
>>> config('app.locale')
>>> config('app.timezone')

# Verificar configuración de moneda
>>> config('currency.default')
```

## 📝 **Logs y Monitoreo**

```bash
# Ver logs de errores
grep "ERROR" storage/logs/laravel.log

# Ver logs de compras
tail -f storage/logs/purchases.log

# Limpiar logs
echo "" > storage/logs/laravel.log
```

## 🚨 **Solución de Problemas**

```bash
# Error de conexión BD
php artisan config:show database

# Error 404 Filament
php artisan route:clear && php artisan config:clear

# Error de permisos
php artisan shield:generate --all

# Error de cache
php artisan cache:clear && php artisan config:clear
```

## 📊 **Métricas del Sistema**

```bash
# Ver estadísticas de BD
php artisan tinker
>>> DB::table('departments')->count()
>>> DB::table('users')->count()

# Ver uso de memoria
php artisan tinker
>>> memory_get_usage(true)
```

## 🔄 **Git Workflow**

```bash
# Crear rama para nueva funcionalidad
git checkout -b feature/nuevo-modulo

# Commit con mensaje descriptivo
git commit -m "feat: agregar módulo de proveedores"

# Push a rama
git push origin feature/nuevo-modulo
```

## 📚 **Referencias Rápidas**

| Comando | Descripción |
|---------|-------------|
| `php artisan list` | Ver todos los comandos |
| `php artisan help [comando]` | Ayuda de comando específico |
| `composer show` | Ver paquetes instalados |
| `npm list` | Ver paquetes Node instalados |
| `php artisan route:list` | Ver todas las rutas |
| `php artisan config:show` | Ver configuración |

## 🎯 **URLs Importantes**

- **Aplicación:** http://localhost:5003
- **Admin Panel:** http://localhost:5003/admin
- **phpMyAdmin:** http://localhost/phpmyadmin
- **Documentación:** [Manual del Desarrollador](./MANUAL_DESARROLLADOR.md)

---

*Mantén este archivo actualizado con comandos frecuentemente utilizados.*


