<?php
/**
 * Product badge taxonomy (product_badge) — stub until phase 1.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register product_badge taxonomy when the feature flag is on.
 */
function hamta_register_product_badge_taxonomy() {
	if ( ! hamta_feature( 'product_badges' ) || ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$labels = array(
		'name'          => __( 'بج‌های محصول', 'hamta-base' ),
		'singular_name' => __( 'بج محصول', 'hamta-base' ),
		'search_items'  => __( 'جستجوی بج', 'hamta-base' ),
		'all_items'     => __( 'همه بج‌ها', 'hamta-base' ),
		'edit_item'     => __( 'ویرایش بج', 'hamta-base' ),
		'update_item'   => __( 'به‌روزرسانی بج', 'hamta-base' ),
		'add_new_item'  => __( 'افزودن بج جدید', 'hamta-base' ),
		'new_item_name' => __( 'نام بج جدید', 'hamta-base' ),
		'menu_name'     => __( 'بج‌های محصول', 'hamta-base' ),
	);

	register_taxonomy(
		'product_badge',
		array( 'product' ),
		array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'product-badge' ),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'hamta_register_product_badge_taxonomy' );
