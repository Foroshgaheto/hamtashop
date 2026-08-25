<?php
/**
 * Per-project feature flags.
 *
 * Toggle features on/off without removing code. Customer forks can override
 * these values as needed.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'product_badges'       => true,
	'price_history'        => false,
	'inquiry_product_type' => false,
	'otp_login'            => false,
	'google_login'         => false,
	'page_cache'           => false,
	'license_manager'      => false,
	'compare'              => false,
	'quick_view'           => false,
);
