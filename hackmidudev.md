# Plan de proyecto hackathon: Repositorio de Proyectos Ganadores

## 1. Resumen ejecutivo

Proponemos una plataforma web para la Hackathon CubePath donde participantes puedan **registrarse** y **subir sus proyectos** (incluyendo nombre, descripción, hasta 3 imágenes en formato WebP y URL del proyecto, url repositorio). Cada proyecto incluirá el año de la hackathon y un indicador de “ganador” si aplica. En la web pública habrá un menú para explorar proyectos por año y una sección especial de “Proyectos Ganadores” por año. Esto crea un histórico accesible de ediciones anteriores. La aplicación usará **Vue.js** en el frontend, **Laravel (PHP)** en el backend, y Clerk para la autenticación. En las siguientes secciones detallamos funcionalidades, stack tecnológico, arquitectura, pruebas y entregables necesarios para un MVP robusto que cumpla con los requisitos de la hackathon.

## 2. Idea del proyecto y problema que resuelve

Actualmente no existe un lugar centralizado donde guardar y mostrar los proyectos participantes de la hackathon. hackmidudev resuelve esto creando una especie de “portafolio de hackathon”:

- **Registro de participantes:** cada usuario crea una cuenta (vía Clerk) y puede ingresar uno o más proyectos asociados a la hackathon actual.
- **Gestión de proyectos:** cada proyecto tiene campos básicos (título, breve descripción, URL al repositorio/demo, imágenes) y el año del evento. Opcionalmente, se marca como “ganador”.
- **Visualización pública:** en la página principal y en el menú, los visitantes pueden ver los proyectos organizados por año y navegar especialmente los proyectos ganadores (por año). El frontend se comportará tipo blog: páginas listando proyectos del año X, y páginas individuales para cada proyecto.
- **Motivación:** Así los participantes pueden exhibir sus proyectos actuales y pasados, y la comunidad accede a ejemplos de soluciones ganadoras y propuestas creativas. Este repositorio en línea sirve como recurso.

**Problema resuelto:** Centraliza el almacenamiento y consulta de proyectos de la hackathon, facilita la difusión de ganadores y permite a participantes guardar su trabajo. Evita dispersión en issues de GitHub (inadecuado para contenido multimedia) y provee una UI amigable para explorar proyectos.

## 3. Público objetivo y valor para la comunidad

El público principal son los **participantes de la Hackathon CubePath** (actuales y de futuras ediciones) y la comunidad tecnológica interesada. GasFinder beneficia a:

- **Participantes:** Pueden guardar y compartir sus proyectos fácilmente, con una pequeña descripción e imágenes. Permite a nuevos participantes revisar proyectos ganadores anteriores para inspirarse.
- **Organizadores:** Tienen un registro limpio de ganadores y participantes históricos, lo que facilita el archivo de resultados y la preparación de resúmenes de cada edición.
- **Comunidad:** Facilita conocer los proyectos más innovadores desarrollados en el evento. Al ser público, cualquier persona puede ver y explorar los proyectos, incentivando la colaboración y aprendizaje.

Este repositorio añade valor como un recurso de largo plazo, reutilizable año tras año. Se alinea con el hackathon al usarse para el propio evento y como plataforma de demostración de tecnologías web modernas (frontend, autenticación, nube).

## 4. Requisitos funcionales

- **Registro y autenticación:** Los usuarios se registran/inician sesión usando **Clerk** (OAuth/Email). Cada cuenta representa a un participante.
- **Perfil de usuario:** Opcionalmente, almacenar nombre, foto de perfil, bio. (No crítico, pero mejora experiencia).
- **Creación de proyectos:** Un usuario autenticado puede crear múltiples proyectos. Cada proyecto incluye:
  - Título (string)
  - Descripción breve (text)
  - URL al repositorio/demo (string, validación de URL)
  - Hasta 3 imágenes (formatos WebP únicamente; tamaño limitado)
  - Año de la hackathon (número, p.ej. 2026)
  - Indicador “ganador” (booleano)
- **Edición/eliminación de proyectos:** El usuario puede editar o eliminar sus proyectos (campos y estado ganador).
- **Visualización pública:**
  - Página principal con presentación “cool” (banner del evento, links rápidos).
  - Sección “Proyectos por año”: lista de años (por ejemplo, 2025, 2026), cada uno lleva a lista de proyectos de ese año.
  - Dentro de un año, se muestra listado de proyectos (p. ej. como tarjetas con imagen, título, autor) y se resaltan aquellos marcados como ganadores.
  - Sección “Proyectos Ganadores”: lista global de proyectos ganadores organizados por año.
  - Página de detalle de proyecto: muestra toda la info (imágenes, descripción, enlace, autor).
