<?php
/**
 * Theme supports, menus, and base setup.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and menus.
 */
function hamta_theme_setup() {
	load_theme_textdomain( 'hamta-base', HAMTA_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'منوی اصلی', 'hamta-base' ),
			'footer'  => __( 'منوی فوتر', 'hamta-base' ),
		)
	);
}
add_action( 'after_setup_theme', 'hamta_theme_setup' );

/**
 * Load feature flags helper.
 *
 * @param string $flag Feature key from theme-config.php.
 * @return bool
 */
function hamta_feature( $flag ) {
	static $config = null;

	if ( null === $config ) {
		$config = include HAMTA_THEME_DIR . '/theme-config.php';
	}

	return ! empty( $config[ $flag ] );
}
