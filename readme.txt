=== YGBMenu Store Selector ===
Contributors: YGB
Tags: selector, dropdown, redirect, cookies, store selector, menu
Requires at least: 7.0
Tested up to: 7.1
Stable tag: 1.5.3
Requires PHP: 8.0
Tested PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Un selector desplegable de URLs para redirección con persistencia mediante cookies.

== Description ==

YGBMenu Store Selector es un plugin de WordPress que proporciona un selector desplegable para redirigir usuarios a diferentes URLs, manteniendo su selección persistente mediante cookies. Muestra el logo del sitio sobre el selector y ajusta automáticamente el ancho del campo al texto de la opción seleccionada.

**Características principales:**

* Selector desplegable con estilos personalizados
* Logo del sitio sobre el selector (opcional, con ancho y alto configurables para escritorio y móvil)
* Ancho del selector adaptativo al texto de la opción seleccionada
* Persistencia de selección mediante cookies (configurable)
* Redirección automática al seleccionar una opción
* Shortcode `[ygbmenu_selector]` para inserción fácil
* Widget personalizado para el sidebar
* Panel de administración completo
* Totalmente compatible con el tema Astra
* Responsive y accesible
* Soporte para teclado
* Internacionalización preparada
* Personalización de colores, tipografía y grosores desde el panel

**Valores por defecto:**

* Ancho máximo del logo (escritorio): 185 px
* Ancho máximo del logo (móvil): 120 px
* Tamaño del texto (escritorio): 20 px
* Tamaño del texto (móvil): 13 px
* Duración de cookies: 30 días
* Colores: #E26143 (texto/borde), #D14F32 (hover), #ffffff (fondo), #00A32E (spinner secundario)

**Compatibilidad probada:**

* Tema Astra (gratis y pro)
* WordPress 7.0+
* PHP 8.0+

== Instalación ==

1. Sube la carpeta `ygbmenu-store-selector` al directorio `/wp-content/plugins/`
2. Activa el plugin desde el menú 'Plugins' en WordPress
3. Ve a 'YGB Menu' para configurar
4. Inserta el shortcode `[ygbmenu_selector]` donde quieras mostrar el selector

== Configuración ==

El plugin incluye un panel de administración completo:

1. **Logo y selector — escritorio**: ancho máximo (185 px por defecto) y alto máximo (0 = sin límite) del logo.
2. **Logo y selector — móvil**: ancho máximo (120 px por defecto) y alto máximo (0 = sin límite) del logo para pantallas ≤ 768 px.
3. **Fondo transparente**: quita fondo y borde del selector.
4. **Texto del selector**: tamaño de fuente en escritorio (20 px por defecto) y móvil (13 px por defecto), opción de seminegrita (font-weight 600).
5. **Cookies**: duración en días (1–365, por defecto 30) y dominio (para subdominios).
6. **Personalización de colores**: hover, texto, fondo, borde y spinner (primario y secundario).
7. **Gestión de URLs**: las URLs se gestionan desde el plugin "YGB Store Selector".

== Uso ==

**Shortcode:**

`[ygbmenu_selector]` - Selector básico
`[ygbmenu_selector class="mi-clase" id="mi-id"]` - Con clases personalizadas

**Widget:**

Ve a 'Apariencia → Widgets' y añade el widget "YGB Menu Selector"

**PHP Template:**

`<?php echo do_shortcode('[ygbmenu_selector]'); ?>`

**Atributos del shortcode:**

* `class` - Clases CSS adicionales
* `id` - ID personalizado para el selector (se sanea con `sanitize_key()`)
* `label` - Texto para etiqueta ARIA

== Preguntas frecuentes ==

= ¿Por qué no se muestran las URLs en el selector? =

El plugin depende del plugin "YGB Store Selector" para gestionar las URLs. Asegúrate de tenerlo instalado y configurado.

= ¿El CSS no se carga en algunas páginas? =

Si usas el tema Astra, ve a 'Apariencia → Customize → Performance' y desactiva "Inline Critical CSS". También limpia la caché de Astra desde 'Ajustes → Astra'.

= ¿Cómo cambio los colores del selector? =

Ve al panel de administración del plugin (YGB Menu → Personalización de Colores) y usa los selectores de color.

= ¿Las cookies funcionan entre subdominios? =

Sí, configura el "Dominio de cookie" con `.tudominio.com` (con punto al inicio). El plugin normaliza el valor automáticamente y lo valida contra el host del sitio.

