<?php
/**
 * WooCommerce loop product template — uses Hamta product card.
 *
 * @package Hamta_Base
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

get_template_part( 'template-parts/product', 'card', array( 'product' => $product ) );
