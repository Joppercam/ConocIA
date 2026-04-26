# Prompts operativos para crear contenido en ConocIA

Fecha de referencia: 2026-04-25

Este documento define frases/prompts que Juan Pablo puede mencionar para que Codex ejecute o prepare contenido especifico dentro del proyecto ConocIA.

La idea es usar prompts cortos, consistentes y accionables. Cuando menciones uno, Codex debe:

1. Identificar el tipo de contenido.
2. Revisar el codigo/comando/vista relacionada.
3. Decidir si corresponde generar texto, ejecutar un comando Artisan, crear/editar registros via codigo, o preparar una pieza para revision.
4. Evitar cambios productivos peligrosos sin validacion.
5. Informar que hizo, que archivo toco y como verificarlo.

Importante: como el aplicativo esta en produccion, cualquier prompt que implique publicar, modificar base de datos, ejecutar automatizaciones o desplegar debe tratarse con cautela.

## Regla transversal de autonomia

Todos los prompts funcionan por intencion, no por formulario.

Cuando Juan Pablo pida crear, actualizar, importar, optimizar o validar contenido, Codex debe asumir por defecto la responsabilidad de completar lo que falte:

- Buscar fuentes, datos, referencias o registros internos necesarios.
- En noticias, priorizar hechos del dia o de las ultimas 48 horas; solo usar temas mas antiguos si siguen siendo relevantes, si el usuario lo pide o si no hay suficientes fuentes recientes.
- Elegir categoria, tags, enfoque editorial, SEO, slug y estructura cuando aplique.
- Determinar el comando Artisan, modelo, controlador, vista o flujo administrativo relacionado.
- Proponer o ejecutar el camino mas seguro segun el ambiente.
- Pedir confirmacion solo si hay riesgo real: publicar en produccion, enviar emails, modificar muchos registros, ejecutar jobs costosos, borrar/archivar datos o desplegar.

Los campos como fuente, categoria, autor, cantidad, periodo, pais, URL, tono o fecha son restricciones opcionales. Si el usuario no los entrega, Codex debe inferirlos, investigarlos o proponerlos.

## 1. Formato general

Puedes pedirme contenido con una frase corta o con una estructura completa. Por defecto, si faltan datos, Codex debe investigarlos y proponerlos.

Forma corta:

```text
Codex, crear noticia sobre [tema].
Codex, preparar analisis de fondo sobre [tema].
Codex, crear concepto IA sobre [concepto].
Codex, optimizar SEO de las noticias prioritarias.
```

Forma completa, solo cuando quieras controlar o limitar detalles:

```text
Codex, usa el prompt: [NOMBRE_DEL_PROMPT]
Tema: [tema especifico]
Objetivo: [para que lo necesito]
Publicar ahora: [si/no]
Notas opcionales: [tono, fuentes sugeridas, restricciones, enlaces, fecha, audiencia, limites]
```

Ejemplo:

```text
Codex, usa el prompt: CREAR_ANALISIS_FONDO
Tema: agentes de IA en empresas chilenas
Objetivo: articulo editorial largo para Profundiza
Publicar ahora: no
Notas: tono claro, tecnico pero accesible, con foco en oportunidades y riesgos
```

## 2. Prompt: CREAR_NOTICIA

Uso:

Forma recomendada:

```text
Codex, crear noticia sobre [tema].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_NOTICIA
Tema:
Fuente o enlace sugerido:
Categoria sugerida:
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Buscar fuentes actuales y confiables sobre el tema, idealmente publicadas hoy o en las ultimas 48 horas.
- Seleccionar la fuente principal y fuentes de apoyo si aplica.
- Extraer los hechos relevantes sin copiar texto protegido.
- Procesar la informacion en formato editorial ConocIA.
- Elegir categoria, tags, slug, imagen sugerida o estrategia de imagen.
- Incluir titulo, bajada, resumen, cuerpo, SEO title, meta description y keywords.
- Preparar el contenido para insercion o insertarlo si el usuario lo pidio explicitamente y el entorno es seguro.
- Si se pide publicar, confirmar que el flujo no rompa produccion y usar el mecanismo existente.

Reglas:

- No inventar hechos.
- Si el tema es actual, buscar y verificar fuentes.
- Descartar noticias antiguas si existen alternativas equivalentes mas recientes.
- Priorizar fuentes primarias, comunicados oficiales, blogs tecnicos oficiales, medios confiables y papers cuando corresponda.
- La fuente, categoria y tags son responsabilidad de Codex salvo que el usuario los entregue como restriccion.
- Mantener tono periodistico y claro.
- Evitar contenido demasiado breve.
- Si no hay fuentes suficientes, informar el limite y proponer esperar, cambiar el enfoque o crear una nota de contexto no noticiosa.

## 3. Prompt: CREAR_NOTICIA_CHILE

Uso:

Forma recomendada:

```text
Codex, crear noticia Chile sobre [tema].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_NOTICIA_CHILE
Tema:
Fuente sugerida:
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Crear contenido para la seccion IA en Chile.
- Buscar fuentes chilenas o fuentes internacionales con impacto directo en Chile.
- Priorizar impacto local, actores chilenos, regulacion, startups, universidades, empresas o sector publico.
- Elegir categoria, tags, SEO y enfoque editorial.
- Incluir contexto para lectores no especialistas.

## 4. Prompt: CREAR_ANALISIS_FONDO

Uso:

Forma recomendada:

```text
Codex, preparar analisis de fondo sobre [tema].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_ANALISIS_FONDO
Tema:
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Investigar contexto, fuentes y tendencias relevantes.
- Preparar un analisis editorial profundo para `/analisis`.
- Puede apoyarse en el comando `analisis:generate` si corresponde.
- Elegir estructura, SEO, tags y enfoque editorial.
- Debe incluir tesis, contexto, implicancias, riesgos, oportunidades y cierre.

Estructura sugerida:

- Titulo.
- Bajada.
- Resumen ejecutivo.
- Contexto.
- Analisis principal.
- Impacto para Chile/LatAm si aplica.
- Riesgos.
- Que mirar despues.
- SEO.

## 5. Prompt: CREAR_CONCEPTO_IA

Uso:

Forma recomendada:

```text
Codex, crear concepto IA sobre [concepto].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_CONCEPTO_IA
Concepto:
Nivel: basico/intermedio/avanzado
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Investigar definicion, usos, ejemplos y conceptos relacionados.
- Crear una pagina tipo enciclopedia para `/conceptos-ia`.
- Explicar definicion, funcionamiento, ejemplos, ventajas, limites, terminos relacionados y preguntas frecuentes.
- Elegir nivel, SEO, tags y estructura si no fueron indicados.
- Puede apoyarse en `conceptos:generate --count=1` si el flujo automatico es preferible.

## 6. Prompt: CREAR_ESTADO_ARTE

Uso:

Forma recomendada:

```text
Codex, crear estado del arte sobre [subcampo].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_ESTADO_ARTE
Subcampo:
Periodo:
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Investigar avances recientes, papers, modelos, herramientas y debates.
- Crear o preparar un digest para `/estado-del-arte`.
- Puede apoyarse en `digest:generate --all`.
- Elegir periodo, subcampo, fuentes y SEO si no fueron indicados.
- Debe resumir avances, papers, modelos, herramientas, debates y tendencias.

## 7. Prompt: CREAR_PAPER_EDITORIAL

Uso:

Forma recomendada:

```text
Codex, crear paper editorial sobre [tema o paper].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_PAPER_EDITORIAL
Paper o arXiv URL sugerido:
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Buscar paper relevante si el usuario no entrega uno.
- Crear contenido para `/papers`.
- Explicar el paper en lenguaje editorial.
- Elegir categoria, tags, SEO y enfoque.
- Incluir aporte, metodologia, resultados, limites, relevancia y enlaces.
- No afirmar mas que lo que el paper sostiene.

## 8. Prompt: CREAR_COLUMNA

Uso:

Forma recomendada:

