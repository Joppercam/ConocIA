# Roadmap: Transformación ConocIA → Plataforma de Divulgación en IA
## Objetivo: Postulación Concurso Nacional Ciencia Pública 2027 (hasta $50.000.000 CLP)

---

## Estado general

| Fase | Descripción | Estado |
|------|-------------|--------|
| 1 | Identidad y branding | ✅ Completada |
| 2 | Página Quiénes Somos | ✅ Completada |
| 3 | Página Impacto | ✅ Completada |
| 4 | Reestructuración de navegación + placeholders | ✅ Completada |
| 5 | Sistema de niveles de complejidad | ⚠️ Parcial (solo Papers) |
| 6 | Footer actualizado | ✅ Completada |
| 7 | Accesibilidad WCAG 2.1 básico | ⚠️ Parcial (skip nav + lang + aria-label) |
| 8 | Mejoras al home | ✅ Completada |

---

## FASE 1 — Identidad y Branding ✅

**Completada.** Verificada en producción (conocia.cl).

- `<title>`: ConocIA - Plataforma de Divulgación en IA
- `og:site_name`: ConocIA - Plataforma de Divulgación en IA
- `meta description`: Plataforma chilena de divulgación, educación y alfabetización en inteligencia artificial...
- `APP_NAME` actualizado en Laravel Cloud

---

## FASE 2 — Página Quiénes Somos ✅

**Completada.**

- Ruta: `/quienes-somos` → `resources/views/about.blade.php`
- Alias `/acerca-de` redirige a `/quienes-somos` (301)
- Enlace en footer sección "Institucional"
- Enlace "Línea Editorial" como ancla `#linea-editorial`

**Pendiente menor:** Completar sección de equipo con nombres y bios reales.

---

## FASE 3 — Página Impacto ✅

**Completada.**

- Ruta: `/impacto` → `ImpactController` → `resources/views/impact/index.blade.php`
- Métricas dinámicas desde BD
- Enlace en menú principal y footer

---

## FASE 4 — Reestructuración de Navegación ✅

**Completada.**

Nueva estructura de menú implementada:
- Aprende (Conceptos IA, IA para Todos*, Glosario*)
- Investiga (Papers, Estado del Arte, Análisis de Fondo, Investigación)
- IA en Chile (Noticias locales, Mapa del Ecosistema*, Observatorio Regulación*)
- Actualidad (Noticias, Columnas)
- Multimedia (ConocIA Radio, ConocIA TV)
- Impacto

`* = página placeholder con ComingSoonController`

Páginas placeholder implementadas: `ia-para-todos`, `glosario`, `ecosistema`, `regulacion`
Ruta: `/proximamente/{slug}`

---

## FASE 5 — Sistema de Niveles de Complejidad ⚠️

**Parcialmente completada.**

| Sección | difficulty_level en BD | Badge en vista |
|---------|------------------------|----------------|
| Papers | ✅ | ✅ |
| Noticias | ❌ | ❌ |
| Investigación | ❌ | ❌ |
| Conceptos IA | ❌ | ❌ |
| Columnas | ❌ | ❌ |

**Pendiente:**
- [ ] Migración para agregar `difficulty_level` a tablas: `news`, `research_articles`, `concepts`, `columns`
- [ ] Valor por defecto: `intermedio`
- [ ] Badge visual en cards de Noticias, Investigación, Conceptos
- [ ] Filtro por nivel en páginas de listado (Noticias, Investigación)
- [ ] Estandarizar estilos CSS de badge (ya existe en Papers, reutilizar)

---

## FASE 6 — Footer Actualizado ✅

**Completada.**

Estructura implementada:
- ConocIA (logo + descripción nueva)
- Explora (con enlace a Impacto)
- Institucional (Quiénes Somos, Línea Editorial, Enviar Investigación)
- Legal
- Newsletter + redes sociales

---

## FASE 7 — Accesibilidad WCAG 2.1 ⚠️

**Parcialmente completada.**

| Item | Estado |
|------|--------|
| `lang="es-CL"` en `<html>` | ✅ (tiene `lang="es"`, ajustar a `es-CL`) |
| Skip navigation link | ❌ |
| `<main id="main-content" role="main">` | ❌ |
| `alt` en todas las imágenes | ⚠️ (revisar) |
| Labels en formularios | ⚠️ (revisar) |
| Estructura semántica header/nav/main/footer | ⚠️ (revisar) |
| Aria labels en elementos interactivos | ⚠️ (revisar) |
| Focus visible en elementos interactivos | ⚠️ (revisar) |

**Pendiente:**
- [ ] Agregar skip navigation antes del `<body>`
- [ ] Envolver contenido principal en `<main id="main-content" role="main">`
- [ ] Ajustar `lang="es"` a `lang="es-CL"`
- [ ] Audit completo de imágenes sin `alt`
- [ ] Verificar contraste 4.5:1 en texto principal

---

## FASE 8 — Mejoras al Home ⚠️

**Parcialmente completada.**

| Item | Estado |
|------|--------|
| Hero de noticias (grid editorial) | ✅ |
| Breaking news ticker | ✅ |
| Topic navigation bar | ✅ |
| Banner institucional con CTA | ❌ |
| Barra de estadísticas dinámica | ❌ |

**Pendiente:**
- [ ] Banner institucional encima del hero:
  - Título: "ConocIA — Divulgación en Inteligencia Artificial"
  - Subtítulo: "Explicamos la IA para que todos la entiendan"
  - CTAs: [Explorar contenido] [Quiénes somos]
- [ ] Barra de estadísticas dinámicas (consulta a BD):
  - Notas IA en Chile (163+)
  - Papers explicados
  - Investigaciones originales
  - Campos cubiertos en Estado del Arte
  - Animación de conteo al hacer scroll

---

## Notas generales

### Lo que NO tocar
- Lógica de ConocIA Papers (arXiv)
- ConocIA Radio (generación de audio)
- Sistema de autenticación de usuarios
- Sistema de guardados
- Rutas existentes (solo agregar)
- Stack tecnológico (Laravel, MySQL, Redis)
- Migraciones destructivas

### Convenciones del proyecto
- Laravel 12, PHP 8.4, Bootstrap 5, Font Awesome 6
- Blade templates (sin Livewire ni Alpine en el frontend principal)
- Colores: `--primary-color: #38b6ff`, `--secondary-color: #2a2a72`, `--dark-bg: #121212`
- CSS vars definidas en `resources/views/layouts/app.blade.php`

### Verificación post-cambios
Después de cada fase:
- [ ] El sitio carga sin errores
- [ ] `php artisan route:list` sin errores
- [ ] Meta tags correctos (ver código fuente)
- [ ] Menú funciona en móvil y desktop
- [ ] No hay rutas 404 nuevas
