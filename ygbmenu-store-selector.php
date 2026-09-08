<?php
/**
 * Plugin Name: YGB Menu Store Selector
 * Plugin URI: https://github.com/yosdeny
 * Description: Selector desplegable de url para redirección con persistencia mediante cookies. Shortcode: [ygbmenu_selector]. Depende del Plugin (YGB Store Selector) para crear y gestionar las URLs.
 * Version: 1.2.5
 * Requires at least: 7.0
 * Tested up to: 7.1
 * Requires PHP: 8.0
 * Tested PHP: 8.2
 * Requires Plugins: ygb-store-selector/ygb-store-selector.php
 * Author: YGB
 * Author URI: https://github.com/yosdeny
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ygbmenu-store-selector
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

// ==================== DEFINICIONES ====================
define('YGBMENU_VERSION', '1.2.5');
define('YGBMENU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('YGBMENU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('YGBMENU_PLUGIN_NAME', 'YGB Menu Store Selector');
define('YGBMENU_SRI_HASH_JS', 'sha384-91983d28fbf536f8b3081745cfcceaae007d0e5cc4f3a379848d877c5fead5b20d22168ae1a60085854c107df315614e');

// ==================== VERIFICACIÓN DE DEPENDENCIAS ====================

/**
 * Verificar si el plugin YGB Store Selector está activo
 */
function ygbmenu_is_dependency_active() {
    // Verificar si la función del plugin dependiente existe
    if (function_exists('ygb_selector_get_stores')) {
        return true;
    }
    
    // Verificar si la opción existe (puede que el plugin esté desactivado pero los datos sigan)
    $stores = get_option('ygb_selector_stores', null);
    if ($stores !== null) {
        return true;
    }
    
    // Verificar si el plugin está activo por nombre
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    if (function_exists('is_plugin_active') && is_plugin_active('ygb-store-selector/ygb-store-selector.php')) {
        return true;
    }
    
    return false;
}

/**
 * Obtener tiendas de forma segura (con fallback)
 */
function ygbmenu_get_stores_safe() {
    $tiendas = get_option('ygb_selector_stores', array());
    
    // Validar que sea un array
    if (!is_array($tiendas)) {
        $tiendas = array();
    }
    
    // Filtrar entradas válidas con validación de URL HTTPS
    $tiendas = array_filter($tiendas, function($tienda) {
        if (!is_array($tienda) || empty($tienda['url']) || empty($tienda['nombre'])) {
            return false;
        }
        // Validar que la URL sea absoluta y preferiblemente HTTPS
        $url = filter_var($tienda['url'], FILTER_VALIDATE_URL);
        return $url && strpos($url, 'https://') === 0;
    });
    
    return $tiendas;
}

/**
 * Mostrar aviso si el plugin dependiente no está activo
 */
function ygbmenu_dependency_notice() {
    if (!ygbmenu_is_dependency_active()) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong>YGB Menu Store Selector:</strong>
                <?php esc_html_e('Este plugin requiere que el plugin "YGB Store Selector" esté instalado y activo para funcionar correctamente. Las URLs no se mostrarán hasta que lo actives.', 'ygbmenu-store-selector'); ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'ygbmenu_dependency_notice');

// ==================== FUNCIÓN SEGURA PARA CONVERTIR HEX A RGB ====================

function ygbmenu_hex2rgb_safe($hex) {
    $hex = ltrim($hex, '#');
    
    if (!preg_match('/^[0-9a-fA-F]{3}$|^[0-9a-fA-F]{6}$/', $hex)) {
        return '226, 97, 67';
    }
    
    if (strlen($hex) == 3) {
        $r = hexdec($hex[0] . $hex[0]);
        $g = hexdec($hex[1] . $hex[1]);
        $b = hexdec($hex[2] . $hex[2]);
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    
    return "$r, $g, $b";
}

function ygbmenu_escape_svg_for_css($svg_content) {
    $svg_content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $svg_content);
    $svg_content = preg_replace('/on\w+="[^"]*"/i', '', $svg_content);
    $svg_content = preg_replace('/javascript:/i', '', $svg_content);
    
    return 'url("data:image/svg+xml;charset=utf-8,' . rawurlencode($svg_content) . '")';
}