```text
Codex, crear columna sobre [tema].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_COLUMNA
Tema:
Autor sugerido:
Tono sugerido:
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Investigar contexto y definir tesis editorial.
- Preparar columna de opinion para `/columnas`.
- Elegir autor, tono, categoria, tags y SEO si no fueron indicados.
- Mantener voz autoral, tesis clara y estructura argumentativa.
- Separar hechos de opinion.

## 9. Prompt: CREAR_BRIEFING_DIARIO

Uso:

Forma recomendada:

```text
Codex, generar briefing diario.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_BRIEFING_DIARIO
Fecha:
Forzar regeneracion: si/no
Notas:
```

Resultado esperado:

- Generar o revisar el briefing diario.
- Usar la fecha actual si no se indica otra.
- Usar `briefing:generate` o `briefing:generate --force` segun corresponda.
- Verificar que quede disponible en `/api/briefing/today` y `/radio`.

Regla:

- No regenerar en produccion sin entender si reemplazara contenido ya publicado.

## 10. Prompt: CREAR_NEWSLETTER

Uso:

Forma recomendada:

```text
Codex, preparar newsletter semanal.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_NEWSLETTER
Periodo:
Cantidad de noticias:
Incluir research: si/no
Incluir columnas: si/no
Enviar ahora: si/no
Notas:
```

Resultado esperado:

- Preparar o enviar newsletter.
- Seleccionar periodo, contenidos, asunto, orden editorial y CTA si no fueron indicados.
- Usar `newsletter:send` si se confirma envio.
- Si no se envia, preparar asunto, introduccion, seleccion de contenidos y CTA.

Regla:

- Envio real requiere confirmacion explicita.

## 11. Prompt: CREAR_SCRIPT_TIKTOK

Uso:

Forma recomendada:

```text
Codex, crear guion TikTok sobre [tema o noticia].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_SCRIPT_TIKTOK
Noticia o tema:
Duracion sugerida:
Estilo sugerido:
Publicar ahora: no
Notas:
```

Resultado esperado:

- Buscar o elegir una noticia relevante si no se entrega una.
- Crear guion para TikTok asociado a una noticia o tema.
- Puede usar `tiktok:generate-scripts --count=...` si se busca generar desde noticias recientes.
- Elegir duracion, estilo, hook y hashtags si no fueron indicados.
- Preparar hook, desarrollo, cierre, caption, hashtags y texto en pantalla.

Regla:

- El prompt no publica en TikTok. Solo prepara contenido para revision.

## 12. Prompt: CREAR_KIT_TIKTOK

Uso:

Forma recomendada:

```text
Codex, crear kit TikTok para el ultimo script pendiente.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_KIT_TIKTOK
ID del script sugerido:
Notas:
```

Resultado esperado:

- Buscar script pendiente o relevante si no se entrega ID.
- Generar kit para un script ya existente si el controlador/comando lo permite.
- Revisar rutas admin relacionadas con `generate-kit` y `download-kit`.
- No asumir que hay audio/zip disponible sin verificar.

## 13. Prompt: CREAR_VIDEO_RESUMEN

Uso:

Forma recomendada:

```text
Codex, crear resumen IA para videos pendientes.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_VIDEO_RESUMEN
Video ID o URL sugerido:
Forzar regeneracion: si/no
Notas:
```

Resultado esperado:

- Generar resumen IA y keywords para video.
- Elegir videos pendientes si no se entrega ID o URL.
- Usar `videos:generate-summaries --limit=...` o `--force` segun corresponda.
- Verificar campos `ai_summary` y `ai_keywords`.

## 14. Prompt: IMPORTAR_VIDEOS_YOUTUBE

Uso:

Forma recomendada:

```text
Codex, importar videos de YouTube sobre IA.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: IMPORTAR_VIDEOS_YOUTUBE
Tema o query:
Cantidad:
Generar resumenes: si/no
Notas:
```

Resultado esperado:

- Importar videos relevantes de YouTube si `YOUTUBE_API_KEY` esta configurado.
- Elegir query, cantidad prudente y categorias si no fueron indicadas.
- Usar `videos:fetch-youtube`.
- Opcionalmente ejecutar `videos:generate-summaries`.

## 15. Prompt: CREAR_STARTUP_SEMANA

Uso:

Forma recomendada:

```text
Codex, crear startup de la semana.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_STARTUP_SEMANA
Startup:
Fuente sugerida:
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Buscar o seleccionar una startup relevante si no se entrega una.
- Crear o actualizar perfil de startup.
- Puede apoyarse en `startups:feature-weekly` o `startups:fetch`.
- Elegir fuente, categoria, tags y SEO si no fueron indicados.
- Incluir que hace, mercado, tecnologia, traccion, riesgos y relevancia.