= ¿El logo se corta o desborda en móvil? =

No. El logo se redimensiona proporcionalmente con `object-fit: contain`. Hay controles independientes para móvil (ancho y alto máximos) en el panel.

= ¿El texto del selector se corta? =

No. El ancho del `<select>` se calcula con JavaScript midiendo el texto de la opción seleccionada más el padding y bordes. El contenedor (con el logo) sigue alineado al ancho del texto más largo de todas las opciones.

= ¿La flecha del selector queda muy separada del texto? =

No. La flecha se posiciona justo después del texto actual. El SVG de la flecha está ajustado a su contenido real (sin espacio transparente) y ocupa 8 px en escritorio, 6 px en móvil.

= ¿Cómo aplico los nuevos valores por defecto si ya tenía el plugin instalado? =

Los valores por defecto solo se aplican a instalaciones nuevas. Si ya tenías el plugin, abre `YGB Menu` y edita manualmente los campos o pulsa "Restaurar" en los que quieras volver a los defaults:
* Ancho máximo del logo: 185
* Ancho máximo del logo en móvil: 120
* Tamaño del texto en escritorio: 20
* Tamaño del texto en móvil: 13

= ¿Por qué el texto del selector no coincide con el título del sitio? =

Asegúrate de tener activada la casilla "Texto en negrita" en el panel. Aplica `font-weight: 600`, que coincide con el peso tipográfico por defecto del título del sitio en Astra. Sin la casilla, aplica `font-weight: 400`.

== Changelog ==

= 1.5.3 - 2026-10-09 =

* NUEVO: nuevos valores por defecto en el panel:
  * Ancho máximo del logo en escritorio: 185 px (antes 160).
  * Ancho máximo del logo en móvil: 120 px (sin cambio).
  * Tamaño del texto en escritorio: 20 px (antes 16).
  * Tamaño del texto en móvil: 13 px (antes 14).
* MANTENIMIENTO: los valores por defecto solo se aplican a instalaciones nuevas. Los sitios existentes conservan su configuración guardada.
* MANTENIMIENTO: bump de versión para invalidar la caché de CSS dinámico.

= 1.5.2 - 2026-10-09 =

* MEJORA: el ancho del selector ahora se ajusta al texto de la opción seleccionada (antes se ajustaba al texto más largo del listado, dejando hueco).
* MEJORA: el contenedor (logo + selector) sigue alineado al ancho del texto más largo para no moverse al cambiar de opción.
* MEJORA: la flecha queda pegada al fin del texto seleccionado tanto en escritorio como en móvil.

= 1.5.1 - 2026-10-09 =

* CORRECCIÓN: la flecha del selector se había agrandado respecto a versiones previas por un cambio de SVG. Ahora mide 8 px (escritorio) y 6 px (móvil).
* CORRECCIÓN: `padding-right` del selector reducido a 12 px (escritorio) y 10 px (móvil) para acercar la flecha al texto.
* CORRECCIÓN: `background-position` de la flecha a `right 2px center` para pegarla al borde derecho del select.

= 1.5.0 - 2026-10-09 =

* MEJORA: SVG de la flecha reemplazado por un triángulo relleno ajustado al contenido (`viewBox="0 0 16 10"`).
* MEJORA: reducción de `padding-right` y `background-size` para minimizar el hueco entre el texto y la flecha.

= 1.4.9 - 2026-10-09 =

* MEJORA: la opción "Texto en negrita" cambia el `font-weight` de 700 a 600, coincidiendo con la tipografía del título del sitio en Astra.
* ACTUALIZACIÓN: descripción del checkbox en el panel: "Mostrar el texto en seminegrita (font-weight: 600)".

= 1.4.8 - 2026-10-09 =

* MEJORA: flecha pegada al borde derecho del select.
* MEJORA: `padding-right` del select reducido a 20 px (escritorio) y 18 px (móvil).

= 1.4.7 - 2026-10-09 =

* MEJORA: flecha del selector pegada al borde con `background-position: right 2px center`.
* MEJORA: `padding-right` del select reducido a 26 px (escritorio) y 24 px (móvil).

= 1.4.6 - 2026-10-09 =

* CORRECCIÓN: en móvil, el texto del selector ya no se corta. El ancho del contenedor se adapta al máximo entre el logo y el texto del select (`max()` en CSS).
* CORRECCIÓN: el logo ya no se recorta. Se reduce proporcionalmente con `object-fit: contain`.
* MEJORA: `--ygbmenu-select-w` variable CSS para comunicar el ancho calculado por JS al contenedor.