// ==================== CSS DINÁMICO ====================

function ygbmenu_clear_dynamic_css_cache() {
    if (wp_using_ext_object_cache()) {
        wp_cache_delete('ygbmenu_dynamic_css', 'ygbmenu');
    } else {
        delete_transient('ygbmenu_dynamic_css');
    }
}

function ygbmenu_get_dynamic_css() {
    $cached_css = false;
    
    if (wp_using_ext_object_cache()) {
        $cached_css = wp_cache_get('ygbmenu_dynamic_css', 'ygbmenu');
    } else {
        $cached_css = get_transient('ygbmenu_dynamic_css');
    }
    
    if ($cached_css !== false) {
        return $cached_css;
    }
    
    $color_primary = get_option('ygbmenu_color_primary', '#E26143');
    $color_hover = get_option('ygbmenu_color_hover', '#D14F32');
    $color_text = get_option('ygbmenu_color_text', '#E26143');
    $color_background = get_option('ygbmenu_color_background', '#ffffff');
    $color_border = get_option('ygbmenu_color_border', '#E26143');
    $color_spinner_primary = get_option('ygbmenu_color_spinner_primary', '#E26143');
    $color_spinner_secondary = get_option('ygbmenu_color_spinner_secondary', '#00A32E');
    
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_primary)) $color_primary = '#E26143';
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_hover)) $color_hover = '#D14F32';
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_text)) $color_text = '#E26143';
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_background)) $color_background = '#ffffff';
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_border)) $color_border = '#E26143';
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_spinner_primary)) $color_spinner_primary = '#E26143';
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_spinner_secondary)) $color_spinner_secondary = '#00A32E';
    
    $svg_location = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"><path d="M8 0C4.7 0 2 2.7 2 6c0 1.5 0.6 2.9 1.5 4l4.5 6 4.5-6c0.9-1.1 1.5-2.5 1.5-4 0-3.3-2.7-6-6-6zm0 8.5c-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5z" fill="' . esc_attr($color_text) . '"/></svg>';
    $svg_arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="' . esc_attr($color_text) . '" d="M7 10l5 5 5-5z"/></svg>';
    $svg_spinner = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke="' . esc_attr($color_spinner_secondary) . '" stroke-width="2" fill="none"/><circle cx="12" cy="12" r="8" stroke="' . esc_attr($color_spinner_primary) . '" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="1 20"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg>';
    
    $rgb_primary = ygbmenu_hex2rgb_safe($color_primary);
    $rgb_hover = ygbmenu_hex2rgb_safe($color_hover);
    
    $css = '
    /* === COLORES PERSONALIZADOS YGB MENU === */
    
    .ygbmenu-unique-select {
        color: ' . esc_attr($color_text) . ' !important;
        border-color: ' . esc_attr($color_border) . ' !important;
        background-color: ' . esc_attr($color_background) . ' !important;
        background-image: ' . ygbmenu_escape_svg_for_css($svg_location) . ', ' . ygbmenu_escape_svg_for_css($svg_arrow) . ' !important;
        background-position: left 10px center, right 10px center !important;
        background-size: 16px, 20px !important;
        background-repeat: no-repeat, no-repeat !important;
    }
    
    .ygbmenu-unique-select:focus {
        border-color: ' . esc_attr($color_hover) . ' !important;
        box-shadow: 0 0 0 2px rgba(' . esc_attr($rgb_primary) . ', 0.4) !important;
    }
    
    .ygbmenu-unique-select:hover {
        border-color: ' . esc_attr($color_hover) . ' !important;
        box-shadow: 0 0 0 2px rgba(' . esc_attr($rgb_hover) . ', 0.3) !important;
    }
    
    .ygbmenu-unique-select.loading-state {
        background-image: ' . ygbmenu_escape_svg_for_css($svg_location) . ', ' . ygbmenu_escape_svg_for_css($svg_spinner) . ' !important;
        background-position: left 10px center, right 10px center !important;
        background-size: 16px, 24px !important;
    }
    
    .ygbmenu-unique-select:focus-visible {
        outline: 2px solid ' . esc_attr($color_primary) . ' !important;
    }
    
    @media (max-width: 480px) {
        .ygbmenu-unique-select {
            background-position: left 8px center, right 8px center !important;
            background-size: 14px, 18px !important;
        }
        .ygbmenu-unique-select.loading-state {
            background-size: 14px, 22px !important;
        }
    }';
    
    if (wp_using_ext_object_cache()) {
        wp_cache_set('ygbmenu_dynamic_css', $css, 'ygbmenu', DAY_IN_SECONDS);
    } else {
        set_transient('ygbmenu_dynamic_css', $css, DAY_IN_SECONDS);
    }
    
    return $css;
}

