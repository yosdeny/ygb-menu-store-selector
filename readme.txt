=== YGBMenu Store Selector ===
Contributors: YGB
Tags: selector, dropdown, redirect, cookies, store selector, menu
Requires at least: 7.0
Tested up to: 7.1
Stable tag: 1.2.5
Requires PHP: 8.0
Tested PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Un selector desplegable de URLs para redirección con persistencia mediante cookies.

== Description ==

YGBMenu Store Selector es un plugin de WordPress que proporciona un selector desplegable elegante para redirigir usuarios a diferentes URLs, manteniendo su selección persistente mediante cookies.

**Características principales:**

* Selector desplegable con estilos personalizados
* Persistencia de selección mediante cookies (configurable)
* Redirección automática al seleccionar una opción
* Shortcode `[ygbmenu_selector]` para inserción fácil
* Widget personalizado para el sidebar
* Panel de administración completo
* Totalmente compatible con el tema Astra
* Responsive y accesible
* Soporte para teclado
* Internacionalización preparada
* Personalización de colores desde el panel de administración

**Compatibilidad probada:**
* Tema Astra (gratis y pro)
* WordPress 6.9+
* PHP 8.0+

== Instalación ==

1. Sube la carpeta `ygbmenu-store-selector` al directorio `/wp-content/plugins/`
2. Activa el plugin desde el menú 'Plugins' en WordPress
3. Ve a 'YGB Plugins → Selector Menu' para configurar
4. Inserta el shortcode `[ygbmenu_selector]` donde quieras mostrar el selector

== Configuración ==

El plugin incluye un panel de administración completo:

1. **Duración de cookies**: Configura cuántos días se recordará la selección (1-365 días)
2. **Dominio de cookie**: Para compartir cookies entre subdominios (ej: .midominio.com)
3. **Personalización de colores**: Cambia los colores del selector (fondo, borde, texto, spinner)
4. **Gestión de URLs**: Las URLs se gestionan desde el plugin "YGB Store Selector"

== Uso ==

**Shortcode:**
`[ygbmenu_selector]` - Selector básico
`[ygbmenu_selector class="mi-clase" id="mi-id"]` - Con clases personalizadas

**Widget:**
Ve a 'Apariencia → Widgets' y añade el widget "YGBMenu Selector"

**PHP Template:**
`<?php echo do_shortcode('[ygbmenu_selector]'); ?>`

**Atributos del shortcode:**
* `class` - Clases CSS adicionales
* `id` - ID personalizado para el selector
* `label` - Texto para etiqueta ARIA

== Preguntas frecuentes ==

= ¿Por qué no se muestran las URLs en el selector? =
El plugin depende del plugin "YGB Store Selector" para gestionar las URLs. Asegúrate de tenerlo instalado y configurado.

= ¿El CSS no se carga en algunas páginas? =
Si usas el tema Astra, ve a 'Apariencia → Customize → Performance' y desactiva "Inline Critical CSS". También limpia la caché de Astra desde 'Ajustes → Astra'.

= ¿Cómo cambio los colores del selector? =
Ve al panel de administración del plugin (YGB Menu) y usa los selectores de color. Puedes cambiar el color primario, hover, texto, fondo, borde y colores del spinner.

= ¿Las cookies funcionan entre subdominios? =
Sí, configura el "Dominio de cookie" en la administración con `.tudominio.com` (con punto al inicio).

== Changelog ==

= 1.2.5 - 2026-09-08 =
* SEGURIDAD: Content Security Policy (CSP) implementado vía header HTTP
* SEGURIDAD: Subresource Integrity (SRI) con hash SHA384 para archivos JavaScript
* HARDENING: función ygbmenu_add_csp_header() añade cabeceras de seguridad estrictas
* MEJORA: filtro script_loader_tag para inyectar atributos integrity y crossorigin
* DOCUMENTACIÓN: actualizada versión a 1.2.5 en todos los archivos
* ARQUITECTURA: defensa en profundidad (defense in depth) nivel empresarial

