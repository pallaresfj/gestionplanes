# UAT Dirigido - gestionplanes-f5 (Laravel 12 + Filament 5)

Fecha de ejecución base: 2026-02-18
Ambiente: `/Users/pallaresfj/Herd/gestionplanes-f5` (rama `codex/migration-filament5`)

## 1) Estado técnico ya verificado (automatizado)

- [x] `php artisan test --testsuite=Feature` (entorno de testing aislado)
- [x] `php artisan route:list | rg "admin|sso"` (rutas esperadas presentes)
- [x] Política de datos activa: no usar `migrate:fresh` ni seeders destructivos en BD de trabajo
- [x] Stack objetivo activo:
  - `filament/filament v5.2.1`
  - `livewire/livewire v4.1.4`
  - `bezhansalleh/filament-shield 4.1.0`
  - `pxlrbt/filament-excel v3.6.0`

## 2) Preconditions UAT manual

- [x] `.env` aislado y operativo (`APP_URL`, `SESSION_COOKIE`, `CACHE_PREFIX`, credenciales SSO de pruebas)
- [x] Usuario de prueba por rol disponible: `super_admin`, `Soporte`, `Directivo`, `Centro`, `Area`, `Docente`
- [x] Permiso `panel_user` asignado a roles con acceso al panel
- [x] Servicio SSO sandbox activo
- [ ] Navegador limpio (incógnito recomendado para pruebas de sesión/logout)

## 2.1) Modo seguro de datos (obligatorio)

- [x] BD de trabajo fija: `laravel_plan_f5`
- [x] Tests aislados en SQLite: `database/testing.sqlite`
- [x] `.env.testing` presente y separado de `.env`
- [x] No ejecutar en BD de trabajo:
  - `php artisan migrate:fresh`
  - `php artisan db:seed` (incluye `RoleSeeder`)

Snapshot de referencia (solo lectura):
- `users=40`
- `plans=19`
- `subjects=163`
- `rubrics=1125`
- `centers=8`
- `roles=6`
- `permissions=92`
- [x] Validación `DATA-SAFE-01`: ejecutar `SsoAuthTest` no alteró conteos en `laravel_plan_f5`

## 3) Flujos críticos de autenticación y sesión (P0)

### AUTH-01 Login institucional exitoso
- Ruta: `/admin/login` -> botón `Ingresar con Cuenta Institucional`
- Paso:
  1. Abrir `/admin/login`
  2. Clic en login institucional
  3. Completar login en IdP de pruebas
- Esperado:
  - Redirección a `/admin`
  - Usuario autenticado en panel
  - Sin errores en pantalla
- Evidencia: captura de `/admin` + usuario visible en menú
- Estado: [x] (validado técnicamente)
- Evidencia técnica:
  - `php artisan test --testsuite=Feature` -> `it creates user, assigns docente role and authenticates with valid...`
  - `curl -Ik https://gestionplanes-f5.test/admin/login` -> `HTTP/2 200`

### AUTH-02 Callback con `state mismatch`
- Ruta: `/sso/callback`
- Paso:
  1. Iniciar login
  2. Alterar manualmente query `state` antes del callback (o repetir callback viejo)
- Esperado:
  - Mensaje de error de seguridad
  - No autenticación local
- Estado: [x] (validado técnicamente)
- Evidencia técnica:
  - `php artisan test --testsuite=Feature` -> `it rejects callback when state does not match`

### AUTH-03 Silent session check OK
- Rutas: `/sso/session-check/start`, `/sso/session-check/callback`
- Paso:
  1. Iniciar sesión
  2. Navegar en `/admin` y esperar intervalo configurado
- Esperado:
  - Verificación silenciosa sin expulsar al usuario
  - Regreso a la URL original del panel
- Estado: [x] (validado técnicamente)
- Evidencia técnica:
  - `php artisan test --testsuite=Feature` -> `it starts silent idp session check for authenticated users`

### AUTH-04 Silent session check con sesión IdP expirada
- Paso:
  1. Iniciar sesión local
  2. Invalidar sesión en IdP
  3. Forzar ciclo de `session-check`
