<?php
/**
 * Desinstalación de YGB Menu Store Selector.
 *
 * Se ejecuta solo cuando el usuario elimina el plugin desde el administrador
 * de WordPress. Borra opciones, transients y entradas de object cache
 * asociadas al plugin.
 *
 * @package YGBMenu_Store_Selector
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

if ( ! current_user_can( 'activate_plugins' ) ) {
    exit;
}

global $wpdb;

// ==================== OPCIONES ====================

$ygbmenu_options = [
    'ygbmenu_cookie_days',
    'ygbmenu_cookie_domain',
    'ygbmenu_logo_max_width',
    'ygbmenu_logo_max_height',
    'ygbmenu_logo_max_width_mobile',
    'ygbmenu_logo_max_height_mobile',
    'ygbmenu_transparent_bg',
    'ygbmenu_text_size',
    'ygbmenu_text_size_mobile',
    'ygbmenu_text_bold',
    'ygbmenu_color_hover',
    'ygbmenu_color_text',
    'ygbmenu_color_background',
    'ygbmenu_color_border',
    'ygbmenu_color_spinner_primary',
    'ygbmenu_color_spinner_secondary',
    'ygbmenu_color_primary',
];

foreach ( $ygbmenu_options as $ygbmenu_option ) {
    delete_option( $ygbmenu_option );
}

// ==================== OBJECT CACHE EXTERNO ====================

$ygbmenu_cache_keys = [
    'ygbmenu_dynamic_css',
    'ygbmenu_dynamic_css_1.2.3',
    'ygbmenu_dynamic_css_1.2.4',
    'ygbmenu_dynamic_css_1.2.5',
    'ygbmenu_dynamic_css_1.2.6',
    'ygbmenu_dynamic_css_1.2.7',
    'ygbmenu_dynamic_css_1.2.8',
    'ygbmenu_dynamic_css_1.2.9',
    'ygbmenu_dynamic_css_1.3.0',
    'ygbmenu_dynamic_css_1.3.1',
    'ygbmenu_dynamic_css_1.3.2',
    'ygbmenu_dynamic_css_1.3.3',
    'ygbmenu_dynamic_css_1.3.4',
    'ygbmenu_dynamic_css_1.3.5',
    'ygbmenu_dynamic_css_1.3.6',
    'ygbmenu_dynamic_css_1.3.7',
    'ygbmenu_dynamic_css_1.3.8',
    'ygbmenu_dynamic_css_1.3.9',
    'ygbmenu_dynamic_css_1.4.0',
    'ygbmenu_dynamic_css_1.4.1',
    'ygbmenu_dynamic_css_1.4.2',
    'ygbmenu_dynamic_css_1.4.3',
    'ygbmenu_dynamic_css_1.4.4',
    'ygbmenu_dynamic_css_1.4.5',
    'ygbmenu_dynamic_css_1.4.6',
    'ygbmenu_dynamic_css_1.4.7',
    'ygbmenu_dynamic_css_1.4.8',
    'ygbmenu_dynamic_css_1.4.9',
    'ygbmenu_dynamic_css_1.5.0',
    'ygbmenu_dynamic_css_1.5.1',
    'ygbmenu_dynamic_css_1.5.2',
    'ygbmenu_dynamic_css_1.5.3',
];

foreach ( $ygbmenu_cache_keys as $ygbmenu_cache_key ) {
    wp_cache_delete( $ygbmenu_cache_key, 'ygbmenu' );
}

// ==================== TRANSIENTS (por prefijo) ====================

/*
 * No existe API de WordPress para borrar transients por prefijo. SQL directo
 * justificado. $wpdb->esc_like() escapa los guiones bajos del prefijo, que
 * son comodines en LIKE si no se escapan.
 */

$ygbmenu_like_transient         = $wpdb->esc_like( '_transient_ygbmenu_dynamic_css_' ) . '%';
$ygbmenu_like_transient_timeout = $wpdb->esc_like( '_transient_timeout_ygbmenu_dynamic_css_' ) . '%';

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
        $ygbmenu_like_transient,
        $ygbmenu_like_transient_timeout
    )
);
// phpcs:enable

// ==================== MULTISITE ====================

if ( is_multisite() ) {
    $ygbmenu_site_ids = get_sites(
        [
            'fields' => 'ids',
            'number' => 0,
        ]
    );

    if ( is_array( $ygbmenu_site_ids ) ) {
        foreach ( $ygbmenu_site_ids as $ygbmenu_site_id ) {
            switch_to_blog( (int) $ygbmenu_site_id );

            foreach ( $ygbmenu_options as $ygbmenu_option ) {
                delete_option( $ygbmenu_option );
            }

            // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                    $ygbmenu_like_transient,
                    $ygbmenu_like_transient_timeout
                )
            );
            // phpcs:enable

            restore_current_blog();
        }
    }
}