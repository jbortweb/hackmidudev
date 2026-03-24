# HackMidu - Repositorio de Proyectos de la Hackathon de Midudev

HackMidu es una plataforma web para visualizar y gestionar los proyectos presentados en la hackathon de [Midudev](https://twitch.tv/midudev). Permite a los participantes mostrar sus proyectos, recibir likes y comentarios de la comunidad.

## Tecnologias

- **Frontend**: Vue 3 + Vite + TailwindCSS 4
- **Backend**: Laravel 11 (API REST)
- **Base de datos**: SQLite
- **Autenticacion**: Clerk (JWT)
- **Iconos**: Lucide Vue
- **Notificaciones**: Vue3 Toastify

## Requisitos

- PHP 8.2+
- Node.js 18+
- Composer
- NPM

## Instalacion

### 1. Clonar el repositorio

```bash
git clone <repo-url>
cd hackmidudev
```

### 2. Backend (Laravel)

```bash
cd backend

# Instalar dependencias
composer install

# Copiar archivo de configuracion
cp .env.example .env

# Generar clave de aplicacion
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Crear enlace simbolico para almacenamiento
php artisan storage:link

# Iniciar servidor de desarrollo
php artisan serve
```

El servidor backend estara disponible en `http://localhost:8000`

### 3. Frontend (Vue)

```bash
cd frontend

# Instalar dependencias
npm install

# Copiar archivo de configuracion
cp .env.example .env

# Iniciar servidor de desarrollo
npm run dev
```

La aplicacion frontend estara disponible en `http://localhost:5173`

### 4. Configuracion de Clerk

1. Crear una cuenta en [Clerk](https://clerk.dev)
2. Crear una nueva aplicacion
3. Obtener las claves `Publishable Key` y `Secret Key`
4. Configurar las variables en `.env`:

**Backend (`backend/.env`):**
```env
CLERK_PUBLISHABLE_KEY=pk_test_...
CLERK_SECRET_KEY=sk_test_...
```

**Frontend (`frontend/.env`):**
```env
VITE_CLERK_PUBLISHABLE_KEY=pk_test_...
```

## Estructura del Proyecto

```
hackmidudev/
├── backend/                 # API Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/API/  # Controladores API
│   │   │   └── Middleware/       # ClerkAuthMiddleware
│   │   └── Models/              # Modelos Eloquent
│   ├── database/
│   │   ├── migrations/           # Migraciones BD
│   │   └── seeders/             # Datos de prueba
│   ├── routes/
│   │   └── api.php              # Rutas API
│   └── storage/
│       └── app/public/img/      # Imagenes subidas
├── frontend/                 # Aplicacion Vue
│   ├── src/
│   │   ├── components/          # Componentes reutilizables
│   │   ├── views/               # Vistas de paginas
│   │   ├── services/            # Servicios API
│   │   └── router/              # Configuracion de rutas
│   └── public/
└── ROADMAP.md               # Progreso del proyecto
```

## API Endpoints

### Rutas Publicas

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/projects` | Lista todos los proyectos (soporta `?year=2025`, `?per_page=12`) |
| GET | `/api/projects/{id}` | Obtiene detalle de un proyecto |

### Rutas Protegidas (Requieren autenticacion Clerk)

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| POST | `/api/projects` | Crear nuevo proyecto |
| PUT | `/api/projects/{id}` | Actualizar proyecto |
| DELETE | `/api/projects/{id}` | Eliminar proyecto |
| POST | `/api/projects/{id}/like` | Dar/quitar like |
| POST | `/api/projects/{id}/comments` | Agregar comentario |
| POST | `/api/user/sync` | Sincronizar perfil de usuario |
| GET | `/api/user/profile` | Obtener perfil con proyectos |
| DELETE | `/api/user/destroy` | Eliminar cuenta y proyectos |
| POST | `/api/images/upload` | Subir imagen |
| DELETE | `/api/images/delete` | Eliminar imagen |

## Funcionalidades

### Completadas

- [x] Autenticacion con Clerk (JWT)
- [x] Listado de proyectos con paginacion (12 por pagina)
- [x] Filtrado por ano
- [x] Slider de proyectos ganadores
- [x] Detalle de proyecto con galeria de imagenes
- [x] Sistema de likes
- [x] Sistema de comentarios
- [x] Perfil de usuario con avatar personalizable
- [x] Subida de imagenes (avatares y proyectos)
- [x] Dashboard personal
- [x] Formulario de creacion/edicion de proyectos
- [x] Eliminacion de cuenta y proyectos
- [x] Modal de confirmacion para acciones destructivas

### En Desarrollo

- [ ] Conversion automatica de imagenes a WebP
- [ ] Buscador global de proyectos
- [ ] Filtrado por tecnologias/categorias

## Seguridad

### Middleware de Autenticacion

El proyecto utiliza `ClerkAuthMiddleware` que:

1. Valida tokens JWT de Clerk
2. Verifica la firma usando JWKS (JSON Web Key Set)
3. Crea/actualiza usuarios automaticamente en la BD
4. Almacena en cache las claves JWKS por 1 hora

### Autorizacion

- Solo el propietario puede editar/eliminar sus proyectos
- Solo el propietario puede eliminar su cuenta
- Los avatares de storage nunca se sobreescriben con avatares de Clerk

### CORS

Configurado para permitir requests desde:
- `http://localhost:5173` (desarrollo)
- `http://localhost:8000` (API)

Para produccion, anadir la URL del frontend en `backend/config/cors.php`.

## Variables de Entorno

### Backend

```env
APP_NAME=HackMidu
APP_ENV=local
APP_KEY=<generado>
APP_DEBUG=true
APP_URL=http://localhost:8000

CLERK_PUBLISHABLE_KEY=pk_test_...
CLERK_SECRET_KEY=sk_test_...
```

### Frontend

```env
VITE_CLERK_PUBLISHABLE_KEY=pk_test_...
VITE_API_URL=http://localhost:8000/api
```

## Desarrollo

### Comandos utiles

```bash
# Backend
php artisan route:list           # Listar rutas
php artisan migrate:fresh --seed # Reiniciar BD con datos
php artisan cache:clear          # Limpiar cache

# Frontend
npm run build                   # Compilar para produccion
npm run lint                    # Verificar codigo
```

### Normas de Contribución

1. Usar nombres descriptivos para commits
2. Mantener el estilo de codigo consistente
3. Probar cambios antes de hacer commit
4. No incluir archivos sensibles en commits

## Licencia

Este proyecto es software libre y esta disponible bajo la licencia MIT.

## Créditos

- [Midudev](https://twitch.tv/midudev) - Inspiracion y organizacion de la hackathon
- [Clerk](https://clerk.dev) - Autenticacion
- [Laravel](https://laravel.com) - Framework backend
- [Vue.js](https://vuejs.org) - Framework frontend

---

**Hackathon CubePath 2026**