- Esperado:
  - Logout local
  - Redirección a `/admin/login`
- Estado: [x] (validado técnicamente)
- Evidencia técnica:
  - `php artisan test --testsuite=Feature` -> `it logs out local session when idp silent check reports login_required`

### AUTH-05 Frontchannel logout válido
- Ruta: `/sso/frontchannel-logout`
- Paso:
  1. Iniciar sesión
  2. Ejecutar frontchannel logout firmado por el IdP
- Esperado:
  - Sesión local cerrada
  - Cookie de sesión invalidada
  - Redirección segura al `next` permitido
- Estado: [x] (validado técnicamente)
- Evidencia técnica:
  - `php artisan test --testsuite=Feature` -> `it logs out local session on valid frontchannel logout request`

## 4) Autorización de panel y permisos (P0)

### AUTHZ-01 Gate panel
- Regla: `canAccessPanel` requiere permiso `panel_user`
- Paso:
  1. Usuario sin `panel_user` intenta `/admin`
  2. Usuario con `panel_user` intenta `/admin`
- Esperado:
  - Sin permiso: acceso denegado/redirect
  - Con permiso: acceso normal
- Estado: [x] (validado técnicamente)
- Evidencia técnica:
  - `php artisan tinker --execute='...'` -> `NO_PANEL=false`, `WITH_PANEL=true`
  - `curl -Ik https://gestionplanes-f5.test/admin` -> `HTTP/2 302` hacia `/admin/login` sin sesión

### AUTHZ-02 Shield CRUD por rol
- Paso:
  1. Entrar como cada rol de negocio
  2. Verificar visibilidad de recursos/acciones (crear/editar/eliminar/exportar)
- Esperado:
  - Cada rol solo ve/ejecuta permisos asignados
- Estado: [ ] (pendiente validación UI manual por rol)
- Evidencia técnica parcial:
  - `php artisan tinker --execute='...'` -> `super_admin=yes`, `Soporte=yes`, `Directivo=yes`, `Centro=yes`, `Area=yes`, `Docente=yes` para permiso `panel_user`

## 5) Checklist por recurso Filament (8 recursos)

## 5.1 Centers (`/admin/centers`)
Campos clave:
- `academic_year`, `name`, `description`, `objective`, `image_path`, `user_id`

Relation managers:
- Teachers, Students, Activities, Budgets

Casos:
- [ ] CENTER-01 Listar (búsqueda, orden, paginación)
- [ ] CENTER-02 Crear centro válido
- [ ] CENTER-03 Validaciones requeridas
- [ ] CENTER-04 Editar centro
- [ ] CENTER-05 Eliminar centro (confirmación y efecto)
- [ ] CENTER-06 Exportar Excel (bulk)
- [ ] CENTER-07 Teacher relation CRUD + upload foto + export
- [ ] CENTER-08 Student relation CRUD + filtro por curso + export
- [ ] CENTER-09 Activity relation CRUD + fechas + export
- [ ] CENTER-10 Budget relation CRUD inline + cálculo total + export

## 5.2 Plans (`/admin/plans`)
Campos clave:
- `school_profile_id`, `cover`, `name`, `year`, `users`, `justification`, `objectives`, `methodology`

Relation managers:
- Subjects

Casos:
- [ ] PLAN-01 Listar + filtros
- [ ] PLAN-02 Crear plan con docentes
- [ ] PLAN-03 Editar tabs (justificación/objetivos/metodología)
- [ ] PLAN-04 Eliminar plan
- [ ] PLAN-05 Exportar Excel (bulk)
- [ ] PLAN-06 Subjects relation: create/edit/delete + acción `abrir` + export

## 5.3 Subjects (`/admin/subjects`)
Campos clave:
- `grade`, `plan_id`, `name`, `weekly_hours`, `users`, `interest_centers`, `contributions`, `strategies`

Relation managers:
- Topics, Rubrics

