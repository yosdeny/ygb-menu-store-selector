<?php
/**
 * Plugin Name: YGB Menu Store Selector
 * Plugin URI: https://github.com/yosdeny
 * Description: Selector desplegable de url para redirección con persistencia mediante cookies. Shortcode: [ygbmenu_selector]. Depende del Plugin (YGB Store Selector) para crear y gestionar las URLs.
 * Version: 1.5.3
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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'YGBMENU_VERSION', '1.5.3' );
define( 'YGBMENU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'YGBMENU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'YGBMENU_PLUGIN_NAME', 'YGB Menu Store Selector' );

// ==================== DEPENDENCIAS ====================

function ygbmenu_is_dependency_active(): bool {
    if ( function_exists( 'ygb_selector_get_stores' ) ) {
        return true;
    }
    $stores = get_option( 'ygb_selector_stores', null );
    if ( null !== $stores ) {
        return true;
    }
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if ( function_exists( 'is_plugin_active' )
        && is_plugin_active( 'ygb-store-selector/ygb-store-selector.php' ) ) {
        return true;
    }
    return false;
}

function ygbmenu_get_stores_safe(): array {
    $tiendas = get_option( 'ygb_selector_stores', [] );
    if ( ! is_array( $tiendas ) ) {
        return [];
    }
    return array_values(
        array_filter(
            $tiendas,
            static function ( $tienda ): bool {
                return is_array( $tienda )
                    && ! empty( $tienda['url'] )
                    && ! empty( $tienda['nombre'] );
            }
        )
    );
}

function ygbmenu_dependency_notice(): void {
    if ( ygbmenu_is_dependency_active() ) {
        return;
    }
    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <strong>YGB Menu Store Selector:</strong>
            <?php esc_html_e( 'Este plugin requiere que el plugin "YGB Store Selector" esté instalado y activo para funcionar correctamente. Las URLs no se mostrarán hasta que lo actives.', 'ygbmenu-store-selector' ); ?>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'ygbmenu_dependency_notice' );

// ==================== UTILIDADES ====================

function ygbmenu_hex2rgb_safe( string $hex ): string {
    $hex = ltrim( $hex, '#' );
    if ( ! preg_match( '/^[0-9a-fA-F]{3}$|^[0-9a-fA-F]{6}$/', $hex ) ) {
        return '226, 97, 67';
    }
    if ( 3 === strlen( $hex ) ) {
        $r = hexdec( $hex[0] . $hex[0] );
        $g = hexdec( $hex[1] . $hex[1] );
        $b = hexdec( $hex[2] . $hex[2] );
    } else {
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
    }
    return "{$r}, {$g}, {$b}";
}

function ygbmenu_escape_svg_for_css( string $svg_content ): string {
    $svg_content = (string) preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $svg_content );
    $svg_content = (string) preg_replace( '/on\w+="[^"]*"/i', '', $svg_content );
    $svg_content = (string) preg_replace( '/javascript:/i', '', $svg_content );
    return 'url("data:image/svg+xml;charset=utf-8,' . rawurlencode( $svg_content ) . '")';
}

// ==================== CSS DINÁMICO ====================

function ygbmenu_dynamic_css_key(): string {
    return 'ygbmenu_dynamic_css_' . YGBMENU_VERSION;
}

function ygbmenu_clear_dynamic_css_cache(): void {
    $key = ygbmenu_dynamic_css_key();
    if ( wp_using_ext_object_cache() ) {
        wp_cache_delete( $key, 'ygbmenu' );
    } else {
        delete_transient( $key );
    }
}

function ygbmenu_get_dynamic_css(): string {
    $key        = ygbmenu_dynamic_css_key();
    $cached_css = wp_using_ext_object_cache()
        ? wp_cache_get( $key, 'ygbmenu' )
        : get_transient( $key );

    if ( is_string( $cached_css ) && '' !== $cached_css ) {
        return $cached_css;
    }

    $defaults = [
        'color_hover'             => '#D14F32',
        'color_text'              => '#E26143',
        'color_background'        => '#ffffff',
        'color_border'            => '#E26143',
        'color_spinner_primary'   => '#E26143',
        'color_spinner_secondary' => '#00A32E',
    ];

    $colors = [];
    foreach ( $defaults as $key_name => $default ) {
        $value = (string) get_option( 'ygbmenu_' . $key_name, $default );
        $colors[ $key_name ] = preg_match( '/^#[a-f0-9]{6}$/i', $value ) ? $value : $default;
    }

    $color_hover             = $colors['color_hover'];
    $color_text              = $colors['color_text'];
    $color_background        = $colors['color_background'];
    $color_border            = $colors['color_border'];
    $color_spinner_primary   = $colors['color_spinner_primary'];
    $color_spinner_secondary = $colors['color_spinner_secondary'];

    $text_size        = (int) get_option( 'ygbmenu_text_size', 20 );
    $text_size        = max( 10, min( 40, $text_size ) );

    $text_size_mobile = (int) get_option( 'ygbmenu_text_size_mobile', 13 );
    $text_size_mobile = max( 10, min( 40, $text_size_mobile ) );

    $text_bold       = (int) get_option( 'ygbmenu_text_bold', 0 );
    $text_weight     = $text_bold ? '600' : '400';
    $text_weight_css = 'font-weight: ' . $text_weight . ' !important;';

    $svg_location = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"><path d="M8 0C4.7 0 2 2.7 2 6c0 1.5 0.6 2.9 1.5 4l4.5 6 4.5-6c0.9-1.1 1.5-2.5 1.5-4 0-3.3-2.7-6-6-6zm0 8.5c-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5z" fill="' . esc_attr( $color_text ) . '"/></svg>';
    $svg_arrow    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 10" width="16" height="10"><path fill="' . esc_attr( $color_text ) . '" d="M0 0l8 10 8-10z"/></svg>';
    $svg_spinner  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke="' . esc_attr( $color_spinner_secondary ) . '" stroke-width="2" fill="none"/><circle cx="12" cy="12" r="8" stroke="' . esc_attr( $color_spinner_primary ) . '" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="1 20"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></circle></svg>';

    $rgb_hover = ygbmenu_hex2rgb_safe( $color_hover );

    $transparent_bg = (int) get_option( 'ygbmenu_transparent_bg', 0 );
    $bg_css = $transparent_bg
        ? 'background-color: transparent !important; border: none !important; box-shadow: none !important;'
        : 'background-color: ' . esc_attr( $color_background ) . ' !important; border-color: ' . esc_attr( $color_border ) . ' !important;';

    $css = '
    .ygbmenu-unique-select {
        color: ' . esc_attr( $color_text ) . ' !important;
        ' . $bg_css . '
        font-size: ' . $text_size . 'px !important;
        ' . $text_weight_css . '
        padding-left: 22px !important;
        padding-right: 12px !important;
        background-image: ' . ygbmenu_escape_svg_for_css( $svg_location ) . ', ' . ygbmenu_escape_svg_for_css( $svg_arrow ) . ' !important;
        background-position: left 2px center, right 2px center !important;
        background-size: 16px, 8px !important;
        background-repeat: no-repeat, no-repeat !important;
        text-align: left;
    }
    .ygbmenu-unique-select:focus {
        border-color: ' . esc_attr( $color_hover ) . ' !important;
        box-shadow: none !important;
        outline: none !important;
    }
    .ygbmenu-unique-select:hover {
        border-color: ' . esc_attr( $color_hover ) . ' !important;
        box-shadow: 0 0 0 2px rgba(' . esc_attr( $rgb_hover ) . ', 0.3) !important;
    }
    .ygbmenu-unique-select:focus-visible {
        outline: none !important;
        box-shadow: none !important;
    }
    .ygbmenu-unique-select.loading-state {
        background-image: ' . ygbmenu_escape_svg_for_css( $svg_location ) . ', ' . ygbmenu_escape_svg_for_css( $svg_spinner ) . ' !important;
        background-position: left 2px center, right 2px center !important;
        background-size: 16px, 20px !important;
    }

    /* ====== LOGO + SELECTOR ====== */
    .ygbmenu-with-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        width: var(--ygbmenu-logo-w, 185px);
        width: max(var(--ygbmenu-logo-w, 185px), var(--ygbmenu-select-w, 0px));
        max-width: 100%;
        margin: 0 auto !important;
        padding: 2px 0 !important;
        box-sizing: border-box;
    }
    .ygbmenu-with-logo .custom-logo-link {
        display: block !important;
        max-width: var(--ygbmenu-logo-w, 185px) !important;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 0 !important;
    }
    .ygbmenu-with-logo .custom-logo {
        display: block !important;
        max-width: var(--ygbmenu-logo-w, 185px) !important;
        max-height: var(--ygbmenu-logo-h, none) !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .ygbmenu-with-logo .ygbmenu-unique-select {
        max-width: 100% !important;
        margin: 0 auto !important;
        box-sizing: border-box;
    }

    section#block-9 {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 0 !important;
        height: auto !important;
        display: block !important;
        line-height: 0 !important;
    }
    section#block-9 > .ygbmenu-with-logo {
        margin: 0 auto !important;
        padding: 2px 0 !important;
        line-height: normal !important;
    }

    @media (max-width: 768px) {
        .ygbmenu-with-logo {
            width: var(--ygbmenu-logo-w-mobile, 120px);
            width: max(var(--ygbmenu-logo-w-mobile, 120px), var(--ygbmenu-select-w, 0px)) !important;
            max-width: 100% !important;
            overflow: visible !important;
        }
        .ygbmenu-with-logo .custom-logo-link {
            max-width: var(--ygbmenu-logo-w-mobile, 120px) !important;
        }
        .ygbmenu-with-logo .custom-logo {
            max-width: var(--ygbmenu-logo-w-mobile, 120px) !important;
            max-height: var(--ygbmenu-logo-h-mobile, none) !important;
        }
        .ygbmenu-with-logo .ygbmenu-unique-select {
            max-width: 100% !important;
        }
        .ygbmenu-unique-select {
            font-size: ' . $text_size_mobile . 'px !important;
            padding-left: 20px !important;
            padding-right: 10px !important;
            background-position: left 2px center, right 2px center !important;
            background-size: 14px, 6px !important;
        }
        .ygbmenu-unique-select.loading-state {
            background-size: 14px, 18px !important;
        }
    }';

    if ( wp_using_ext_object_cache() ) {
        wp_cache_set( $key, $css, 'ygbmenu', DAY_IN_SECONDS );
    } else {
        set_transient( $key, $css, DAY_IN_SECONDS );
    }

    return $css;
}