= 1.4.5 - 2026-10-09 =

* CORRECCIÓN: el logo se recortaba en móvil en lugar de reducirse. Ahora usa `width: 100%; max-width: 100%; height: auto; object-fit: contain`.
* MEJORA: `overflow: visible` en móvil para no cortar el contenido.

= 1.4.4 - 2026-10-09 =

* NUEVO: controles independientes para móvil (≤ 768 px): ancho y alto máximos del logo, tamaño del texto.
* MEJORA: el shortcode expone `--ygbmenu-logo-w-mobile` y `--ygbmenu-logo-h-mobile` como variables CSS.

= 1.4.3 - 2026-10-09 =

* CORRECCIÓN: el select ya no desborda el contenedor en móvil. El ancho se clampea al padre.
* CORRECCIÓN: la cabecera de Astra no se desconfigura en móvil.

= 1.4.2 - 2026-10-09 =

* NUEVO: ancho del selector adaptativo al contenido mediante JavaScript (mide el texto más largo).
* MEJORA: el select ya no ocupa el 100% del contenedor.

= 1.4.1 - 2026-10-09 =

* NUEVO: control del tamaño del texto en el panel (10–40 px).
* NUEVO: opción "Texto en negrita" (font-weight 700, cambiado a 600 en 1.4.9).
* MEJORA: en móvil, el tamaño del texto se reduce automáticamente 2 px (mínimo 12 px).

= 1.4.0 - 2026-10-09 =

* MEJORA: pin de localización pegado al borde izquierdo del select, alineado con el borde izquierdo del logo.
* MEJORA: `padding-left` del select reducido a 22 px.

= 1.3.9 - 2026-10-09 =

* NUEVO: logo sobre el selector en el shortcode y el widget.
* NUEVO: SVG del pin de localización integrado como imagen de fondo.

= 1.3.7 - 2026-10-09 =

* CAMBIO: eliminada la opción "Color primario". Solo se mantiene "Color hover" para el efecto hover.
* CORRECCIÓN: eliminado el doble borde (halo) del focus.

= 1.3.5 - 2026-10-09 =

* NUEVO: opción "Fondo transparente" para quitar fondo y borde del selector.
* MEJORA: `uninstall.php` limpia transients por prefijo con `$wpdb->prepare()` + `$wpdb->esc_like()`.

= 1.3.2 - 2026-10-09 =

* CORRECCIÓN: eliminada la regla que forzaba el ancho al 100%. El select ahora se ajusta al contenido.

= 1.3.0 - 2026-10-09 =

* MEJORA: se eliminaron del plugin las reglas sobre contenedores de Astra para evitar conflictos.

= 1.2.6 - 2026-10-08 =

* SEGURIDAD: validación del dominio de cookie con `FILTER_VALIDATE_DOMAIN` + `FILTER_FLAG_HOSTNAME`.
* SEGURIDAD: el dominio de cookie solo se aplica si coincide con el host del sitio.
* SEGURIDAD: eliminado el fallback a `$_SERVER['HTTP_HOST']`.
* SEGURIDAD: todos los `$_POST` pasan por `wp_unslash()` y `sanitize_*()`.
* CORRECCIÓN: `preg_match()` ya no recibe `null` en PHP 8.1+.
* CORRECCIÓN: eliminado `wp_kses_post()` sobre `$args['before_widget']` (rompía sidebars).
* CORRECCIÓN: eliminada la variable variable `$$key` del guardado de colores.
* MEJORA: caché de CSS dinámico versionada.

= 1.2.5 - 2026-09-08 =

* Requisitos mínimos actualizados a WordPress 7.0+.
* Compatibilidad probada hasta WordPress 7.1.
* PHP probado hasta versión 8.2.
* Declaración "Requires Plugins" en el header para dependencia explícita.

= 1.2.4 - 2026-08-15 =

* SEGURIDAD: validación de dominio de cookie mejorada usando FILTER_VALIDATE_DOMAIN.
* SEGURIDAD: eliminación de console.log/warn para evitar exposición de información.
* MEJORA: hook de activación añadido para inicializar opciones por defecto.
* MEJORA: archivo uninstall.php creado para limpieza completa al eliminar.

= 1.2.3 - 2026-07-20 =

