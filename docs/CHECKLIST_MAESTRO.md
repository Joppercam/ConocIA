# CONOCIA.CL — CHECKLIST MAESTRO DE PENDIENTES
# Estado del proyecto: En transformación hacia plataforma de divulgación
# Objetivo final: Postulación Concurso Nacional Ciencia Pública 2027
# Última actualización: 07 de junio de 2026

---

## INSTRUCCIONES PARA CLAUDE CODE
# Este archivo es el registro central de tareas del proyecto ConocIA.
# - Marcar [x] cuando una tarea esté completada
# - Agregar fecha de completado entre paréntesis: [x] Tarea (completado: 2026-06-01)
# - No eliminar tareas completadas, solo marcarlas
# - Agregar nuevas tareas al final de cada sección si surgen
# - Este archivo debe actualizarse cada vez que se complete una tarea

---

## 1. FORMALIZACIÓN LEGAL (URGENTE — SEMANA 1)

### 1.1 Constitución SpA
- [x] Entrar a empresasenundia.cl con ClaveÚnica (completado: 2026-06-07)
- [x] Constituir "ConocIA SpA" (completado: 2026-06-07)
  - RUT: 78.441.343-8
  - CVE certificado: ACH4xfWFKjnQ
  - Fecha actuación: 07-06-2026
- [x] Descargar y guardar certificado de vigencia PDF (completado: 2026-06-07)

### 1.2 Inicio de actividades SII
- [x] Obtener clave tributaria empresa (completado: 2026-06-07)
- [x] Seleccionar opción "Emitir facturas" (completado: 2026-06-07)
- [x] Seleccionar actividades económicas (completado: 2026-06-07):
  - 631200 Portales web
  - 591300 Distribución audiovisual
  - 620900 Otras actividades TI
- [ ] Adjuntar documentos pendientes para completar trámite:
  - [ ] Certificado de constitución SpA (ya disponible, CVE: ACH4xfWFKjnQ)
  - [ ] Declaración de autorización de domicilio firmada por pareja (Quilicura, rol 079-01070-016)
- [ ] Confirmar aprobación del SII (1-5 días hábiles)

### 1.3 Cuenta bancaria empresarial (SEMANA 2-3)
- [ ] Elegir banco (Banco Estado / BCI / Banco de Chile / Santander)
- [ ] Reunir documentos:
  - [ ] Certificado de vigencia de la empresa
  - [ ] RUT de la empresa
  - [ ] Cédula de identidad del representante legal
  - [ ] Inicio de actividades del SII
- [ ] Abrir cuenta corriente empresarial
- [ ] Anotar número de cuenta: _______________

### 1.4 Certificado digital de facturación (SEMANA 3-4, POSTERGABLE)
- [ ] Comprar certificado digital (Acepta / E-Sign / E-Certchile)
- [ ] Centralizar certificado en portal del SII
- [ ] Verificar que puede emitir boletas y facturas electrónicas

---

## 2. TRANSFORMACIÓN DEL SITIO WEB (JUNIO - JULIO 2026)

### 2.1 Cambios de identidad y branding (Fase 1 del documento de instrucciones)
- [x] Cambiar título: "ConocIA - Portal de Noticias" → "ConocIA - Plataforma de Divulgación en IA" (completado: 2026-05-23)
- [x] Cambiar meta description global (completado: 2026-05-23)
- [x] Actualizar Open Graph tags (og:site_name, og:title, og:description) (completado: 2026-05-23)
- [x] Actualizar Twitter Card tags (completado: 2026-05-23)
- [x] Cambiar eslogan: "El futuro del conocimiento..." → "Democratizando el conocimiento en IA..." (completado: 2026-05-23)
- [x] Actualizar keywords con términos de divulgación científica (completado: 2026-05-23)
- [x] Actualizar footer con nueva descripción (completado: 2026-05-23)
- [x] Verificar cambios en producción con `curl` o inspeccionando código fuente (completado: 2026-05-23)
- [x] Limpiar caché de Laravel: `php artisan optimize:clear` (completado: 2026-05-23)

### 2.2 Página "Quiénes Somos" (Fase 2)
- [x] Crear controlador AboutController (completado: 2026-05-23)
- [x] Crear ruta /quienes-somos (completado: 2026-05-23)
- [x] Crear vista con secciones: misión, visión, línea editorial, lo que hacemos, equipo, historia, contacto (completado: 2026-05-23)
- [x] Agregar enlace en footer (completado: 2026-05-23)
- [x] Verificar en producción (completado: 2026-05-23)
- [ ] Completar sección de equipo con nombres y bios reales

### 2.3 Página "Impacto" (Fase 3)
- [x] Crear controlador ImpactController con consultas a BD (completado: 2026-05-23)
- [x] Crear ruta /impacto (completado: 2026-05-23)
- [x] Crear vista con métricas dinámicas (completado: 2026-05-23)
- [x] Agregar enlace en footer y menú (completado: 2026-05-23)
- [x] Verificar en producción (completado: 2026-05-23)

