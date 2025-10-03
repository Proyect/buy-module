# Sistema de Compras - Documentación Técnica

## Descripción del Proyecto

Sistema integral de gestión de compras desarrollado con Laravel y Filament, diseñado para integrarse con sistemas ERP existentes que no cuentan con módulo de compras nativo.

## Características Principales

- **Gestión Completa de Compras**: Desde solicitudes hasta recepción de mercancías
- **Sistema de Aprobaciones**: Flujo de trabajo multi-nivel configurable
- **Integración ERP**: Conexión bidireccional con sistemas ERP existentes
- **Panel de Administración**: Interfaz moderna y intuitiva con Filament
- **Gestión de Usuarios**: Sistema robusto de roles y permisos
- **Control de Inventario**: Seguimiento automático de stock
- **Reportes y Analytics**: Dashboards informativos y reportes exportables
- **Auditoría Completa**: Log de todas las operaciones del sistema

## Arquitectura del Sistema

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Laravel App   │    │   ERP System    │
│   (Filament)    │◄──►│   (Core Logic)  │◄──►│   (Database)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   User Roles    │    │   Business      │    │   Data Mapping  │
│   & Permissions │    │   Logic         │    │   & Sync        │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Documentación Disponible

### 📋 [Guía de Instalación](laravel-filament-installation.md)
Instrucciones completas para instalar y configurar Laravel, Filament y todas las dependencias necesarias.

**Incluye:**
- Requisitos del sistema
- Instalación paso a paso
- Configuración de base de datos
- Instalación de paquetes adicionales
- Configuración de roles y permisos
- Comandos de desarrollo

### 🗄️ [Modelo de Base de Datos](database-model.md)
Documentación completa del esquema de base de datos y relaciones entre entidades.

**Incluye:**
- Diagrama de entidad-relación
- Estructura de tablas
- Relaciones entre modelos
- Migraciones y seeders
- Índices y optimización
- Integración con ERP

### 🔄 [Integración ERP - Mapeo de Datos](erp-integration-mapping.md)
Guía detallada para la integración con sistemas ERP externos y mapeo de datos.

**Incluye:**
- Conceptos de mapeo de datos
- Tipos de mapeo (directo, transformación, condicional)
- Implementación práctica
- Servicios de sincronización
- Validación y manejo de errores
- Mejores prácticas

## Flujo de Trabajo del Sistema

### 1. Solicitud de Compra
```
Usuario → Crear Solicitud → Llenar Items → Enviar para Aprobación
```

### 2. Proceso de Aprobación
```
Solicitud → Evaluación → Aprobación Multi-nivel → Notificación
```

### 3. Orden de Compra
```
Solicitud Aprobada → Crear Orden → Enviar a Proveedor → Seguimiento
```

### 4. Recepción y Control
```
Mercancía Recibida → Verificación → Actualización Inventario → Facturación
```

## Tecnologías Utilizadas

### Backend
- **Laravel 10.x**: Framework PHP principal
- **Filament 3.x**: Panel de administración
- **MySQL 8.0**: Base de datos principal
- **Spatie Laravel Permission**: Gestión de roles y permisos
- **Filament Shield**: Integración de permisos con Filament

### Frontend
- **Filament UI**: Componentes de interfaz
- **Alpine.js**: Interactividad
- **Tailwind CSS**: Estilos
- **Chart.js**: Gráficos y visualizaciones

### Integración
- **Laravel Jobs**: Procesamiento asíncrono
- **Laravel Events**: Sistema de eventos
- **Laravel Notifications**: Notificaciones
- **Custom Services**: Lógica de negocio

## Roles del Sistema

### 👤 Solicitante
- Crear solicitudes de compra
- Editar solicitudes pendientes
- Ver historial personal

### 🛒 Comprador
- Gestionar proveedores y productos
- Crear órdenes de compra
- Controlar inventario
- Generar reportes

### 👨‍💼 Supervisor
- Aprobar solicitudes pequeñas (< $1,000)
- Supervisar equipo
- Ver reportes departamentales

### 👔 Gerente
- Aprobar solicitudes medianas (< $10,000)
- Gestionar presupuestos
- Ver reportes ejecutivos

### 👑 Administrador
- Acceso completo al sistema
- Gestión de usuarios y roles
- Configuración del sistema

## Configuración del Entorno

### Requisitos Mínimos
- PHP 8.1+
- Composer 2.0+
- Node.js 16.0+
- MySQL 8.0+
- 2GB RAM
- 10GB almacenamiento

### Requisitos Recomendados
- PHP 8.2+
- Composer 2.4+
- Node.js 18.0+
- MySQL 8.0+
- 4GB RAM
- 20GB SSD

## Instalación Rápida

```bash
# 1. Clonar o crear proyecto
composer create-project laravel/laravel order-buy
cd order-buy

# 2. Instalar Filament
composer require filament/filament
php artisan filament:install --panels

# 3. Instalar paquetes adicionales
composer require spatie/laravel-permission
composer require bezhansalleh/filament-shield

# 4. Configurar base de datos
cp .env.example .env
php artisan key:generate

# 5. Ejecutar migraciones
php artisan migrate

# 6. Crear usuario administrador
php artisan make:filament-user

# 7. Iniciar servidor
php artisan serve
```

## Estructura del Proyecto

```
order-buy/
├── app/
│   ├── Filament/          # Recursos de Filament
│   ├── Models/            # Modelos Eloquent
│   ├── Services/          # Lógica de negocio
│   ├── Jobs/              # Tareas asíncronas
│   ├── Events/            # Eventos del sistema
│   └── Listeners/         # Manejadores de eventos
├── database/
│   ├── migrations/        # Migraciones de BD
│   ├── seeders/           # Datos iniciales
│   └── factories/         # Factories para testing
├── docs/                  # Documentación
├── config/                # Configuraciones
└── resources/             # Vistas y assets
```

## Comandos Útiles

### Desarrollo
```bash
# Crear recurso Filament
php artisan make:filament-resource ModelName

# Crear widget
php artisan make:filament-widget WidgetName

# Generar permisos Shield
php artisan shield:generate --all

# Limpiar caches
php artisan optimize:clear
```

### Base de Datos
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Refrescar base de datos
php artisan migrate:fresh --seed
```

### Testing
```bash
# Ejecutar tests
php artisan test

# Tests con cobertura
php artisan test --coverage
```

## Contribución

### Estándares de Código
- PSR-12 para PHP
- Convenciones de Laravel
- Documentación en inglés
- Tests unitarios y de integración

### Flujo de Trabajo
1. Fork del repositorio
2. Crear rama feature
3. Implementar cambios
4. Agregar tests
5. Documentar cambios
6. Crear Pull Request

## Soporte y Contacto

### Documentación
- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)

### Issues y Bugs
- Crear issue en GitHub
- Incluir logs de error
- Describir pasos para reproducir
- Especificar versión del sistema

## Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo `LICENSE` para más detalles.

## Changelog

### v1.0.0 (Enero 2024)
- ✅ Sistema básico de compras
- ✅ Integración con Filament
- ✅ Sistema de roles y permisos
- ✅ Integración ERP básica
- ✅ Documentación completa

### Próximas Versiones
- 🔄 Sincronización en tiempo real
- 📊 Analytics avanzados
- 📱 API REST completa
- 🔔 Notificaciones push
- 📈 Dashboard ejecutivo

---

**Desarrollado con ❤️ usando Laravel y Filament**