= 1.2.4 - 2026-08-15 =
* SEGURIDAD: validación de dominio de cookie mejorada usando FILTER_VALIDATE_DOMAIN
* SEGURIDAD: validación de URLs HTTPS para tiendas (filtro estricto)
* SEGURIDAD: eliminación de console.log/warn para evitar exposición de información
* MEJORA: hook de activación añadido para inicializar opciones por defecto
* MEJORA: archivo uninstall.php creado para limpieza completa al eliminar
* MEJORA: declaración "Requires Plugins" añadida en el header
* MEJORA: constantes JS para magic numbers (YGBMENU_REDIRECT_DELAY, YGBMENU_INIT_DELAY)
* DEPENDENCIA: ahora requiere explícitamente ygb-store-selector/ygb-store-selector.php

= 1.2.3 - 2026-07-20 =
* REQUISITOS: se actualiza la versión mínima de WordPress a 6.9+ y PHP a 8.0+.
* SEGURIDAD: normalización de URL en JS ahora devuelve cadena vacía en caso de error, previniendo redirecciones a `javascript:` o `data:`.
* ESCAPADO: en el widget se usa `wp_kses_post()` para `$args['before_widget']`, `after_widget`, `before_title` y `after_title` (hardening).
* VERSIÓN: actualizada a 1.2.3 en todos los archivos.
* CACHE: limpieza automática al desactivar el plugin.

= 1.2.2 - 2025-07-25 =
* SOLUCIÓN DEFINITIVA para compatibilidad con tema Astra
* CSS inline forzado para cargar en todas las páginas
* JavaScript optimizado con delay para Astra
* !important añadido a todos los estilos CSS
* Mejora en la carga de assets
* Corrección de errores críticos

= 1.0.9 =
* Versión estable base sin errores críticos
* Funcionalidad básica probada
* CSS con clases únicas para evitar conflictos

= 1.0.8 =
* Intento de añadir gestión avanzada de cookies
* Error crítico detectado y corregido en versión 1.0.9

= 1.0.5 =
* Versión funcional inicial
* Shortcode y widget operativos
* Administración básica

== Solución de problemas ==

**Error crítico en WordPress:**
1. Desactiva y reactiva el plugin
2. Verifica que el plugin "YGB Store Selector" esté activo
3. Revisa los logs de error de WordPress

**CSS no se carga en todas las páginas (especialmente con Astra):**
1. Ve a 'Apariencia → Customize → Performance'
2. Desactiva "Load Google Fonts Locally"
3. Desactiva "Preload Local Fonts"
4. Desactiva "Inline Critical CSS"
5. Guarda cambios
6. Ve a 'Ajustes → Astra' y haz clic en "Regenerate Assets & CSS Files"

**Selector no mantiene la selección:**
1. Verifica que las cookies estén habilitadas en el navegador
2. Revisa la configuración de duración de cookies en el admin
3. Prueba en modo incógnito

**Advertencia de seguridad (desde versión 1.2.3):**
* Si ves alertas de "URL no válida" al seleccionar una opción, verifica que las URLs estén correctamente formadas en el plugin YGB Store Selector.
* El plugin ahora bloquea redirecciones a protocolos no HTTP(S) por seguridad.

== Notas técnicas ==

**Estructura de archivos:**
* `ygbmenu-store-selector.php` - Archivo principal del plugin
* `ygbmenu-selector.css` - Estilos CSS personalizados
* `ygbmenu-selector.js` - Lógica JavaScript
* `ygbmenu-store-selector.pot` - Plantilla de traducción

**Hooks utilizados:**
* `wp_enqueue_scripts` - Para cargar CSS/JS
* `astra_head` - Hook específico para Astra
* `wp_head` - Hook genérico para CSS inline
* `admin_menu` - Para el panel de administración
* `widgets_init` - Para registrar el widget
* `plugins_loaded` - Para cargar traducciones

**Clases CSS únicas (para evitar conflictos):**
* `.ygbmenu-unique-container` - Contenedor principal
* `.ygbmenu-unique-select` - Elemento select
* `.loading-state` - Estado de carga

**Configuración JavaScript (ygbmenuConfig):**
* `cookieDays` - Días de duración de cookie
* `cookieName` - Nombre de la cookie ('tiendaActual')
* `cookieDomain` - Dominio para la cookie
* `i18n` - Textos traducibles