### 2.4 Reestructuración de navegación (Fase 4)
- [x] Rediseñar menú principal con ejes de divulgación (completado: 2026-05-23)
- [x] Crear páginas placeholder con badge "Próximamente" (completado: 2026-05-23)
- [x] Crear ComingSoonController genérico (completado: 2026-05-23)
- [x] Verificar navegación en móvil y desktop (completado: 2026-05-23)

### 2.5 Sistema de niveles de complejidad (Fase 5)
- [x] Crear migración para campo difficulty_level (completado: 2026-05-23)
- [x] Aplicar migración a tablas: news, researches, columns, conceptos_ia (completado: 2026-05-23)
- [x] Crear badge visual (Básico / Intermedio / Avanzado) (completado: 2026-05-23)
- [x] Agregar badge en cards de Noticias, Investigación, Conceptos (completado: 2026-05-23)
- [x] Agregar filtro por nivel en páginas de listado (Noticias, Investigación) (completado: 2026-06-07)
- [ ] Etiquetar contenido existente con niveles apropiados

### 2.6 Mejoras al footer (Fase 6)
- [x] Actualizar descripción del footer (completado: 2026-05-23)
- [x] Agregar sección "Institucional" (completado: 2026-05-23)
- [x] Agregar enlace a Impacto en sección Explora (completado: 2026-05-23)

### 2.7 Mejoras de accesibilidad WCAG 2.1 (Fase 7)
- [x] Agregar lang="es-CL" al tag html (completado: 2026-05-23)
- [x] Agregar skip navigation link (completado: 2026-05-23)
- [x] Agregar landmark main con id="main-content" (completado: 2026-05-23)
- [ ] Verificar alt texts en todas las imágenes
- [ ] Verificar contraste de colores (mínimo 4.5:1)
- [ ] Verificar labels en formularios
- [ ] Verificar navegación por teclado
- [ ] Verificar estructura semántica (header, nav, main, article, footer)

### 2.8 Actualización del home (Fase 8)
- [x] Agregar banner institucional compacto sobre el ticker de noticias (completado: 2026-05-23)
- [x] Slogan: "IA sin simplificar. Divulgación sin barreras." (completado: 2026-05-23)
- [x] Agregar barra de estadísticas con números dinámicos (completado: 2026-05-23)
- [x] Reordenar secciones: Profundiza y Papers antes que Noticias (completado: 2026-05-23)
- [x] Sección "IA en Chile" en columna derecha de noticias (completado: 2026-05-23)

---

## 3. CONTENIDO ESTRATÉGICO (JULIO - NOVIEMBRE 2026)

### 3.1 Mapa del Ecosistema IA en Chile
- [ ] Investigar y documentar actores del ecosistema (objetivo: 30-50)
- [ ] Universidades con programas/laboratorios de IA (CENIA, IDS-UDD, UTFSM, etc.)
- [ ] Startups chilenas que usan IA
- [ ] Centros de investigación
- [ ] Políticas públicas activas
- [ ] Desarrollar vista interactiva del mapa
- [ ] Crear ficha individual por actor

### 3.2 Enciclopedia de Conceptos IA
- [x] Catálogo expandido a 76 conceptos en GenerateConceptosIa (completado: 2026-05-23)
- [x] Scheduler genera 1 concepto/día laboral desde las 06:00 (completado: 2026-05-23)
- [ ] Objetivo: mínimo 30 conceptos publicados para postulación
- [ ] Agregar filtro por nivel de dificultad en /conceptos-ia

### 3.3 Primer módulo curso "IA para Todos"
- [ ] Definir estructura del módulo 1: "¿Qué es la IA?"
- [ ] Escribir contenido de las lecciones
- [ ] Crear elementos interactivos básicos
- [ ] Publicar como artículo educativo estructurado

### 3.4 Radar Regulatorio IA Chile
- [x] Migration + modelo RadarRegulatorio (completado: 2026-05-23)
- [x] Comando radar:generate con 5 hitos semilla (completado: 2026-05-23)
- [x] Vistas index (timeline) y show (ficha técnica) (completado: 2026-05-23)
- [x] Scheduler: jueves 10:00 (completado: 2026-05-23)
- [ ] Ejecutar php artisan radar:generate en Laravel Cloud para poblar hitos iniciales
- [ ] Agregar enlace al Radar en menú "IA en Chile"
- [ ] Agregar widget del Radar en home o en sección IA en Chile

### 3.5 Caso Real en Chile (PENDIENTE)
- [ ] Diseñar modelo CasoRealChile (organización, sector, uso_ia, impacto, fuente)
- [ ] Crear migration + modelo
- [ ] Crear comando GenerateCasoRealChile
- [ ] Crear controller + vistas (index + show)
- [ ] Agregar rutas y scheduler (semanal)