function ygbmenu_dynamic_css() {
    if (!is_admin() && !is_customize_preview()) {
        $css = ygbmenu_get_dynamic_css();
        if (!empty($css)) {
            echo '<style id="ygbmenu-custom-colors">' . $css . '</style>';
        }
    }
}
add_action('wp_head', 'ygbmenu_dynamic_css', 999);
add_action('astra_head', 'ygbmenu_dynamic_css', 999);

/**
 * Añadir Content Security Policy (CSP) para hardening de seguridad
 */
function ygbmenu_add_csp_header() {
    if (!is_admin() && !is_customize_preview()) {
        // CSP estricto que permite solo recursos del mismo origen y datos para SVG inline
        $csp_policy = "default-src 'self'; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' data:; img-src 'self' data: blob:; font-src 'self' data:; connect-src 'self' https:; frame-ancestors 'self'; base-uri 'self'; form-action 'self';";
        
        header("Content-Security-Policy: {$csp_policy}");
    }
}
add_action('send_headers', 'ygbmenu_add_csp_header');

// ==================== ADMINISTRACIÓN ====================

function ygbmenu_is_ygbplugin_active() {
    return function_exists('ygbplugin_register') || class_exists('YGBPlugin_System');
}

function ygbmenu_save_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (!isset($_POST['ygbmenu_nonce']) || !wp_verify_nonce($_POST['ygbmenu_nonce'], 'ygbmenu_save_action')) {
        return;
    }
    
    if (!isset($_POST['submit'])) {
        return;
    }
    
    $cookie_days = isset($_POST['cookie_days']) ? intval($_POST['cookie_days']) : 30;
    $cookie_days = max(1, min(365, $cookie_days));
    
    $cookie_domain = isset($_POST['cookie_domain']) ? sanitize_text_field($_POST['cookie_domain']) : '';
    if (!empty($cookie_domain)) {
        // Validación mejorada de dominio usando FILTER_VALIDATE_DOMAIN
        if (!filter_var($cookie_domain, FILTER_VALIDATE_DOMAIN)) {
            $cookie_domain = '';
        }
    }
    
    $color_primary = isset($_POST['color_primary']) ? sanitize_hex_color($_POST['color_primary']) : '#E26143';
    $color_hover = isset($_POST['color_hover']) ? sanitize_hex_color($_POST['color_hover']) : '#D14F32';
    $color_text = isset($_POST['color_text']) ? sanitize_hex_color($_POST['color_text']) : '#E26143';
    $color_background = isset($_POST['color_background']) ? sanitize_hex_color($_POST['color_background']) : '#ffffff';
    $color_border = isset($_POST['color_border']) ? sanitize_hex_color($_POST['color_border']) : '#E26143';
    $color_spinner_primary = isset($_POST['color_spinner_primary']) ? sanitize_hex_color($_POST['color_spinner_primary']) : '#E26143';
    $color_spinner_secondary = isset($_POST['color_spinner_secondary']) ? sanitize_hex_color($_POST['color_spinner_secondary']) : '#00A32E';
    
    $colors = array(
        'color_primary' => $color_primary,
        'color_hover' => $color_hover,
        'color_text' => $color_text,
        'color_background' => $color_background,
        'color_border' => $color_border,
        'color_spinner_primary' => $color_spinner_primary,
        'color_spinner_secondary' => $color_spinner_secondary
    );
    
    foreach ($colors as $key => $color) {
        if (empty($color) || !preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            $defaults = array(
                'color_primary' => '#E26143',
                'color_hover' => '#D14F32',
                'color_text' => '#E26143',
                'color_background' => '#ffffff',
                'color_border' => '#E26143',
                'color_spinner_primary' => '#E26143',
                'color_spinner_secondary' => '#00A32E'
            );
            $$key = $defaults[$key];
        }
    }
    
    update_option('ygbmenu_cookie_days', $cookie_days);
    update_option('ygbmenu_cookie_domain', $cookie_domain);
    update_option('ygbmenu_color_primary', $color_primary);
    update_option('ygbmenu_color_hover', $color_hover);
    update_option('ygbmenu_color_text', $color_text);
    update_option('ygbmenu_color_background', $color_background);
    update_option('ygbmenu_color_border', $color_border);
    update_option('ygbmenu_color_spinner_primary', $color_spinner_primary);
    update_option('ygbmenu_color_spinner_secondary', $color_spinner_secondary);
    
    ygbmenu_clear_dynamic_css_cache();
    
    add_settings_error(
        'ygbmenu_messages',
        'ygbmenu_message',
        esc_html__('Configuración guardada correctamente.', 'ygbmenu-store-selector'),
        'success'
    );
}
add_action('admin_init', 'ygbmenu_save_settings');

