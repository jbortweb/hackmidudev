# Roadmap: HackMiduDev

Estado actual: **Fase 4: Funcionalidades Completas** ✅

## Fase 1: Configuración del Entorno y Arquitectura ✅

- [x] Inicializar proyecto Laravel (Backend API).
- [x] Inicializar proyecto Vue.js con Vite (Frontend).
- [x] Configurar Tailwind CSS 4 en el frontend.
- [x] Configurar base de datos (SQLite).
- [x] Configurar variables de entorno (.env) protegidas.
- [x] Configurar Clerk en los archivos .env (Frontend y Backend).
- [x] Configurar CORS para desarrollo local.
- [x] Crear enlace simbólico para almacenamiento de imágenes.

## Fase 2: Backend (Laravel API) ✅

- [x] Crear migraciones para `users`, `projects`, `project_likes`, `comments`.
- [x] Crear modelos `User`, `Project`, `ProjectLike`, `Comment`.
- [x] Configurar Middleware `ClerkAuthMiddleware` para validar tokens JWT.
- [x] Implementar autenticación con Clerk (validación JWKS).
- [x] Endpoints API públicos (listado y detalle de proyectos).
- [x] Endpoints API protegidos (CRUD proyectos, likes, comentarios).
- [x] Endpoints para gestión de perfil (sync, profile, destroy).
- [x] Controlador para subida de imágenes (`ImageController`).
- [x] Autorización: verificación de propietario en update/destroy.
- [x] Poblar base de datos con Seeder de prueba.
- [x] Paginación en listado de proyectos.
- [x] eager loading de relaciones (user, comments.user).

## Fase 3: Frontend (Vue.js) ✅

### Componentes Base

- [x] **Header** responsivo con Auth de Clerk (sign in/up, logout).
- [x] **Footer** con información del proyecto.
- [x] **ProjectCard** - Tarjeta de proyecto con imagen, título, tecnologías, likes.
- [x] **Slider** de Ganadores dinámico.
- [x] **ImageUploader** con drag & drop y preview.
- [x] **ConfirmModal** para confirmaciones de logout/eliminar.
- [x] **Loader/Spinner** estilo hacker/terminal.

### Vistas

- [x] **HomeView** con listado de proyectos por año.
- [x] **HomeView** con slider de ganadores.
- [x] **HomeView** con paginación (12 proyectos/página).
- [x] **HomeView** con historial de años anteriores.
- [x] **ProjectDetailView** con galeria de imágenes.
- [x] **ProjectDetailView** con información del autor y avatar.
- [x] **ProjectDetailView** con sistema de likes.
- [x] **ProjectDetailView** con sistema de comentarios (paginados).
- [x] **DashboardView** con proyectos del usuario.
- [x] **DashboardView** con botón "Volver al inicio".
- [x] **ProfileView** con edición de perfil y avatar.
- [x] **ProfileView** con botón de eliminar cuenta.
- [x] **ProjectFormView** para crear/editar proyectos.

### Autenticación y UX

- [x] Rutas protegidas con meta `requiresAuth`.
- [x] Redirección a login cuando no autenticado.
- [x] Sincronización de perfil con Clerk.
- [x] Notificaciones toast (éxito, error, info).
- [x] Estados de carga con animaciones.
- [x] Estados vacíos con mensajes descriptivos.

## Fase 4: Seguridad ✅

- [x] Middleware de autenticación Clerk con JWKS.
- [x] Verificación de propietario en edición/eliminación de proyectos.
- [x] Verificación de propietario en eliminación de cuenta.
- [x] Validación de inputs en todos los endpoints.
- [x] CORS configurado para desarrollo.
- [x] Archivos sensibles en .gitignore.
- [x] Protección de avatares de storage vs Clerk.
- [x] Límite de 5 proyectos por usuario.
- [x] Rate limiting: 30 segundos entre comentarios (anti-spam).

## Fase 5: Despliegue (Pendiente)

- [ ] Optimización de assets para producción.
- [ ] Configurar CORS para URL de producción.
- [ ] Actualizar variables Clerk a producción.
- [ ] Desplegar en servidor (Vercel/Render + Railway/Fly.io).
- [ ] Tests de integración en producción.

## Funcionalidades Pendientes

- [ ] Conversión automática de imágenes a WebP.
- [ ] Búsqueda global de proyectos.
- [ ] Filtrado por tecnologías/categorías.
- [ ] Animaciones de entrada (Framer Motion o transiciones Vue).
- [ ] Panel de administración para marcar winners.
- [ ] Tests automatizados.

**Hackathon CubePath 2026**
