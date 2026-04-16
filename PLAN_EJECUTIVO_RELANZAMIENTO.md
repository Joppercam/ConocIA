# Plan ejecutivo de relanzamiento (4 semanas)

Fecha base: 21-02-2026
Proyecto: ConocIA
Objetivo: relanzar con estabilidad técnica + mejoras funcionales de alto impacto.

## 1) Objetivo del relanzamiento

- Recuperar operación estable del portal en local/staging.
- Reactivar integraciones clave (IA, ingesta, redes, newsletter).
- Entregar mejoras funcionales visibles para usuarios/admin.
- Llegar a salida con checklist de calidad y monitoreo básico.

## 2) Principios de ejecución

- Priorizar estabilidad antes de nuevas features.
- Avanzar por verticales cerradas (funciona end-to-end o no se libera).
- Reducir riesgo operativo (logs, colas, scheduler y fallback manual).
- Medir impacto con KPIs concretos desde la semana 2.

## 3) Roadmap por semanas

## Semana 1 — Estabilización y credenciales

### Meta
Dejar entorno y flujos críticos funcionando con datos reales.

### Entregables
- Credenciales y servicios de pago configurados y validados.
- Smoke test técnico completo en local.
- Flujo de publicación mínimo operativo (noticia -> revisión -> publicación).
- Lista de incidencias técnicas priorizadas (P1/P2/P3).

### Checklist técnico
- `make up`
- `make dev`
- `make check`
- Probar scheduler/colas sin errores críticos
- Validar conectividad a APIs pagadas

---

## Semana 2 — Funcionalidad core (contenido)

### Meta
Fortalecer el corazón editorial del portal.

### Entregables
- Mejora de panel/admin de noticias (usabilidad + confiabilidad).
- Mejoras en flujo de archivo/históricos y consistencia de estados.
- Revisión de rendimiento en listados y home.
- Tests mínimos de regresión para rutas críticas editoriales.

### KPI sugerido
- Tiempo de publicación editorial reducido.
- 0 errores bloqueantes en flujo de alta/edición/publicación.

---

## Semana 3 — Distribución y automatización

### Meta
Reactivar canales y automatizaciones de difusión.

### Entregables
- Flujo social operativo (cola + reintentos + trazabilidad).
- Flujo de newsletter validado de punta a punta.
- Flujo TikTok scripts en estado usable para operación.
- Dashboard simple de estado (qué se publicó, qué falló, por qué).

### KPI sugerido
- % de publicaciones sociales exitosas.
- % envíos newsletter exitosos.

---

## Semana 4 — Pulido y salida

### Meta
Cerrar calidad, performance y preparación de release.

### Entregables
- Corrección de bugs pendientes P1/P2.
- Checklist de release completo (funcional + técnico).
- Validación final en staging.
- Plan de salida y reversión.

### KPI sugerido
- Error rate aceptable en rutas críticas.
- Tiempo de respuesta en páginas clave dentro de objetivo.

## 4) Backlog inicial recomendado (prioridad)

### P1 (hacer primero)
- Estabilidad de flujos editoriales.
- Integraciones pagadas mínimas funcionando.
- Colas y scheduler sin fallos silenciosos.

### P2
- Usabilidad en panel admin.
- Trazabilidad de errores de publicación/redes.
- Optimización de consultas en home/listados.

### P3
- Mejoras visuales secundarias.
- Automatizaciones no críticas.

## 5) Riesgos y mitigación

- Dependencia de APIs de pago
  - Mitigación: fallback local, feature flags y validación temprana de cuotas.

- Regresiones por migraciones históricas
  - Mitigación: `migrate:fresh` en staging de prueba + smoke tests por vertical.

- Saturación de tareas en relanzamiento
  - Mitigación: congelar scope semanal, cerrar P1 antes de abrir P2.

## 6) Criterio de “listo para relanzar”

Se considera listo cuando:
- Entorno estable y reproducible (`make up`, `make dev`, `make check`).
- Flujos críticos pasan prueba end-to-end:
  - Publicación de noticias
  - Archivo histórico
  - Al menos 1 canal social
  - Newsletter
- Sin errores bloqueantes abiertos.
- Existe plan de rollback documentado.

## 7) Arranque recomendado al retomar

Día 1 de retorno:
1. Revisar `GUIA_RETORNO_RELANZAMIENTO.md`.
2. Levantar entorno y validar salud (`make up`, `make dev`, `make check`).
3. Confirmar credenciales pagadas.
4. Ejecutar smoke test funcional.
5. Congelar backlog de Semana 1 y comenzar ejecución.
