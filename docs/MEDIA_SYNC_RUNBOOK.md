# Runbook de sincronización de media pública (`gestionplanes-f5`)

## Objetivo
Recuperar portadas de planes y centros en entorno local sin borrar datos, copiando solo archivos faltantes.

## Comando principal
```bash
cd /Users/pallaresfj/Herd/gestionplanes-f5
bash scripts/sync-public-media.sh
```

## Qué hace
1. Crea carpetas destino en `storage/app/public`.
2. Copia archivos faltantes desde:
   - `/Users/pallaresfj/Herd/gestionplanes/storage/app/public`
   - `/Users/pallaresfj/Herd/plan/storage/app/public`
3. Crea `public/storage` si no existe.
4. Reporta faltantes reales:
   - `missing_plan_images`
   - `missing_center_images`

## Después de sincronizar
```bash
cd /Users/pallaresfj/Herd/gestionplanes-f5
php artisan optimize:clear
```

## Verificación manual
1. `https://gestionplanes-f5.test/planes`
2. `https://gestionplanes-f5.test/plan/1`
3. `https://gestionplanes-f5.test/centers`
4. `https://gestionplanes-f5.test/center/1`

## Nota de fallback
Si un archivo no existe en ninguna fuente, las vistas usarán imagen institucional:
- Planes: `public/images/planes.jpg`
- Centros: `public/images/centros.jpg`
