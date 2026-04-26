# CajaLink (POS)

CajaLink es un sistema web de punto de venta (POS) desarrollado con **Laravel 12**.

## Módulos principales

- **POS**: creación de ventas, impresión de ticket, lector de báscula (según configuración).
- **Administración**: productos, usuarios, ventas, reportes y configuraciones.
- **Facturación / Suscripciones**: planes, pagos y portal (Stripe opcional).
- **Plataforma (Super Admin)**: gestión de tiendas, usuarios y pagos.
- **Soporte**: chat entre cliente (tienda) y soporte.

## Requisitos

- PHP 8.2+
- Composer
- Node.js + npm
- Base de datos: **MySQL** o **SQLite**

## Instalación (local)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Base de datos

Configura `.env` con el driver que usarás:

**Opción A: SQLite**

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/absoluta/al/proyecto/database/database.sqlite
```

**Opción B: MySQL (ejemplo MAMP)**

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=laravel_cajalink
DB_USERNAME=root
DB_PASSWORD=root
```

Luego ejecuta migraciones y seed:

```bash
php artisan migrate
php artisan db:seed
```

## Assets (Vite)

Para desarrollo:

```bash
npm run dev
```

Para producción:

```bash
npm run build
```

## Archivos públicos (avatars)

El perfil permite subir foto (avatar). Crea el symlink de storage:

```bash
php artisan storage:link
```

## Ejecutar el proyecto

```bash
php artisan serve
```

## Usuarios de prueba (seed)

Al ejecutar `php artisan db:seed` se crean usuarios de ejemplo:

- `superadmin@pos.test` / `password` (Super Admin)
- `admin@pos.test` / `password` (Admin)
- `supervisor@pos.test` / `password` (Supervisor)
- `cajero1@pos.test` / `password` (Cajero)
- `cajero2@pos.test` / `password` (Cajero)

## Rutas principales

- Perfil: `/profile`
- POS: `/pos` (requiere suscripción activa)
- Admin: `/admin` (requiere permisos de administración)
- Plataforma (Super Admin): `/plataforma`
- Soporte (cliente): `/soporte`
- Soporte (admin): `/soporte/admin`

## Roles y permisos (resumen)

Los roles se manejan en `App\Enums\UserRole`.

- **SUPER_ADMIN**: acceso a Plataforma y administración global.
- **SUPPORT**: acceso al panel de soporte (`/soporte/admin`).
- **ADMIN / SUPERVISOR / CASHIER**: roles de tienda (POS / administración según permisos).

## Soporte (Chat)

- Cliente:
  - GET `/soporte`
  - GET `/soporte/mensajes`
  - POST `/soporte/mensajes`
- Soporte:
  - GET `/soporte/admin`
  - GET `/soporte/admin/{conversation}`
  - GET `/soporte/admin/{conversation}/mensajes`
  - POST `/soporte/admin/{conversation}/mensajes`

## Comandos útiles

Formato:

```bash
./vendor/bin/pint
```

Tests:

```bash
php artisan test
```

## Variables opcionales (Stripe)

Si habilitas pagos por Stripe, configura en `.env`:

- `STRIPE_KEY`
- `STRIPE_SECRET`
- `STRIPE_WEBHOOK_SECRET`

## Troubleshooting

- **Warning: Module "imagick" is already loaded**: es un warning del entorno PHP, no impide ejecutar el proyecto.
- Si cambias `.env` o rutas y algo se queda “pegado”:

```bash
php artisan optimize:clear
php artisan view:clear
```