## 16. Prompt: ACTUALIZAR_MODELOS_IA

Uso:

Forma recomendada:

```text
Codex, actualizar modelos IA.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: ACTUALIZAR_MODELOS_IA
Proveedor o modelo:
Notas:
```

Resultado esperado:

- Actualizar comparador de modelos IA.
- Puede usar `models:sync`.
- Elegir proveedores/modelos prioritarios si no se indican.
- Verificar informacion actual en fuentes oficiales.

## 17. Prompt: CREAR_AGENDA_EVENTOS

Uso:

Forma recomendada:

```text
Codex, crear agenda de eventos IA.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_AGENDA_EVENTOS
Pais o region:
Meses hacia adelante:
Cantidad:
Notas:
```

Resultado esperado:

- Importar o preparar eventos de IA.
- Elegir region, meses y cantidad prudente si no fueron indicados.
- Puede usar `events:fetch --months=... --limit=...`.
- Verificar fecha, modalidad, ubicacion y fuente.

## 18. Prompt: OPTIMIZAR_SEO_NOTICIA

Uso:

Forma recomendada:

```text
Codex, optimizar SEO de una noticia prioritaria.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: OPTIMIZAR_SEO_NOTICIA
URL o ID sugerido:
Objetivo:
Notas:
```

Resultado esperado:

- Elegir noticia prioritaria si no se entrega URL o ID.
- Revisar titulo, slug, bajada, meta description, schema, keywords y enlaces internos.
- Puede apoyarse en Search Console si hay metricas.
- No cambiar sentido editorial de la noticia.

## 19. Prompt: OPTIMIZAR_SEO_PRIORITARIO

Uso:

Forma recomendada:

```text
Codex, optimizar SEO prioritario.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: OPTIMIZAR_SEO_PRIORITARIO
Periodo Search Console:
Cantidad de URLs:
Aplicar cambios: si/no
Notas:
```

Resultado esperado:

- Usar metricas Search Console para detectar oportunidades.
- Elegir periodo, cantidad y URLs prioritarias si no fueron indicadas.
- Puede apoyarse en `seo:sync-search-console`, `seo:audit-search-console` y `content:optimize-priority-news-seo`.
- Si `Aplicar cambios: no`, entregar recomendaciones.

## 20. Prompt: GENERAR_SITEMAP

Uso:

Forma recomendada:

```text
Codex, generar sitemap.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: GENERAR_SITEMAP
Ambiente: local/produccion
Notas:
```

Resultado esperado:

- Revisar rutas sitemap.
- Detectar ambiente o preguntar solo si ejecutar en produccion es riesgoso.
- Ejecutar `sitemap:generate` si corresponde.
- Confirmar que rutas XML respondan.

## 21. Prompt: VALIDAR_CALIDAD_NOTICIAS

Uso:

Forma recomendada:

```text
Codex, validar calidad de noticias.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: VALIDAR_CALIDAD_NOTICIAS
Alcance:
Aplicar correcciones: si/no
Notas:
```

Resultado esperado:

- Usar `news:validate-quality` y/o `news:clean-content`.
- Elegir alcance prudente si no fue indicado.
- Detectar noticias incompletas, imagenes rotas, resumenes pobres o problemas SEO.
- Si no se aplican correcciones, entregar reporte.