* SEGURIDAD: normalización de URL en JS ahora devuelve cadena vacía en caso de error, previniendo redirecciones a `javascript:` o `data:`.
* VERSIÓN: actualizada a 1.2.3 en todos los archivos.
* CACHE: limpieza automática al desactivar el plugin.

= 1.2.2 - 2025-07-25 =

* SOLUCIÓN DEFINITIVA para compatibilidad con tema Astra.
* CSS inline forzado para cargar en todas las páginas.
* JavaScript optimizado con delay para Astra.
* !important añadido a todos los estilos CSS.

= 1.0.9 =

* Versión estable base sin errores críticos.
* CSS con clases únicas para evitar conflictos.

= 1.0.8 =

* Intento de añadir gestión avanzada de cookies.
* Error crítico detectado y corregido en versión 1.0.9.

= 1.0.5 =

* Versión funcional inicial.
* Shortcode y widget operativos.
* Administración básica.

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

**El logo no se ve sobre el selector:**

1. Verifica que tengas configurado el logo del sitio en 'Apariencia → Personalizar → Identidad del sitio'
2. El plugin usa `get_custom_logo()`, por lo que necesita el logo del tema

**La flecha del selector aparece muy separada del texto:**

1. Verifica que estés en la versión 1.5.2 o superior
2. Limpia la caché del navegador con Ctrl + Shift + R
3. Limpia la caché del plugin de caché si tienes

**Los valores por defecto no se aplican tras actualizar:**

Los defaults solo se aplican a instalaciones nuevas. En instalaciones existentes, edita los campos manualmente o pulsa "Restaurar" en el panel `YGB Menu`.

== Notas técnicas ==

**Estructura de archivos:**

* `ygbmenu-store-selector.php` - Archivo principal del plugin
* `ygbmenu-selector.css` - Estilos base del selector
* `ygbmenu-selector.js` - Lógica del frontend (ancho adaptativo)
* `uninstall.php` - Limpieza al eliminar el plugin
* `readme.txt` - Este fichero

**Hooks utilizados:**

* `wp_enqueue_scripts` - Para cargar CSS/JS
* `wp_head` y `astra_head` - Para el CSS dinámico
* `admin_menu` - Para el panel de administración
* `admin_init` - Para el guardado de ajustes
* `admin_notices` - Para el aviso de dependencia
* `widgets_init` - Para registrar el widget
* `plugins_loaded` - Para cargar traducciones

**Clases CSS y atributos:**

* `.ygbmenu-unique-container` - Contenedor principal
* `.ygbmenu-with-logo` - Contenedor cuando hay logo
* `.ygbmenu-unique-select` - Elemento select
* `.loading-state` - Estado de carga
* `[data-ygbmenu-selector]` - Ancla estable para que el JS encuentre los selects

**Variables CSS (inyectadas por PHP):**

* `--ygbmenu-logo-w` - Ancho máximo del logo en escritorio
* `--ygbmenu-logo-h` - Alto máximo del logo en escritorio
* `--ygbmenu-logo-w-mobile` - Ancho máximo del logo en móvil
* `--ygbmenu-logo-h-mobile` - Alto máximo del logo en móvil

**Variables CSS (inyectadas por JS):**

* `--ygbmenu-select-w` - Ancho calculado del contenedor según el texto más largo

**Configuración JavaScript (`ygbmenuConfig`):**

* `cookieDays` - Días de duración de cookie
* `cookieName` - Nombre de la cookie (`tiendaActual`)
* `cookieDomain` - Dominio para la cookie (puede ser vacío → cookie host-only)
* `secure` - `true` si el sitio usa HTTPS
* `i18n` - Textos traducibles (`loading`, `invalid_url`)

**Caché de CSS dinámico:**

* Clave versionada: `ygbmenu_dynamic_css_{YGBMENU_VERSION}`
* Se guarda en object cache externo si está disponible; en caso contrario, en un transient de 1 día.
* Se invalida al guardar ajustes o al actualizar la versión del plugin.

**Opciones de WordPress utilizadas:**

* `ygbmenu_cookie_days` (default: 30)
* `ygbmenu_cookie_domain` (default: '')
* `ygbmenu_logo_max_width` (default: 185)
* `ygbmenu_logo_max_height` (default: 0)
* `ygbmenu_logo_max_width_mobile` (default: 120)
* `ygbmenu_logo_max_height_mobile` (default: 0)
* `ygbmenu_transparent_bg` (default: 0)
* `ygbmenu_text_size` (default: 20)
* `ygbmenu_text_size_mobile` (default: 13)
* `ygbmenu_text_bold` (default: 0)
* `ygbmenu_color_hover` (default: '#D14F32')
* `ygbmenu_color_text` (default: '#E26143')
* `ygbmenu_color_background` (default: '#ffffff')
* `ygbmenu_color_border` (default: '#E26143')
* `ygbmenu_color_spinner_primary` (default: '#E26143')
* `ygbmenu_color_spinner_secondary` (default: '#00A32E')

