<?php
/**
 * License Manager — phase 1 stub (manual key + remote API validation).
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Hamta_License_Manager
 */
class Hamta_License_Manager {

	const API_BASE = 'https://updates.hamtaweb.com/api/v1/license';

	/**
	 * Validate a license key against the central API.
	 *
	 * @param string $license_key License key.
	 * @param string $addon_slug  Add-on slug.
	 * @return array{valid:bool,message:string,data?:array}
	 */
	public function validate( $license_key, $addon_slug = 'hamta-base' ) {
		$license_key = sanitize_text_field( $license_key );
		$addon_slug  = sanitize_key( $addon_slug );

		if ( '' === $license_key ) {
			return array(
				'valid'   => false,
				'message' => __( 'کد فعال‌سازی خالی است.', 'hamta-base' ),
			);
		}

		// Remote call implemented when updates.hamtaweb.com is live.
		$response = array(
			'valid'   => false,
			'message' => __( 'سرویس اعتبارسنجی لایسنس هنوز فعال نشده است.', 'hamta-base' ),
		);

		/**
		 * Filter license validation result (for testing / future API).
		 *
		 * @param array  $response     Validation payload.
		 * @param string $license_key  License key.
		 * @param string $addon_slug   Add-on slug.
		 */
		return apply_filters( 'hamta_license_validate', $response, $license_key, $addon_slug );
	}

	/**
	 * Whether an add-on is marked active locally after successful validation.
	 *
	 * @param string $addon_slug Add-on slug.
	 * @return bool
	 */
	public function is_active( $addon_slug ) {
		$flags = get_option( 'hamta_license_flags', array() );
		return ! empty( $flags[ sanitize_key( $addon_slug ) ] );
	}
}