## 22. Prompt: REPROCESAR_NOTICIAS_CORTAS

Uso:

Forma recomendada:

```text
Codex, reprocesar noticias cortas.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: REPROCESAR_NOTICIAS_CORTAS
Cantidad:
Publicar resultado: si/no
Notas:
```

Resultado esperado:

- Usar `news:reprocess-short`.
- Elegir cantidad baja y segura si no fue indicada.
- Expandir noticias demasiado breves con IA.
- Validar que no se inventen datos.

## 23. Prompt: ARCHIVAR_NOTICIAS

Uso:

Forma recomendada:

```text
Codex, archivar noticias antiguas.
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: ARCHIVAR_NOTICIAS
Dias:
Batch:
Ejecutar ahora: si/no
Notas:
```

Resultado esperado:

- Usar `news:archive`.
- Elegir umbrales por defecto del comando si no se indican.
- Mover noticias antiguas a historico segun reglas.
- Verificar conteos antes y despues si se ejecuta.

## 24. Prompt: CREAR_PACK_PROFUNDIZA

Uso:

Forma recomendada:

```text
Codex, crear pack Profundiza sobre [tema].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_PACK_PROFUNDIZA
Tema:
Componentes: conceptos/analisis/papers/estado-arte/columnas
Publicar ahora: si/no
Notas:
```

Resultado esperado:

- Investigar tema y proponer componentes si no fueron indicados.
- Crear un conjunto editorial coordinado para Profundiza.
- Puede apoyarse en comandos `content:publish-*` existentes si el pack corresponde.
- Mantener consistencia de enlaces internos, SEO y tono.

## 25. Prompt: CREAR_PROMPT_NUEVO

Uso:

Forma recomendada:

```text
Codex, documenta un nuevo prompt para [objetivo].
```

Forma con restricciones opcionales:

```text
Codex, usa el prompt: CREAR_PROMPT_NUEVO
Nombre:
Objetivo:
Modulo relacionado:
Entradas necesarias:
Resultado esperado:
Riesgos:
```

Resultado esperado:

- Agregar una nueva entrada a este documento.
- Inferir nombre, modulo, entradas y riesgos si no fueron indicados.
- Alinear el nuevo prompt con la arquitectura real del proyecto.
- Incluir reglas de seguridad para produccion.

## 26. Reglas editoriales base

Todo contenido debe:

- Ser claro, verificable y util.
- Separar hechos, opinion y prediccion.
- Evitar exageraciones comerciales.
- Evitar afirmar actualidad sin revisar fuente.
- Incluir SEO cuando vaya a publicarse.
- Mantener tono ConocIA: editorial, inteligente, accesible y sobrio.
- Considerar lector chileno/latinoamericano cuando el tema lo permita.

## 27. Reglas tecnicas base

Antes de ejecutar comandos que escriben datos:

- Revisar entorno actual (`APP_ENV`) si es posible.
- Confirmar si la accion impacta produccion.
- Preferir modo reporte/dry-run si existe.
- Ejecutar con limites bajos primero.
- Revisar logs despues.

Antes de tocar codigo:

- Revisar rutas, controlador, modelo y vista afectados.
- Mantener cambios pequenos y reversibles.
- Ejecutar pruebas relevantes si el entorno lo permite.
- No modificar `.env` con secretos.

## 28. Prompts rapidos

Puedes usar estas frases cortas:

- "Codex, crear noticia sobre [tema]."
- "Codex, preparar analisis de fondo sobre [tema]."
- "Codex, crear concepto IA para [concepto]."
- "Codex, genera briefing de hoy."
- "Codex, prepara newsletter semanal."
- "Codex, crea guion TikTok de esta noticia: [URL]."
- "Codex, optimiza SEO de esta URL: [URL]."
- "Codex, valida calidad de las ultimas noticias."
- "Codex, crea pack Profundiza sobre [tema]."
- "Codex, documenta un nuevo prompt para [objetivo]."

Cuando uses una frase corta, Codex debe inferir el prompt mas cercano y confirmar la accion si hay riesgo productivo.
