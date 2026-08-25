<?php
/**
 * WooCommerce hooks (stubs — filled in later phases).
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare WooCommerce compatibility and remove default wrappers if needed later.
 */
function hamta_woocommerce_setup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Placeholder for phase-specific WC customizations.
}
add_action( 'after_setup_theme', 'hamta_woocommerce_setup', 20 );