### 3.6 Laboratorio Interactivo (2-3 demos)
- [ ] Demo 1: Clasificador de sentimiento
- [ ] Demo 2: Visualización interactiva de red neuronal simple
- [ ] Demo 3: Quiz "¿Cuánto sabes de IA?" con resultados educativos
- [ ] Integrar demos en sección /laboratorio

### 3.7 Análisis de Fondo editorial
- [x] Catálogo expandido a 30 temas (Chile & Latam + divulgación ciudadana) (completado: 2026-05-23)
- [x] Scheduler genera análisis cada miércoles 14:00 (completado: 2026-05-23)

---

## 4. ACREDITACIÓN Y ALIANZAS (JULIO - DICIEMBRE 2026)

### 4.1 Registro DIBAM
- [ ] Preparar documentación (línea editorial publicada, capturas históricas, datos PJ)
- [ ] Solicitar inscripción en Catastro de Medios Digitales
- [ ] Obtener certificado de acreditación DIBAM

### 4.2 Alianzas institucionales
- [ ] Contactar CENIA (U de Chile) — área de divulgación
- [ ] Contactar Facultad FCFM (U de Chile) — comunicaciones
- [ ] Contactar UTFSM — vinculación con el medio
- [ ] Contactar universidades regionales
- [ ] Objetivo: obtener al menos 2 cartas de apoyo/interés
- [ ] Carta 1: _______________
- [ ] Carta 2: _______________

### 4.3 Consulta a Subsecretaría CTCI
- [ ] Redactar consulta formal (antigüedad PJ, postulación persona natural, excepciones)
- [ ] Enviar consulta por canal oficial
- [ ] Registrar respuesta: _______________
- [ ] Ajustar timeline según respuesta

---

## 5. PREPARACIÓN DE POSTULACIÓN (DICIEMBRE 2026 - FEBRERO 2027)

### 5.1 Documentación
- [ ] Compilar métricas de impacto verificables
- [ ] Recopilar capturas históricas del sitio (Wayback Machine)
- [ ] Preparar portafolio de contenido publicado
- [ ] Recopilar testimonios de usuarios o instituciones

### 5.2 Formulario Ciencia Pública
- [ ] Obtener bases de la convocatoria 2027 cuando se publiquen
- [ ] Redactar proyecto según formato requerido
- [ ] Preparar presupuesto detallado ($50M CLP):
  - [ ] Personal: $25.000.000 (50%)
  - [ ] Infraestructura: $8.000.000 (16%)
  - [ ] Producción multimedia: $7.000.000 (14%)
  - [ ] Desarrollo Lab + curso: $6.000.000 (12%)
  - [ ] Difusión y alianzas: $4.000.000 (8%)
- [ ] Adjuntar cartas de apoyo institucional
- [ ] Revisión final del formulario

### 5.3 Plan B: Fondo de Medios 2027
- [ ] Verificar fecha de convocatoria (generalmente marzo)
- [ ] Preparar postulación paralela (hasta $3.500.000 CLP)
- [ ] Enfocar en: "Cobertura del ecosistema IA en Chile"

---

## 6. REGISTRO DE HITOS Y DECISIONES

### Hitos completados
| Fecha | Hito | Detalle |
|---|---|---|
| 2026-05-23 | Documento de transformación creado | Propuesta completa con 8 fases |
| 2026-05-23 | Análisis de fondos públicos completado | Ciencia Pública como objetivo principal |
| 2026-05-23 | Fases 1-8 implementadas | Branding, páginas, nav, accesibilidad, home, niveles |
| 2026-05-23 | Catálogos de contenido expandidos | AnalisisFondo: 30 temas; ConceptosIA: 76 conceptos |
| 2026-05-23 | Radar Regulatorio Chile creado | /radar-regulatorio con timeline y ficha técnica |
| 2026-06-07 | ConocIA SpA constituida | RUT 78.441.343-8, CVE ACH4xfWFKjnQ |
| 2026-06-07 | Inicio de actividades SII iniciado | Pendiente adjuntar documentos de domicilio |

### Decisiones pendientes
| Tema | Opciones | Decisión | Fecha |
|---|---|---|---|
| Nombre empresa | ConocIA SpA / ConocIA Medios SpA | ConocIA SpA | 2026-06-07 |
| Régimen tributario | Pro PyME General / Pro PyME Transparente | Por definir con contador | |
| Banco | Banco Estado / BCI / Banco de Chile | Por definir | |
| Antigüedad PJ | Esperar respuesta CTCI | Pendiente consulta | |

### Contactos importantes
| Entidad | Contacto | Email/Teléfono | Estado |
|---|---|---|---|
| Subsecretaría CTCI | | | Por contactar |
| CENIA U de Chile | | | Por contactar |
| DIBAM | | | Por contactar |
| Contador/a | | | Por definir |

---

## NOTAS
# -
# -