Casos:
- [ ] SUBJECT-01 Listar + filtros por grado/área/docente
- [ ] SUBJECT-02 Crear asignatura
- [ ] SUBJECT-03 Editar y persistencia de relaciones (docentes/centros)
- [ ] SUBJECT-04 Eliminar asignatura
- [ ] SUBJECT-05 Exportar Excel (bulk)
- [ ] SUBJECT-06 Topics relation CRUD + filtros + export
- [ ] SUBJECT-07 Rubrics relation CRUD + filtros + export

## 5.4 Rubrics (`/admin/rubrics`)
Campos clave:
- `period`, `subject_id`, `criterion`, `superior_level`, `high_level`, `basic_level`, `low_level`

Casos:
- [ ] RUBRIC-01 Listar + filtros por período/asignatura/área
- [ ] RUBRIC-02 Crear rúbrica
- [ ] RUBRIC-03 Editar niveles
- [ ] RUBRIC-04 Eliminar
- [ ] RUBRIC-05 Exportar Excel (bulk)

## 5.5 Topics (`/admin/topics`)
Campos clave:
- `period`, `subject_id`, `standard`, `dba`, `competencies`, `contents`

Casos:
- [ ] TOPIC-01 Listar + filtros por período/asignatura/área
- [ ] TOPIC-02 Crear tópico
- [ ] TOPIC-03 Editar contenido rico
- [ ] TOPIC-04 Eliminar
- [ ] TOPIC-05 Exportar Excel (bulk)

## 5.6 Users (`/admin/users`)
Campos clave:
- `name`, `email`, `password`, `profile_photo_path`, `roles`

Casos:
- [ ] USER-01 Listar + filtros por rol/verificado
- [ ] USER-02 Crear usuario y asignar rol
- [ ] USER-03 Editar usuario + upload avatar
- [ ] USER-04 Acción `verify` (verificación email)
- [ ] USER-05 Eliminar usuario
- [ ] USER-06 Exportar Excel (bulk)

## 5.7 Roles Shield (`/admin/shield/roles`)
Campos clave:
- `name`, `guard_name`, permisos por recursos/páginas/widgets

Casos:
- [ ] ROLE-01 Listar roles
- [ ] ROLE-02 Crear rol nuevo con permisos mínimos
- [ ] ROLE-03 Editar permisos existentes
- [ ] ROLE-04 Validar efecto de permisos en usuario real
- [ ] ROLE-05 Eliminar rol no crítico

## 5.8 School Profiles (`/admin/school-profiles`)
Campos clave:
- `mission`, `vision`

Casos:
- [ ] SCHOOL-01 Ver registro existente
- [ ] SCHOOL-02 Editar misión/visión
- [ ] SCHOOL-03 Persistencia y render correcto en recursos que lo usan

## 6) Pruebas de no regresión pública

- [ ] HOME-01 `/` responde OK
- [ ] HOME-02 vistas públicas ligadas a planes/centros/asignaturas siguen operativas
- [ ] HOME-03 ninguna ruta pública rompe por cambios de Filament 5

## 7) Criterios de salida UAT

- [ ] 0 defectos P0
- [ ] 0 defectos P1
- [ ] Defectos P2/P3 documentados con ticket y plan
- [ ] Evidencia mínima por flujo crítico (captura + pasos + resultado)

## 8) Registro de hallazgos

Formato recomendado:
- ID: `P0|P1|P2|P3-<módulo>-<número>`
- Caso UAT: `<ID de checklist>`
- Entorno: `local f5`
- Pasos para reproducir:
- Resultado actual:
- Resultado esperado:
- Evidencia:
- Estado: `Abierto|Corregido|Revalidado`

## 9) Comandos de apoyo (durante UAT)

```bash
cd /Users/pallaresfj/Herd/gestionplanes-f5
php artisan optimize:clear
php artisan test --testsuite=Feature
php artisan route:list | rg "sso|admin"
# NO usar en BD de trabajo:
# php artisan migrate:fresh --seed
# Seeder seguro (idempotente) si faltan roles/permisos:
# php artisan db:seed --class=RolePermissionSafeSeeder
```