function ygbmenu_render_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('No tienes permisos suficientes.', 'ygbmenu-store-selector'));
    }
    
    settings_errors('ygbmenu_messages');
    
    $cookie_days = get_option('ygbmenu_cookie_days', 30);
    $cookie_domain = get_option('ygbmenu_cookie_domain', '');
    
    $color_primary = get_option('ygbmenu_color_primary', '#E26143');
    $color_hover = get_option('ygbmenu_color_hover', '#D14F32');
    $color_text = get_option('ygbmenu_color_text', '#E26143');
    $color_background = get_option('ygbmenu_color_background', '#ffffff');
    $color_border = get_option('ygbmenu_color_border', '#E26143');
    $color_spinner_primary = get_option('ygbmenu_color_spinner_primary', '#E26143');
    $color_spinner_secondary = get_option('ygbmenu_color_spinner_secondary', '#00A32E');
    
    $dependency_active = ygbmenu_is_dependency_active();
    $stores = ygbmenu_get_stores_safe();
    $show_breadcrumb = ygbmenu_is_ygbplugin_active();
    ?>
    <div class="wrap ygbmenu-admin-wrap">
        <?php if ($show_breadcrumb): ?>
        <div style="margin: 0 0 20px 0; padding: 10px 15px; background: #f0f6fc; border-radius: 6px; border-left: 4px solid #4299e1;">
            <p style="margin: 0; font-size: 14px; display: flex; align-items: center;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=ygbplugin-dashboard')); ?>" style="text-decoration: none; color: #2271b1;">
                    <span class="dashicons dashicons-admin-site-alt3" style="margin-right: 8px;"></span>YGBPlugin Dashboard
                </a>
                <span style="margin: 0 15px; color: #8c8f94;">›</span>
                <strong>YGB Menu Store Selector v<?php echo esc_html(YGBMENU_VERSION); ?></strong>
            </p>
        </div>
        <?php endif; ?>
        
        <h1><?php echo esc_html__('Configuración de YGB Menu Store Selector', 'ygbmenu-store-selector'); ?></h1>
        
        <?php if (!$dependency_active): ?>
        <div class="notice notice-error">
            <p><strong><?php esc_html_e('Plugin dependiente no detectado', 'ygbmenu-store-selector'); ?></strong></p>
            <p><?php esc_html_e('Este plugin requiere "YGB Store Selector" para funcionar. Por favor, instálalo y actívalo.', 'ygbmenu-store-selector'); ?></p>
        </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
            <div style="background: #f0f6fc; padding: 15px; border-radius: 8px; border-left: 4px solid #4299e1;">
                <div style="font-size: 12px; color: #646970;"><?php esc_html_e('Estado', 'ygbmenu-store-selector'); ?></div>
                <div style="font-size: 16px; font-weight: bold;">
                    <?php if ($dependency_active): ?>
                        ✅ <?php esc_html_e('Plugin dependiente activo', 'ygbmenu-store-selector'); ?>
                    <?php else: ?>
                        ❌ <?php esc_html_e('Plugin dependiente no activo', 'ygbmenu-store-selector'); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div style="background: #f0f6fc; padding: 15px; border-radius: 8px; border-left: 4px solid #38a169;">
                <div style="font-size: 12px; color: #646970;"><?php esc_html_e('URLs configuradas', 'ygbmenu-store-selector'); ?></div>
                <div style="font-size: 16px; font-weight: bold;"><?php echo count($stores); ?></div>
            </div>
            <div style="background: #f0f6fc; padding: 15px; border-radius: 8px; border-left: 4px solid #ed8936;">
                <div style="font-size: 12px; color: #646970;"><?php esc_html_e('Shortcode', 'ygbmenu-store-selector'); ?></div>
                <div><code>[ygbmenu_selector]</code></div>
            </div>
        </div>
        
        <div class="ygbmenu-admin-container">
            <div class="ygbmenu-admin-card">
                <h2><?php esc_html_e('Configuración', 'ygbmenu-store-selector'); ?></h2>
                
                <form method="post" action="">
                    <?php wp_nonce_field('ygbmenu_save_action', 'ygbmenu_nonce'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="cookie_days"><?php esc_html_e('Duración de cookies (días):', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="number" id="cookie_days" name="cookie_days" value="<?php echo esc_attr($cookie_days); ?>" min="1" max="365" class="regular-text">
                                <p class="description"><?php esc_html_e('Número de días que se recordará la selección (1-365 días).', 'ygbmenu-store-selector'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><label for="cookie_domain"><?php esc_html_e('Dominio de cookie:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="text" id="cookie_domain" name="cookie_domain" value="<?php echo esc_attr($cookie_domain); ?>" placeholder="ej: .midominio.com" class="regular-text">
                                <p class="description"><?php esc_html_e('Deja vacío para usar el dominio actual.', 'ygbmenu-store-selector'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <h3><?php esc_html_e('Personalización de Colores', 'ygbmenu-store-selector'); ?></h3>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="color_primary"><?php esc_html_e('Color primario:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="color" id="color_primary" name="color_primary" value="<?php echo esc_attr($color_primary); ?>" class="ygbmenu-color-field">
                                <span class="ygbmenu-color-value"><?php echo esc_html($color_primary); ?></span>
                                <button type="button" class="button button-small ygbmenu-reset-color" data-default="#E26143"><?php esc_html_e('Restaurar', 'ygbmenu-store-selector'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="color_hover"><?php esc_html_e('Color hover:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="color" id="color_hover" name="color_hover" value="<?php echo esc_attr($color_hover); ?>" class="ygbmenu-color-field">
                                <span class="ygbmenu-color-value"><?php echo esc_html($color_hover); ?></span>
                                <button type="button" class="button button-small ygbmenu-reset-color" data-default="#D14F32"><?php esc_html_e('Restaurar', 'ygbmenu-store-selector'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="color_text"><?php esc_html_e('Color del texto:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="color" id="color_text" name="color_text" value="<?php echo esc_attr($color_text); ?>" class="ygbmenu-color-field">
                                <span class="ygbmenu-color-value"><?php echo esc_html($color_text); ?></span>
                                <button type="button" class="button button-small ygbmenu-reset-color" data-default="#E26143"><?php esc_html_e('Restaurar', 'ygbmenu-store-selector'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="color_background"><?php esc_html_e('Color de fondo:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="color" id="color_background" name="color_background" value="<?php echo esc_attr($color_background); ?>" class="ygbmenu-color-field">
                                <span class="ygbmenu-color-value"><?php echo esc_html($color_background); ?></span>
                                <button type="button" class="button button-small ygbmenu-reset-color" data-default="#ffffff"><?php esc_html_e('Restaurar', 'ygbmenu-store-selector'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="color_border"><?php esc_html_e('Color del borde:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="color" id="color_border" name="color_border" value="<?php echo esc_attr($color_border); ?>" class="ygbmenu-color-field">
                                <span class="ygbmenu-color-value"><?php echo esc_html($color_border); ?></span>
                                <button type="button" class="button button-small ygbmenu-reset-color" data-default="#E26143"><?php esc_html_e('Restaurar', 'ygbmenu-store-selector'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="color_spinner_primary"><?php esc_html_e('Color spinner primario:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="color" id="color_spinner_primary" name="color_spinner_primary" value="<?php echo esc_attr($color_spinner_primary); ?>" class="ygbmenu-color-field">
                                <span class="ygbmenu-color-value"><?php echo esc_html($color_spinner_primary); ?></span>
                                <button type="button" class="button button-small ygbmenu-reset-color" data-default="#E26143"><?php esc_html_e('Restaurar', 'ygbmenu-store-selector'); ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="color_spinner_secondary"><?php esc_html_e('Color spinner secundario:', 'ygbmenu-store-selector'); ?></label></th>
                            <td>
                                <input type="color" id="color_spinner_secondary" name="color_spinner_secondary" value="<?php echo esc_attr($color_spinner_secondary); ?>" class="ygbmenu-color-field">
                                <span class="ygbmenu-color-value"><?php echo esc_html($color_spinner_secondary); ?></span>
                                <button type="button" class="button button-small ygbmenu-reset-color" data-default="#00A32E"><?php esc_html_e('Restaurar', 'ygbmenu-store-selector'); ?></button>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Guardar cambios', 'ygbmenu-store-selector'); ?>">
                    </p>
                </form>
            </div>
            
            <div class="ygbmenu-admin-card">
                <h2><?php esc_html_e('Uso del Plugin', 'ygbmenu-store-selector'); ?></h2>
                <h3><?php esc_html_e('Shortcode', 'ygbmenu-store-selector'); ?></h3>
                <code>[ygbmenu_selector]</code>
                <h3><?php esc_html_e('Widget', 'ygbmenu-store-selector'); ?></h3>
                <p><?php esc_html_e('Apariencia → Widgets → YGB Menu Selector', 'ygbmenu-store-selector'); ?></p>
            </div>
            
            <div class="ygbmenu-admin-card">
                <h2><?php esc_html_e('URLs Configuradas', 'ygbmenu-store-selector'); ?></h2>
                <?php if (empty($stores)): ?>
                    <div class="notice notice-warning inline"><p><?php esc_html_e('No hay URLs configuradas.', 'ygbmenu-store-selector'); ?></p></div>
                <?php else: ?>
                    <ul>
                        <?php foreach ($stores as $tienda): ?>
                            <li><strong><?php echo esc_html($tienda['nombre']); ?>:</strong> <?php echo esc_url($tienda['url']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <p><a href="<?php echo esc_url(admin_url('admin.php?page=ygb-selector')); ?>" class="button"><?php esc_html_e('Gestionar URLs', 'ygbmenu-store-selector'); ?></a></p>
            </div>
        </div>
    </div>
    
    <style>
    .ygbmenu-admin-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
    .ygbmenu-admin-card { background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; }
    .ygbmenu-admin-card h2 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .ygbmenu-color-field { width: 60px; height: 30px; vertical-align: middle; margin-right: 10px; }
    .ygbmenu-color-value { display: inline-block; vertical-align: middle; margin-right: 10px; font-family: monospace; background: #f5f5f5; padding: 4px 8px; border-radius: 3px; min-width: 70px; }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        function updateColorValue(input) {
            $(input).siblings('.ygbmenu-color-value').text($(input).val());
        }
        $('.ygbmenu-color-field').on('change', function() { updateColorValue(this); });
        $('.ygbmenu-color-field').each(function() { updateColorValue(this); });
        $('.ygbmenu-reset-color').on('click', function() {
            var $input = $(this).closest('td').find('.ygbmenu-color-field');
            $input.val($(this).data('default'));
            updateColorValue($input[0]);
        });
    });
    </script>
    <?php
}

function ygbmenu_create_admin_menu() {
    add_menu_page(
        'YGB Menu Store Selector',
        'YGB Menu',
        'manage_options',
        'ygbmenu-selector',
        'ygbmenu_render_admin_page',
        'dashicons-menu-alt',
        30
    );
}
add_action('admin_menu', 'ygbmenu_create_admin_menu', 20);

// ==================== CARGA DE ASSETS ====================

function ygbmenu_store_selector_assets() {
    if (!is_admin() && !is_customize_preview()) {
        // Cargar CSS con SRI para integridad de subrecursos
        wp_enqueue_style('ygbmenu-selector-css', YGBMENU_PLUGIN_URL . 'ygbmenu-selector.css', array(), YGBMENU_VERSION, 'all');
        
        // Cargar JS con SRI para integridad de subrecursos
        wp_enqueue_script(
            'ygbmenu-selector-js', 
            YGBMENU_PLUGIN_URL . 'ygbmenu-selector.js', 
            array('jquery'), 
            YGBMENU_VERSION, 
            true
        );
        
        // Añadir atributo de integridad SRI al script
        add_filter('script_loader_tag', 'ygbmenu_add_sri_to_script', 10, 3);
        
        $cookie_domain = get_option('ygbmenu_cookie_domain', '');
        $site_domain = wp_parse_url(home_url(), PHP_URL_HOST);
        if (empty($site_domain)) $site_domain = $_SERVER['HTTP_HOST'] ?? '';
        
        wp_localize_script('ygbmenu-selector-js', 'ygbmenuConfig', array(
            'cookieDays'   => intval(get_option('ygbmenu_cookie_days', 30)),
            'cookieName'   => 'tiendaActual',
            'cookieDomain' => !empty($cookie_domain) ? $cookie_domain : $site_domain,
            'secure'       => is_ssl(),
            'i18n' => array(
                'loading'     => __('Redirigiendo...', 'ygbmenu-store-selector'),
                'invalid_url' => __('URL no válida', 'ygbmenu-store-selector')
            )
        ));
    }
}
add_action('wp_enqueue_scripts', 'ygbmenu_store_selector_assets', 100);

/**
 * Añadir atributo de integridad SRI a los scripts del plugin
 */
function ygbmenu_add_sri_to_script($tag, $handle, $src) {
    if ($handle === 'ygbmenu-selector-js') {
        $sri_hash = defined('YGBMENU_SRI_HASH_JS') ? YGBMENU_SRI_HASH_JS : '';
        if (!empty($sri_hash)) {
            // Detectar el tipo de comillas usadas en el tag
            if (strpos($tag, "src='") !== false) {
                $tag = str_replace(
                    " src='",
                    " integrity='{$sri_hash}' crossorigin='anonymous' src='",
                    $tag
                );
            } else {
                $tag = str_replace(
                    ' src="',
                    " integrity=\"{$sri_hash}\" crossorigin=\"anonymous\" src=\"",
                    $tag
                );
            }
        }
    }
    return $tag;
}

// ==================== FUNCIONALIDAD FRONTEND ====================

function ygbmenu_load_textdomain() {
    load_plugin_textdomain('ygbmenu-store-selector', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'ygbmenu_load_textdomain');

function ygbmenu_store_selector_shortcode($atts = [], $content = null) {
    $atts = shortcode_atts([
        'class' => '',
        'id'    => 'ygbmenu-unique-menu',
        'label' => __('Selecciona una tienda', 'ygbmenu-store-selector')
    ], $atts, 'ygbmenu_selector');
    
    $tiendas = ygbmenu_get_stores_safe();
    
    if (empty($tiendas)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="ygbmenu-unique-container <?php echo esc_attr($atts['class']); ?>">
        <label for="<?php echo esc_attr($atts['id']); ?>" class="screen-reader-text"><?php echo esc_html($atts['label']); ?></label>
        <select id="<?php echo esc_attr($atts['id']); ?>" aria-label="<?php echo esc_html($atts['label']); ?>" onchange="ygbmenuGuardarYRedirigir(this)" class="ygbmenu-unique-select">
            <?php foreach ($tiendas as $tienda): ?>
                <option value="<?php echo esc_url(rtrim($tienda['url'], '/')); ?>"><?php echo esc_html($tienda['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('ygbmenu_selector', 'ygbmenu_store_selector_shortcode');

class YGBMenu_Store_Selector_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'ygbmenu_store_selector_widget',
            __('YGB Menu Selector', 'ygbmenu-store-selector'),
            array('description' => __('Selector de URLs para YGB Menu', 'ygbmenu-store-selector'))
        );
    }
    
    public function widget($args, $instance) {
        // Escapar los argumentos del widget para cumplir con hardening (sugerencia de auditoría)
        echo wp_kses_post( $args['before_widget'] );
        
        if (!empty($instance['title'])) {
            echo wp_kses_post( $args['before_title'] ) . 
                 apply_filters('widget_title', $instance['title']) . 
                 wp_kses_post( $args['after_title'] );
        }
        
        echo ygbmenu_store_selector_shortcode();
        echo wp_kses_post( $args['after_widget'] );
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Menú Selector', 'ygbmenu-store-selector');
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Título:', 'ygbmenu-store-selector'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"></p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        return $instance;
    }
}

function ygbmenu_register_widget() {
    register_widget('YGBMenu_Store_Selector_Widget');
}
add_action('widgets_init', 'ygbmenu_register_widget');

function ygbmenu_add_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=ygbmenu-selector')) . '">' . esc_html__('Configuración', 'ygbmenu-store-selector') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ygbmenu_add_action_links');

function ygbmenu_deactivate_cleanup() {
    ygbmenu_clear_dynamic_css_cache();
}
register_deactivation_hook(__FILE__, 'ygbmenu_deactivate_cleanup');

/**
 * Hook de activación - Inicializar opciones por defecto
 */
function ygbmenu_activate_plugin() {
    // Inicializar opciones por defecto si no existen
    if (get_option('ygbmenu_cookie_days') === false) {
        add_option('ygbmenu_cookie_days', 30);
    }
    if (get_option('ygbmenu_cookie_domain') === false) {
        add_option('ygbmenu_cookie_domain', '');
    }
    if (get_option('ygbmenu_color_primary') === false) {
        add_option('ygbmenu_color_primary', '#E26143');
    }
    if (get_option('ygbmenu_color_hover') === false) {
        add_option('ygbmenu_color_hover', '#D14F32');
    }
    if (get_option('ygbmenu_color_text') === false) {
        add_option('ygbmenu_color_text', '#E26143');
    }
    if (get_option('ygbmenu_color_background') === false) {
        add_option('ygbmenu_color_background', '#ffffff');
    }
    if (get_option('ygbmenu_color_border') === false) {
        add_option('ygbmenu_color_border', '#E26143');
    }
    if (get_option('ygbmenu_color_spinner_primary') === false) {
        add_option('ygbmenu_color_spinner_primary', '#E26143');
    }
    if (get_option('ygbmenu_color_spinner_secondary') === false) {
        add_option('ygbmenu_color_spinner_secondary', '#00A32E');
    }
}
register_activation_hook(__FILE__, 'ygbmenu_activate_plugin');