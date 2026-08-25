<?php
/**
 * Hamta Base Theme bootstrap.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HAMTA_THEME_VERSION', '0.1.0' );
define( 'HAMTA_THEME_DIR', get_template_directory() );
define( 'HAMTA_THEME_URI', get_template_directory_uri() );

require_once HAMTA_THEME_DIR . '/theme-config.php';
require_once HAMTA_THEME_DIR . '/inc/setup.php';
require_once HAMTA_THEME_DIR . '/inc/woocommerce-hooks.php';
require_once HAMTA_THEME_DIR . '/inc/settings-panel.php';
require_once HAMTA_THEME_DIR . '/inc/cache/page-cache.php';
require_once HAMTA_THEME_DIR . '/inc/cache/cache-exclusions.php';
require_once HAMTA_THEME_DIR . '/inc/license/license-manager.php';
require_once HAMTA_THEME_DIR . '/inc/badges/product-badge-taxonomy.php';

/**
 * Enqueue front-end assets (Vite build + Alpine.js).
 */
function hamta_enqueue_assets() {
	$css_file = HAMTA_THEME_DIR . '/assets/dist/main.css';
	$js_file  = HAMTA_THEME_DIR . '/assets/dist/main.js';

	if ( file_exists( $css_file ) ) {
		wp_enqueue_style(
			'hamta-main',
			HAMTA_THEME_URI . '/assets/dist/main.css',
			array(),
			(string) filemtime( $css_file )
		);
	}

	$script_deps = array();

	if ( file_exists( $js_file ) ) {
		wp_enqueue_script(
			'hamta-main',
			HAMTA_THEME_URI . '/assets/dist/main.js',
			array(),
			(string) filemtime( $js_file ),
			true
		);
		$script_deps[] = 'hamta-main';
	}

	wp_enqueue_script(
		'alpinejs',
		'https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js',
		$script_deps,
		'3.14.8',
		true
	);
	wp_script_add_data( 'alpinejs', 'defer', true );
}
add_action( 'wp_enqueue_scripts', 'hamta_enqueue_assets' );