== Compatibilidad ==

**Temas probados:**
* Astra (gratis) - COMPATIBILIDAD COMPLETA
* Twenty Twenty-Four
* Twenty Twenty-Three
* GeneratePress

**Plugins requeridos:**
* YGB Store Selector - Para gestionar las URLs

**Navegadores soportados:**
* Chrome 60+
* Firefox 55+
* Safari 11+
* Edge 79+

== Mejores prácticas ==

1. **Configuración inicial:**
   - Define la duración de cookies según tus necesidades
   - Configura el dominio correcto si usas subdominios
   - Organiza las URLs en el plugin "YGB Store Selector"

2. **Para desarrolladores:**
   - Usa las clases CSS únicas para personalizaciones
   - El JavaScript está modularizado para fácil extensión
   - Los hooks están documentados en el código

3. **Mantenimiento:**
   - Limpia caché después de actualizaciones
   - Regenera assets de Astra si cambias estilos
   - Prueba en múltiples páginas del sitio

== Soporte ==

Para soporte técnico, revisa:
1. La sección de Preguntas Frecuentes
2. Los logs de error de WordPress
3. La consola del navegador para errores JavaScript

Si encuentras un bug, verifica:
1. Que todos los plugins requeridos estén activos
2. Que el tema sea compatible
3. Que no haya conflictos con otros plugins

== Licencia ==

Este plugin está licenciado bajo GPL v2 o posterior.

== Créditos ==

Desarrollado por YGB.
Compatibilidad con Astra implementada mediante CSS inline y hooks específicos.

== Aspectos técnicos importantes ==

**PROBLEMA RESUELTO: CSS no cargaba en páginas que no eran el home**
* **Causa**: Astra no cargaba estilos de plugins en todas las páginas
* **Solución**: Implementación de CSS inline mediante hook `astra_head`
* **Código clave**: Función `ygbmenu_astra_inline_css()` que fuerza CSS en todas las páginas

**PROBLEMA RESUELTO: Error crítico en WordPress**
* **Causa**: Funciones PHP no definidas en versión 1.0.8
* **Solución**: Reversión a código estable y añadido de validaciones
* **Lección**: Mantener compatibilidad con versiones anteriores

**PROBLEMA RESUELTO (v1.2.3): Vulnerabilidad potencial en normalización de URL**
* **Causa**: En JavaScript, si `new URL()` fallaba, se devolvía la URL sin sanitizar, pudiendo redirigir a `javascript:...`
* **Solución**: Ahora el `catch` devuelve cadena vacía y se valida que la URL comience con `http` antes de redirigir.
* **Mejora adicional**: Escapado de `$args` en widget con `wp_kses_post()`.

**OPTIMIZACIONES IMPLEMENTADAS:**
1. **CSS con !important** para sobrescribir estilos de tema
2. **Delay en JavaScript** para esperar carga completa de Astra
3. **Versiones dinámicas** en enqueue para evitar caché
4. **Polyfills** para navegadores antiguos
5. **Validación de archivos** antes de cargar
6. **Caché de CSS dinámico** con transients/object cache

**HOOKS ESPECÍFICOS PARA ASTRA:**
- `astra_head` - Para CSS inline garantizado
- Prioridad 5 en `wp_enqueue_scripts` - Carga temprana
- `wp_footer` con prioridad 1 - Para JavaScript de respaldo

**ESTRUCTURA DE ARCHIVOS MANTENIDA:**
- `ygbmenu-store-selector.php` - Lógica principal y administración
- `ygbmenu-selector.css` - Estilos con clases únicas
- `ygbmenu-selector.js` - Lógica frontend con polyfills
- Archivo .pot para futuras traducciones

**PARA FUTURAS ACTUALIZACIONES:**
1. Siempre probar en páginas que NO sean el home
2. Verificar compatibilidad con última versión de Astra
3. Mantener el sistema de CSS inline para Astra
4. Actualizar versiones en todos los archivos simultáneamente
5. Documentar cambios en este README
6. Mantener las medidas de seguridad introducidas en v1.2.3
7. Revisar compatibilidad con las nuevas versiones de WP (6.9+) y PHP (8.0+)