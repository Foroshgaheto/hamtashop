<?php
/**
 * WooCommerce hooks for product card loop.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme WC supports (gallery kept for single product phases).
 */
function hamta_woocommerce_setup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
}
add_action( 'after_setup_theme', 'hamta_woocommerce_setup', 20 );

/**
 * Add Hamta grid class to products loop list.
 *
 * @param string $html Loop start HTML.
 * @return string
 */
function hamta_wc_product_loop_start( $html ) {
	return str_replace( 'class="products', 'class="products hamta-product-card-grid', $html );
}
add_filter( 'woocommerce_product_loop_start', 'hamta_wc_product_loop_start' );

/**
 * Replace default WC loop item chrome — our content-product.php owns the markup.
 */
function hamta_wc_loop_product_card_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}
add_action( 'wp', 'hamta_wc_loop_product_card_hooks' );
