<?php
/**
 * Cache exclusion rules (cart, checkout, account, AJAX, gateways, webhooks).
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if the current request must bypass page cache.
 *
 * @return bool True when caching is forbidden.
 */
function hamta_should_bypass_cache() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return true;
	}

	if ( is_user_logged_in() ) {
		return true;
	}

	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		return true;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

	$excluded_patterns = array(
		'/wc-api/',
		'/wp-json/',
		'wc-ajax=',
		'zibal',
		'zarinpal',
		'melipayamak',
		'torob',
	);

	foreach ( $excluded_patterns as $pattern ) {
		if ( false !== stripos( $request_uri, $pattern ) ) {
			return true;
		}
	}

	/**
	 * Filter cache bypass decision.
	 *
	 * @param bool $bypass Whether to bypass cache.
	 */
	return (bool) apply_filters( 'hamta_should_bypass_cache', false );
}