function ygbmenu_dynamic_css(): void {
    if ( is_admin() || is_customize_preview() ) {
        return;
    }
    $css = ygbmenu_get_dynamic_css();
    if ( '' !== $css ) {
        echo '<style id="ygbmenu-custom-colors">' . $css . '</style>';
    }
}
add_action( 'wp_head', 'ygbmenu_dynamic_css', 999 );
add_action( 'astra_head', 'ygbmenu_dynamic_css', 999 );

// ==================== ADMIN ====================

function ygbmenu_is_ygbplugin_active(): bool {
    return function_exists( 'ygbplugin_register' ) || class_exists( 'YGBPlugin_System' );
}

function ygbmenu_save_settings(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( ! isset( $_POST['ygbmenu_nonce'] ) ) {
        return;
    }
    $nonce = sanitize_text_field( wp_unslash( $_POST['ygbmenu_nonce'] ) );
    if ( ! wp_verify_nonce( $nonce, 'ygbmenu_save_action' ) ) {
        return;
    }
    if ( ! isset( $_POST['submit'] ) ) {
        return;
    }

    $cookie_days = isset( $_POST['cookie_days'] ) ? absint( wp_unslash( $_POST['cookie_days'] ) ) : 30;
    $cookie_days = max( 1, min( 365, $cookie_days ) );

    $cookie_domain = isset( $_POST['cookie_domain'] )
        ? sanitize_text_field( wp_unslash( $_POST['cookie_domain'] ) )
        : '';

    if ( '' !== $cookie_domain ) {
        $candidate     = ltrim( $cookie_domain, '.' );
        $valid         = filter_var( $candidate, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME );
        $cookie_domain = ( false === $valid ) ? '' : '.' . $candidate;
    }

    $logo_width         = isset( $_POST['logo_max_width'] ) ? absint( wp_unslash( $_POST['logo_max_width'] ) ) : 185;
    $logo_width         = max( 40, min( 1200, $logo_width ) );

    $logo_height        = isset( $_POST['logo_max_height'] ) ? absint( wp_unslash( $_POST['logo_max_height'] ) ) : 0;
    $logo_height        = max( 0, min( 1200, $logo_height ) );

    $logo_width_mobile  = isset( $_POST['logo_max_width_mobile'] ) ? absint( wp_unslash( $_POST['logo_max_width_mobile'] ) ) : 120;
    $logo_width_mobile  = max( 40, min( 1200, $logo_width_mobile ) );

    $logo_height_mobile = isset( $_POST['logo_max_height_mobile'] ) ? absint( wp_unslash( $_POST['logo_max_height_mobile'] ) ) : 0;
    $logo_height_mobile = max( 0, min( 1200, $logo_height_mobile ) );

    $transparent_bg     = isset( $_POST['transparent_bg'] ) ? 1 : 0;

    $text_size          = isset( $_POST['text_size'] ) ? absint( wp_unslash( $_POST['text_size'] ) ) : 20;
    $text_size          = max( 10, min( 40, $text_size ) );

    $text_size_mobile   = isset( $_POST['text_size_mobile'] ) ? absint( wp_unslash( $_POST['text_size_mobile'] ) ) : 13;
    $text_size_mobile   = max( 10, min( 40, $text_size_mobile ) );

    $text_bold          = isset( $_POST['text_bold'] ) ? 1 : 0;

    $color_defaults = [
        'color_hover'             => '#D14F32',
        'color_text'              => '#E26143',
        'color_background'        => '#ffffff',
        'color_border'            => '#E26143',
        'color_spinner_primary'   => '#E26143',
        'color_spinner_secondary' => '#00A32E',
    ];

    $colors = [];
    foreach ( $color_defaults as $key => $default ) {
        $raw            = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
        $safe           = sanitize_hex_color( is_string( $raw ) ? $raw : '' );
        $colors[ $key ] = ( is_string( $safe ) && '' !== $safe ) ? $safe : $default;
    }

    update_option( 'ygbmenu_cookie_days', $cookie_days );
    update_option( 'ygbmenu_cookie_domain', $cookie_domain );
    update_option( 'ygbmenu_logo_max_width', $logo_width );
    update_option( 'ygbmenu_logo_max_height', $logo_height );
    update_option( 'ygbmenu_logo_max_width_mobile', $logo_width_mobile );
    update_option( 'ygbmenu_logo_max_height_mobile', $logo_height_mobile );
    update_option( 'ygbmenu_transparent_bg', $transparent_bg );
    update_option( 'ygbmenu_text_size', $text_size );
    update_option( 'ygbmenu_text_size_mobile', $text_size_mobile );
    update_option( 'ygbmenu_text_bold', $text_bold );

    foreach ( $colors as $key => $value ) {
        update_option( 'ygbmenu_' . $key, $value );
    }

    delete_option( 'ygbmenu_color_primary' );

    ygbmenu_clear_dynamic_css_cache();

    add_settings_error(
        'ygbmenu_messages',
        'ygbmenu_message',
        __( 'Configuración guardada correctamente.', 'ygbmenu-store-selector' ),
        'success'
    );
}
add_action( 'admin_init', 'ygbmenu_save_settings' );