== Compatibilidad ==

**Temas probados:**

* Astra (gratis) - COMPATIBILIDAD COMPLETA
* Twenty Twenty-Four
* Twenty Twenty-Three
* GeneratePress

**Plugins requeridos:**

* YGB Store Selector - Para gestionar las URLs

**Navegadores soportados:**

* Chrome 79+
* Firefox 75+
* Safari 13.1+
* Edge 79+

(Requiere soporte de CSS `max()` y `:has()`. El JS usa `Element.closest`, disponible desde Chrome 41+, Firefox 35+, Safari 9+.)

== Mejores prácticas ==

1. **Configuración inicial:**
   - Define la duración de cookies según tus necesidades
   - Configura el dominio correcto si usas subdominios
   - Ajusta el ancho del logo en escritorio y móvil por separado
   - Organiza las URLs en el plugin "YGB Store Selector"

2. **Para desarrolladores:**
   - Usa las clases CSS únicas para personalizaciones
   - El JavaScript usa delegación de eventos: puedes añadir selects dinámicamente sin reinicializar
   - Los hooks están documentados en el código
   - El CSS dinámico está versionado para invalidación automática

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
Compatibilidad con Astra implementada mediante CSS dinámico y hooks específicos.

== Aspectos técnicos importantes ==

**PROBLEMA RESUELTO: CSS no cargaba en páginas que no eran el home**

* **Causa**: Astra no cargaba estilos de plugins en todas las páginas.
* **Solución**: CSS dinámico inyectado mediante `wp_head` y `astra_head` con prioridad 999.

**PROBLEMA RESUELTO: Vulnerabilidad potencial en normalización de URL (v1.2.3)**

* **Causa**: si `new URL()` fallaba, se devolvía la URL sin sanitizar, pudiendo redirigir a `javascript:...`.
* **Solución**: el `catch` devuelve cadena vacía y se valida que la URL comience con `http`.

**PROBLEMA RESUELTO (v1.3.0): Conflicto con contenedores de Astra**

* **Causa**: el plugin metía reglas sobre `.header-widget-area` que rompían otros elementos del tema.
* **Solución**: eliminadas esas reglas. El plugin solo controla su propio bloque.

**PROBLEMA RESUELTO (v1.4.5 y v1.4.6): El logo se cortaba en móvil**

* **Causa**: el contenedor tenía `overflow: hidden` y la imagen no se redimensionaba proporcionalmente.
* **Solución**: `width: 100%; max-width: 100%; height: auto; object-fit: contain`. Y `overflow: visible` en móvil.

**PROBLEMA RESUELTO (v1.4.6): El texto del selector se cortaba en móvil**

* **Causa**: el JS clampeaba el ancho del select al del contenedor padre.
* **Solución**: el contenedor se adapta con `max()` al máximo entre el ancho del logo y el ancho del select.

**PROBLEMA RESUELTO (v1.5.2): La flecha quedaba muy separada del texto**

* **Causa**: el select medía el ancho del texto más largo del listado, aunque la opción seleccionada fuera más corta.
* **Solución**: el JS mide dos anchos (texto más largo → contenedor; texto actual → select). El select se ajusta al texto seleccionado, el contenedor se mantiene al más largo para que el logo no se mueva.

**PARA FUTURAS ACTUALIZACIONES:**

1. Siempre probar en páginas que NO sean el home
2. Verificar compatibilidad con la última versión de Astra
3. Actualizar `YGBMENU_VERSION` en el header y en `readme.txt`
4. Añadir la nueva clave de caché a `uninstall.php` (`ygbmenu_dynamic_css_X.Y.Z`)
5. Documentar cambios en este README
6. Mantener las medidas de seguridad introducidas en v1.2.3 y v1.2.6
7. Si se añaden nuevos endpoints AJAX: nonce + capability check + validación
8. Si se añaden llamadas HTTP: `sslverify` + timeout + `is_wp_error()` + response code
9. Si se leen/escriben archivos locales: usar `WP_Filesystem` API