<?php
/**
 * Archivo de desinstalación para YGB Menu Store Selector
 * Elimina todas las opciones del plugin al ser eliminado
 */

// Verificar que se está ejecutando desde WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Eliminar todas las opciones del plugin
delete_option('ygbmenu_cookie_days');
delete_option('ygbmenu_cookie_domain');
delete_option('ygbmenu_color_primary');
delete_option('ygbmenu_color_hover');
delete_option('ygbmenu_color_text');
delete_option('ygbmenu_color_background');
delete_option('ygbmenu_color_border');
delete_option('ygbmenu_color_spinner_primary');
delete_option('ygbmenu_color_spinner_secondary');

// Limpiar caché de CSS dinámico
delete_transient('ygbmenu_dynamic_css');

// Si se usa object cache externo
if (function_exists('wp_cache_delete')) {
    wp_cache_delete('ygbmenu_dynamic_css', 'ygbmenu');
}