function ygbmenu_render_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'No tienes permisos suficientes.', 'ygbmenu-store-selector' ) );
    }

    settings_errors( 'ygbmenu_messages' );

    $cookie_days        = (int) get_option( 'ygbmenu_cookie_days', 30 );
    $cookie_domain      = (string) get_option( 'ygbmenu_cookie_domain', '' );
    $logo_width         = (int) get_option( 'ygbmenu_logo_max_width', 185 );
    $logo_height        = (int) get_option( 'ygbmenu_logo_max_height', 0 );
    $logo_width_mobile  = (int) get_option( 'ygbmenu_logo_max_width_mobile', 120 );
    $logo_height_mobile = (int) get_option( 'ygbmenu_logo_max_height_mobile', 0 );
    $transparent_bg     = (int) get_option( 'ygbmenu_transparent_bg', 0 );
    $text_size          = (int) get_option( 'ygbmenu_text_size', 20 );
    $text_size_mobile   = (int) get_option( 'ygbmenu_text_size_mobile', 13 );
    $text_bold          = (int) get_option( 'ygbmenu_text_bold', 0 );

    $color_hover             = (string) get_option( 'ygbmenu_color_hover', '#D14F32' );
    $color_text              = (string) get_option( 'ygbmenu_color_text', '#E26143' );
    $color_background        = (string) get_option( 'ygbmenu_color_background', '#ffffff' );
    $color_border            = (string) get_option( 'ygbmenu_color_border', '#E26143' );
    $color_spinner_primary   = (string) get_option( 'ygbmenu_color_spinner_primary', '#E26143' );
    $color_spinner_secondary = (string) get_option( 'ygbmenu_color_spinner_secondary', '#00A32E' );

    $dependency_active = ygbmenu_is_dependency_active();
    $stores            = ygbmenu_get_stores_safe();
    $show_breadcrumb   = ygbmenu_is_ygbplugin_active();
    ?>
    <div class="wrap ygbmenu-admin-wrap">
        <?php if ( $show_breadcrumb ) : ?>
            <div style="margin: 0 0 20px 0; padding: 10px 15px; background: #f0f6fc; border-radius: 6px; border-left: 4px solid #4299e1;">
                <p style="margin: 0; font-size: 14px; display: flex; align-items: center;">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=ygbplugin-dashboard' ) ); ?>" style="text-decoration: none; color: #2271b1;">
                        <span class="dashicons dashicons-admin-site-alt3" style="margin-right: 8px;"></span>YGBPlugin Dashboard
                    </a>
                    <span style="margin: 0 15px; color: #8c8f94;">›</span>
                    <strong>YGB Menu Store Selector v<?php echo esc_html( YGBMENU_VERSION ); ?></strong>
                </p>
            </div>
        <?php endif; ?>

        <h1><?php echo esc_html__( 'Configuración de YGB Menu Store Selector', 'ygbmenu-store-selector' ); ?></h1>

        <?php if ( ! $dependency_active ) : ?>
            <div class="notice notice-error">
                <p><strong><?php esc_html_e( 'Plugin dependiente no detectado', 'ygbmenu-store-selector' ); ?></strong></p>
                <p><?php esc_html_e( 'Este plugin requiere "YGB Store Selector" para funcionar. Por favor, instálalo y actívalo.', 'ygbmenu-store-selector' ); ?></p>
            </div>
        <?php endif; ?>

        <div class="ygbmenu-admin-container">
            <div class="ygbmenu-admin-card">
                <h2><?php esc_html_e( 'Configuración', 'ygbmenu-store-selector' ); ?></h2>

                <form method="post" action="">
                    <?php wp_nonce_field( 'ygbmenu_save_action', 'ygbmenu_nonce' ); ?>

                    <h3><?php esc_html_e( 'Logo y selector — escritorio', 'ygbmenu-store-selector' ); ?></h3>
                    <p class="description"><?php esc_html_e( 'El selector nunca será más ancho que el logo. Ajusta el ancho y alto máximos del logo en escritorio.', 'ygbmenu-store-selector' ); ?></p>

                    <table class="form-table">
                        <tr>
                            <th><label for="logo_max_width"><?php esc_html_e( 'Ancho máximo del logo (px):', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="number" id="logo_max_width" name="logo_max_width" value="<?php echo esc_attr( $logo_width ); ?>" min="40" max="1200" step="1" class="small-text">
                                <p class="description"><?php esc_html_e( 'Ancho de la columna logo+selector. Rango: 40–1200 px. Por defecto: 185.', 'ygbmenu-store-selector' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="logo_max_height"><?php esc_html_e( 'Alto máximo del logo (px):', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="number" id="logo_max_height" name="logo_max_height" value="<?php echo esc_attr( $logo_height ); ?>" min="0" max="1200" step="1" class="small-text">
                                <p class="description"><?php esc_html_e( '0 = sin límite de alto. Rango: 0–1200 px. Por defecto: 0.', 'ygbmenu-store-selector' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="transparent_bg"><?php esc_html_e( 'Fondo transparente:', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="transparent_bg" name="transparent_bg" value="1" <?php checked( 1, $transparent_bg ); ?>>
                                    <?php esc_html_e( 'Quitar fondo y borde del selector', 'ygbmenu-store-selector' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Logo y selector — móvil', 'ygbmenu-store-selector' ); ?></h3>
                    <p class="description"><?php esc_html_e( 'Aplica a pantallas de hasta 768 px de ancho.', 'ygbmenu-store-selector' ); ?></p>

                    <table class="form-table">
                        <tr>
                            <th><label for="logo_max_width_mobile"><?php esc_html_e( 'Ancho máximo del logo en móvil (px):', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="number" id="logo_max_width_mobile" name="logo_max_width_mobile" value="<?php echo esc_attr( $logo_width_mobile ); ?>" min="40" max="1200" step="1" class="small-text">
                                <p class="description"><?php esc_html_e( 'Rango: 40–1200 px. Por defecto: 120.', 'ygbmenu-store-selector' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="logo_max_height_mobile"><?php esc_html_e( 'Alto máximo del logo en móvil (px):', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="number" id="logo_max_height_mobile" name="logo_max_height_mobile" value="<?php echo esc_attr( $logo_height_mobile ); ?>" min="0" max="1200" step="1" class="small-text">
                                <p class="description"><?php esc_html_e( '0 = sin límite de alto. Rango: 0–1200 px. Por defecto: 0.', 'ygbmenu-store-selector' ); ?></p>
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Texto del selector', 'ygbmenu-store-selector' ); ?></h3>

                    <table class="form-table">
                        <tr>
                            <th><label for="text_size"><?php esc_html_e( 'Tamaño del texto en escritorio (px):', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="number" id="text_size" name="text_size" value="<?php echo esc_attr( $text_size ); ?>" min="10" max="40" step="1" class="small-text">
                                <p class="description"><?php esc_html_e( 'Rango: 10–40 px. Por defecto: 20.', 'ygbmenu-store-selector' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="text_size_mobile"><?php esc_html_e( 'Tamaño del texto en móvil (px):', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="number" id="text_size_mobile" name="text_size_mobile" value="<?php echo esc_attr( $text_size_mobile ); ?>" min="10" max="40" step="1" class="small-text">
                                <p class="description"><?php esc_html_e( 'Rango: 10–40 px. Por defecto: 13.', 'ygbmenu-store-selector' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="text_bold"><?php esc_html_e( 'Texto en negrita:', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="text_bold" name="text_bold" value="1" <?php checked( 1, $text_bold ); ?>>
                                    <?php esc_html_e( 'Mostrar el texto en seminegrita (font-weight: 600)', 'ygbmenu-store-selector' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Cookies', 'ygbmenu-store-selector' ); ?></h3>

                    <table class="form-table">
                        <tr>
                            <th><label for="cookie_days"><?php esc_html_e( 'Duración de cookies (días):', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="number" id="cookie_days" name="cookie_days" value="<?php echo esc_attr( $cookie_days ); ?>" min="1" max="365" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="cookie_domain"><?php esc_html_e( 'Dominio de cookie:', 'ygbmenu-store-selector' ); ?></label></th>
                            <td>
                                <input type="text" id="cookie_domain" name="cookie_domain" value="<?php echo esc_attr( $cookie_domain ); ?>" placeholder="ej: .midominio.com" class="regular-text">
                            </td>
                        </tr>
                    </table>

                    <h3><?php esc_html_e( 'Personalización de Colores', 'ygbmenu-store-selector' ); ?></h3>

                    <table class="form-table">
                        <?php
                        $color_fields = [
                            'color_hover'             => [ __( 'Color hover:', 'ygbmenu-store-selector' ), $color_hover, '#D14F32' ],
                            'color_text'              => [ __( 'Color del texto:', 'ygbmenu-store-selector' ), $color_text, '#E26143' ],
                            'color_background'        => [ __( 'Color de fondo:', 'ygbmenu-store-selector' ), $color_background, '#ffffff' ],
                            'color_border'            => [ __( 'Color del borde:', 'ygbmenu-store-selector' ), $color_border, '#E26143' ],
                            'color_spinner_primary'   => [ __( 'Color spinner primario:', 'ygbmenu-store-selector' ), $color_spinner_primary, '#E26143' ],
                            'color_spinner_secondary' => [ __( 'Color spinner secundario:', 'ygbmenu-store-selector' ), $color_spinner_secondary, '#00A32E' ],
                        ];
                        foreach ( $color_fields as $field_id => $field ) :
                            [ $label, $value, $default ] = $field;
                            ?>
                            <tr>
                                <th><label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label></th>
                                <td>
                                    <input type="color" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $value ); ?>" class="ygbmenu-color-field">
                                    <span class="ygbmenu-color-value"><?php echo esc_html( $value ); ?></span>
                                    <button type="button" class="button button-small ygbmenu-reset-color" data-default="<?php echo esc_attr( $default ); ?>"><?php esc_html_e( 'Restaurar', 'ygbmenu-store-selector' ); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <p class="submit">
                        <input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Guardar cambios', 'ygbmenu-store-selector' ); ?>">
                    </p>
                </form>
            </div>

            <div class="ygbmenu-admin-card">
                <h2><?php esc_html_e( 'URLs Configuradas', 'ygbmenu-store-selector' ); ?></h2>
                <?php if ( empty( $stores ) ) : ?>
                    <div class="notice notice-warning inline"><p><?php esc_html_e( 'No hay URLs configuradas.', 'ygbmenu-store-selector' ); ?></p></div>
                <?php else : ?>
                    <ul>
                        <?php foreach ( $stores as $tienda ) : ?>
                            <li><strong><?php echo esc_html( $tienda['nombre'] ); ?>:</strong> <?php echo esc_url( $tienda['url'] ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=ygb-selector' ) ); ?>" class="button"><?php esc_html_e( 'Gestionar URLs', 'ygbmenu-store-selector' ); ?></a></p>
            </div>
        </div>
    </div>

    <style>
    .ygbmenu-admin-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
    .ygbmenu-admin-card { background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; }
    .ygbmenu-admin-card h2 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    .ygbmenu-admin-card h3 { margin-top: 24px; }
    .ygbmenu-color-field { width: 60px; height: 30px; vertical-align: middle; margin-right: 10px; }
    .ygbmenu-color-value { display: inline-block; vertical-align: middle; margin-right: 10px; font-family: monospace; background: #f5f5f5; padding: 4px 8px; border-radius: 3px; min-width: 70px; }
    </style>

    <script>
    jQuery(function ($) {
        function updateColorValue(input) {
            $(input).siblings('.ygbmenu-color-value').text($(input).val());
        }
        $('.ygbmenu-color-field').on('change', function () { updateColorValue(this); });
        $('.ygbmenu-color-field').each(function () { updateColorValue(this); });
        $('.ygbmenu-reset-color').on('click', function () {
            var $input = $(this).closest('td').find('.ygbmenu-color-field');
            $input.val($(this).data('default'));
            updateColorValue($input[0]);
        });
    });
    </script>
    <?php
}

function ygbmenu_create_admin_menu(): void {
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
add_action( 'admin_menu', 'ygbmenu_create_admin_menu', 20 );

// ==================== ASSETS ====================

function ygbmenu_store_selector_assets(): void {
    if ( is_admin() ) {
        return;
    }

    wp_enqueue_style( 'ygbmenu-selector-css', YGBMENU_PLUGIN_URL . 'ygbmenu-selector.css', [], YGBMENU_VERSION, 'all' );
    wp_enqueue_script( 'ygbmenu-selector-js', YGBMENU_PLUGIN_URL . 'ygbmenu-selector.js', [ 'jquery' ], YGBMENU_VERSION, true );

    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
    $site_host = is_string( $site_host ) ? $site_host : '';

    $configured = ltrim( (string) get_option( 'ygbmenu_cookie_domain', '' ), '.' );

    $effective_domain = '';
    if ( '' !== $configured && '' !== $site_host ) {
        if ( $configured === $site_host || str_ends_with( $site_host, '.' . $configured ) ) {
            $effective_domain = '.' . $configured;
        }
    }

    wp_localize_script(
        'ygbmenu-selector-js',
        'ygbmenuConfig',
        [
            'cookieDays'   => (int) get_option( 'ygbmenu_cookie_days', 30 ),
            'cookieName'   => 'tiendaActual',
            'cookieDomain' => $effective_domain,
            'secure'       => is_ssl(),
            'i18n'         => [
                'loading'     => __( 'Redirigiendo', 'ygbmenu-store-selector' ),
                'invalid_url' => __( 'URL no válida', 'ygbmenu-store-selector' ),
            ],
        ]
    );
}
add_action( 'wp_enqueue_scripts', 'ygbmenu_store_selector_assets', 100 );

// ==================== FRONTEND ====================

function ygbmenu_load_textdomain(): void {
    load_plugin_textdomain( 'ygbmenu-store-selector', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'ygbmenu_load_textdomain' );

function ygbmenu_store_selector_shortcode( $atts = [], $content = null ): string {
    $atts = shortcode_atts(
        [
            'class' => '',
            'id'    => 'ygbmenu-unique-menu',
            'label' => __( 'Selecciona una tienda', 'ygbmenu-store-selector' ),
        ],
        $atts,
        'ygbmenu_selector'
    );

    $select_id = sanitize_key( (string) $atts['id'] );
    if ( '' === $select_id ) {
        $select_id = 'ygbmenu-unique-menu';
    }

    $class_parts = preg_split( '/\s+/', (string) $atts['class'], -1, PREG_SPLIT_NO_EMPTY );
    $class_parts = is_array( $class_parts ) ? $class_parts : [];
    $classes     = array_filter( array_map( 'sanitize_html_class', $class_parts ) );

    $tiendas = ygbmenu_get_stores_safe();
    if ( empty( $tiendas ) ) {
        return '';
    }

    $has_logo           = has_custom_logo();
    $logo_width         = (int) get_option( 'ygbmenu_logo_max_width', 185 );
    $logo_height        = (int) get_option( 'ygbmenu_logo_max_height', 0 );
    $logo_width_mobile  = (int) get_option( 'ygbmenu_logo_max_width_mobile', 120 );
    $logo_height_mobile = (int) get_option( 'ygbmenu_logo_max_height_mobile', 0 );

    $logo_width         = max( 40, min( 1200, $logo_width ) );
    $logo_height        = max( 0, min( 1200, $logo_height ) );
    $logo_width_mobile  = max( 40, min( 1200, $logo_width_mobile ) );
    $logo_height_mobile = max( 0, min( 1200, $logo_height_mobile ) );

    $container_classes = [ 'ygbmenu-unique-container' ];
    $container_style   = '';

    if ( $has_logo ) {
        $container_classes[] = 'ygbmenu-with-logo';

        $style_parts = [
            '--ygbmenu-logo-w: ' . $logo_width . 'px',
            '--ygbmenu-logo-h: ' . ( $logo_height > 0 ? $logo_height . 'px' : 'none' ),
            '--ygbmenu-logo-w-mobile: ' . $logo_width_mobile . 'px',
            '--ygbmenu-logo-h-mobile: ' . ( $logo_height_mobile > 0 ? $logo_height_mobile . 'px' : 'none' ),
        ];
        $container_style = ' style="' . esc_attr( implode( '; ', $style_parts ) ) . ';"';
    }

    $classes = array_merge( $container_classes, $classes );

    ob_start();
    ?>
    <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $container_style; ?>>
        <?php if ( $has_logo ) : ?>
            <?php the_custom_logo(); ?>
        <?php endif; ?>

        <label for="<?php echo esc_attr( $select_id ); ?>" class="screen-reader-text">
            <?php echo esc_html( $atts['label'] ); ?>
        </label>
        <select
            id="<?php echo esc_attr( $select_id ); ?>"
            aria-label="<?php echo esc_attr( $atts['label'] ); ?>"
            class="ygbmenu-unique-select"
            data-ygbmenu-selector="1"
        >
            <?php foreach ( $tiendas as $tienda ) : ?>
                <option value="<?php echo esc_url( rtrim( (string) $tienda['url'], '/' ) ); ?>">
                    <?php echo esc_html( $tienda['nombre'] ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
    return (string) ob_get_clean();
}
add_shortcode( 'ygbmenu_selector', 'ygbmenu_store_selector_shortcode' );

class YGBMenu_Store_Selector_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'ygbmenu_store_selector_widget',
            __( 'YGB Menu Selector', 'ygbmenu-store-selector' ),
            [ 'description' => __( 'Selector de URLs para YGB Menu', 'ygbmenu-store-selector' ) ]
        );
    }

    public function widget( $args, $instance ): void {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
            echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title'];
        }

        echo ygbmenu_store_selector_shortcode();

        echo $args['after_widget'];
    }

    public function form( $instance ): void {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Menú Selector', 'ygbmenu-store-selector' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Título:', 'ygbmenu-store-selector' ); ?>
            </label>
            <input
                class="widefat"
                id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                type="text"
                value="<?php echo esc_attr( $title ); ?>"
            >
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ): array {
        return [ 'title' => sanitize_text_field( $new_instance['title'] ?? '' ) ];
    }
}

function ygbmenu_register_widget(): void {
    register_widget( 'YGBMenu_Store_Selector_Widget' );
}
add_action( 'widgets_init', 'ygbmenu_register_widget' );

function ygbmenu_add_action_links( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=ygbmenu-selector' ) ) . '">'
        . esc_html__( 'Configuración', 'ygbmenu-store-selector' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ygbmenu_add_action_links' );

// ==================== ACTIVACIÓN / DESACTIVACIÓN ====================

function ygbmenu_activate_plugin(): void {
    $defaults = [
        'ygbmenu_cookie_days'               => 30,
        'ygbmenu_cookie_domain'             => '',
        'ygbmenu_logo_max_width'            => 185,
        'ygbmenu_logo_max_height'           => 0,
        'ygbmenu_logo_max_width_mobile'     => 120,
        'ygbmenu_logo_max_height_mobile'    => 0,
        'ygbmenu_transparent_bg'            => 0,
        'ygbmenu_text_size'                 => 20,
        'ygbmenu_text_size_mobile'          => 13,
        'ygbmenu_text_bold'                 => 0,
        'ygbmenu_color_hover'               => '#D14F32',
        'ygbmenu_color_text'                => '#E26143',
        'ygbmenu_color_background'          => '#ffffff',
        'ygbmenu_color_border'              => '#E26143',
        'ygbmenu_color_spinner_primary'     => '#E26143',
        'ygbmenu_color_spinner_secondary'   => '#00A32E',
    ];
    foreach ( $defaults as $option => $default ) {
        if ( false === get_option( $option, false ) ) {
            add_option( $option, $default );
        }
    }

    delete_option( 'ygbmenu_color_primary' );
}
register_activation_hook( __FILE__, 'ygbmenu_activate_plugin' );

function ygbmenu_deactivate_cleanup(): void {
    ygbmenu_clear_dynamic_css_cache();
}
register_deactivation_hook( __FILE__, 'ygbmenu_deactivate_cleanup' );