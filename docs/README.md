# Índice de documentación
 
 - [Informe técnico](INFORME_TECNICO.md)
 - [Manual de desarrollador](MANUAL_DESARROLLADOR.md)
 - [Manual de despliegue](MANUAL_DESPLIEGUE.md)
 - [Mapeos de integración ERP](erp-integration-mapping.md)
 - [Modelo de base de datos](database-model.md)
 - [Diagrama (Mermaid)](der.mmd)
 
{{ ... }}
- **Laravel 12.x**: Framework PHP principal
- **Filament 4.x**: Panel de administración
{{ ... }}
### Requisitos Mínimos
- PHP 8.2+
- Composer 2.5+
- Node.js 18+
- MySQL 8.0+ (o SQLite para desarrollo)
- 2GB RAM
- 10GB almacenamiento
### Requisitos Recomendados
- PHP 8.3
- Composer 2.7+
- Node.js 20+
- MySQL 8.0+
- 4GB RAM
- 20GB SSD
# 2. Instalar Filament (v4)
composer require filament/filament
php artisan filament:install