- **Validaciones:** Solo formatos WebP para imágenes. URL válida. Los campos requeridos (título, descripción, año).
- **Búsqueda/filtrado:** Posibilidad de buscar proyectos por palabra clave o filtrar por participante.
- **Roles:** Un admin o el organizador marcara a mano quiénes son ganadores, en la bbdd habra una columna donde se podra poner un 1, 2, o 3 segun la posicion que haya quedado, default, sera 0, tambien se confía en que el usuario marque su propio proyecto ganador. En el frontend habra un slider mostrando los ultimos tres premios ganadores.

## 5. Stack tecnológico

Se propone el siguiente stack, priorizando tecnologías conocidas (Vue, Laravel) y servicios adecuados:

| Tecnología / Servicio  | Tipo                       | Ventajas                                                                          | Desventajas                                                   |
| ---------------------- | -------------------------- | --------------------------------------------------------------------------------- | ------------------------------------------------------------- |
| **Vue.js**             | Frontend (SPA)             | Framework progresivo JS, componentes reutilizables, fácil integración con Laravel | Requiere configuración de compilación (Vite)                  |
| **Tailwind CSS**       | Estilos                    | CSS utility-first para diseño rápido y responsivo                                 | Curva de aprendizaje inicial si no se conoce                  |
| **Laravel (PHP)**      | Backend (API)              | Framework maduro, incluye ORM (Eloquent) y autenticación básica                   | PHP no es específicamente para IA/ML, pero aquí no hace falta |
| **Clerk**              | Autenticación OAuth        | Implementa login/regístro de usuarios con 0 código backend                        | Requiere credenciales/claves externas                         |
| **MySQL / SQLite**     | Base de datos              | Eloquent maneja migraciones fácilmente                                            | Por escala no crítico (pequeño número de usuarios)            |
| **Storage (CubePath)** | Almacenamiento de imágenes | VPS de CubePath (nanoservers) puede alojar archivos WebP                          | Posible límite de disco (considerar S3 si fuera mayor escala) |
|                        |

