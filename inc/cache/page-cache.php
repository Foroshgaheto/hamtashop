<?php
/**
 * File-based page cache (stub — implemented in phase 8).
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether page cache is enabled for the current request.
 *
 * @return bool
 */
function hamta_page_cache_enabled() {
	return hamta_feature( 'page_cache' ) && ! is_user_logged_in();
}
