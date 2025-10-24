# Manual de Despliegue

## Preparación
- Variables de entorno (`src/buy/.env`): DB, `APP_KEY`, `APP_URL`
- Asegurar permisos de escritura en `storage/` y `bootstrap/cache/`

## Pasos
```bash
composer install --no-dev --optimize-autoloader
php src/buy/artisan migrate --force
php src/buy/artisan config:cache
php src/buy/artisan route:cache
npm --prefix src/buy ci
npm --prefix src/buy run build
```

## Colas
```bash
php src/buy/artisan queue:work --tries=3
```

## Limpieza/Cache
```bash
php src/buy/artisan cache:clear
php src/buy/artisan config:clear
php src/buy/artisan route:clear
```

## Verificaciones
- Panel Filament accesible
- Exportaciones CSV/PDF funcionando
- Integración ERP: probar mappers si aplica

## Rollback
```bash
php src/buy/artisan migrate:rollback --step=1 --force
```