- **Frontend Vue:** Consume APIs REST de Laravel. Gestiona formularios de registro (Clerk), creación de proyectos (subida de imágenes), y muestra los listados.
- **Backend Laravel:** Expone rutas REST seguras. Ejemplos: `POST /api/projects`, `GET /api/projects?year=2026`, `PUT /api/projects/{id}`, etc. Usa Middleware para verificar JWT de Clerk.
- **Clerk:** Facilita creación de accounts: proporciona componentes listos para login/registro. Laravel valida tokens JWT de Clerk.
- **Base de datos:** Tablas mínimas: `users` (id, name, email), `projects` (id, user_id, title, description, url, year, winner (bool)). Eloquent se encarga de relaciones.
- **Almacenamiento WebP:** Laravel procesa la subida (por ejemplo con [Laravel File Storage](https://laravel.com/docs/filesystem)). Validar que solo se acepten archivos con mime `image/webp` y tamaño razonable (<2MB cada uno). Guardar con nombres únicos (por ejemplo UUID).

## 6. Arquitectura y flujos de API

La arquitectura es cliente-servidor. A continuación se detallan los flujos principales:

```mermaid
flowchart LR
  subgraph Cliente
    Navegador[Usuario (Navegador)]
  end
  subgraph Infra
    Frontend[Vue.js App]
    Backend[Laravel API]
    AuthClerk[Clerk (Auth Service)]
    DB[(Base de datos SQL)]
    Storage[(Almacenamiento de Imágenes)]
  end
  Navegador --> Frontend
  Frontend --> AuthClerk
  AuthClerk -- (token) --> Frontend
  Frontend -->|JSON REST| Backend
  Backend --> DB
  Backend --> Storage
```

1. **Registro/Login:** El usuario usa el componente de Clerk en Vue, que maneja el flujo de autenticación y devuelve un token JWT.
2. **Creación de proyecto:** El frontend envía `POST /api/projects` con payload `{titulo, descripcion, url, year, ganador, images[]}`. El backend valida el JWT (middleware) y procesa: guarda texto en DB (`projects`), procesa y almacena imágenes en `Storage`, asociándolas al proyecto.
3. **Listado de proyectos:** El frontend pide `GET /api/projects?year=2026`. Backend devuelve JSON con proyectos filtrados (puede incluir datos de usuario como nombre).
4. **Detalle proyecto:** `GET /api/projects/{id}` muestra la información completa.
5. **Lista de ganadores:** `GET /api/projects?winner=1` devuelve solo proyectos marcados como ganadores.
6. **Edición y eliminación:** Opcionalmente, rutas `PUT /api/projects/{id}` y `DELETE /api/projects/{id}`, restringidas al autor.

Todos los endpoints deben devolver códigos HTTP apropiados (200 OK, 201 Created, 400 Bad Request, 403 Forbidden si no es dueño, etc.). Se usará autenticación via Bearer token.

## 10. Riesgos, mitigaciones y métricas de éxito

- **Formato de imágenes obligatorio WebP:** Riesgo: usuarios suben JPG. _Mitigación:_ Validar archivos en frontend y backend; ofrecer conversión automática (p.ej. con librería `imagewebp`).
- **Autenticación fallida:** Riesgo: integración con Clerk compleja. _Mitigación:_ Seguir la documentación oficial, y tener un plan B con Laravel Breeze para registro manual si falla.
- **Escalabilidad:** Pocos usuarios en hackathon, pero CubePath VPS (nanoservers) tiene recursos limitados. _Mitigación:_ Optimizar imágenes (usar webp ayuda) y minimizar consultas innecesarias.
- **Uso inapropiado:** Proyectos ofensivos o spam. _Mitigación:_ Añadir moderación (un admin revisa proyectos antes de publicación) o requerir aprobación de organizador.

**Métricas de éxito:**

- _Funcionalidad:_ 100% de las funciones críticas operando (registro, creación/listado de proyectos, filtro de ganadores).
- _Usabilidad:_ Feedback positivo de usuarios en pruebas (está clara la navegación y upload de proyectos).
- _Estabilidad:_ Despliegue activo 24/7; ningún fallo crítico durante revisión.
- _Documentación:_ Cumplimiento de checklist oficial (README con demo y capturas, etc.)【76†L317-L319】.

## 11. Entregables y checklist final

- **Repositorio GitHub:** Código completo (frontend y backend), commits claros.
- **README.md:** Con descripción del proyecto, link a demo en CubePath, capturas/GIFs de la app y uso de CubePath【76†L317-L319】.
- **Despliegue en CubePath:** URL pública funcionando.
- **Tests automatizados:** Al menos tests unitarios en backend.
- **Demo/Video/Slides:** Presentación y video de 2-3 min mostrando uso de la app.
- **Otros:** , documentos (diagrama de arquitectura).

**Checklist:**

- [ ] README con todos los elementos requeridos (demo link, screenshots)【76†L317-L319】.
- [ ] Registro y login con Clerk funcionando.
- [ ] Creación de proyecto con campos completos e imágenes WebP.
- [ ] Listado de proyectos por año operando, con filtro de ganadores.
- [ ] Visualización en frontend de todos los datos ingresados.
- [ ] Pruebas unitarias pasando y despliegue exitoso en CubePath.
- [ ] Slides y video listos para la presentación.

## Importante

Este repositorio es publico, hay que poner variables de entorno e impedir que se suban a github

Para el slider o el frontend puedes mirar los estilos, animaciones, tarjetas y demas componentes en esta url https://github.com/jbortweb/componentGallery

Quiero que haya un header con el titulo hackmidu y un footer con mis datos como creador jbortweb, que abra un modal y muestre mi enlace de linkedin, mi correo y mi github y mi portafolio

- https://www.linkedin.com/in/jordi-bort/
- jbortweb@gmail.com
- https://github.com/jbortweb
- https://portafoliojbortweb.netlify.app/

El proyecto se subira a https://my.cubepath.com/

Este plan detallado cubre todos los aspectos técnicos y estructurales: desde la definición de datos, arquitectura cliente-servidor, rutas de API, hasta la planificación de tareas diarias. Siguiendo este roadmap, el equipo podrá implementar un MVP completo que sirva como portal oficial de proyectos de la hackathon, cumpliendo los requisitos funcionales y de calidad esperados.
