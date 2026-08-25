<?php
/**
 * Seed demo products for Phase 1 product card testing.
 * Run: php bin/seed-demo-products.php
 *
 * @package Hamta_Base
 */

if ( php_sapi_name() !== 'cli' ) {
	exit( "CLI only\n" );
}

// From: wp-content/themes/hamta-base-theme/bin -> up 4 = WordPress root
$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	fwrite( STDERR, "wp-load.php not found at {$wp_load}\n" );
	exit( 1 );
}

require_once $wp_load;

if ( ! class_exists( 'WooCommerce' ) ) {
	fwrite( STDERR, "WooCommerce is required.\n" );
	exit( 1 );
}

/**
 * Ensure a product_badge term exists.
 *
 * @param string $name  Name.
 * @param string $slug  Slug.
 * @param string $color Hex.
 * @return int Term ID.
 */
function hamta_seed_badge( $name, $slug, $color ) {
	$term = term_exists( $slug, 'product_badge' );
	if ( ! $term ) {
		$term = wp_insert_term( $name, 'product_badge', array( 'slug' => $slug ) );
	}
	$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
	update_term_meta( $term_id, 'hamta_badge_color', $color );
	return $term_id;
}

/**
 * Ensure pa_color attribute + terms.
 *
 * @return int Attribute taxonomy ready.
 */
function hamta_seed_color_attribute() {
	$attribute_id = wc_attribute_taxonomy_id_by_name( 'color' );
	if ( ! $attribute_id ) {
		$attribute_id = wc_create_attribute(
			array(
				'name'         => 'رنگ',
				'slug'         => 'color',
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			)
		);
		delete_transient( 'wc_attribute_taxonomies' );
		register_taxonomy(
			'pa_color',
			apply_filters( 'woocommerce_taxonomy_objects_pa_color', array( 'product' ) ),
			apply_filters(
				'woocommerce_taxonomy_args_pa_color',
				array(
					'labels'       => array( 'name' => 'رنگ' ),
					'hierarchical' => false,
					'show_ui'      => false,
					'query_var'    => true,
					'rewrite'      => false,
				)
			)
		);
	}

	$colors = array(
		'black' => array( 'سیاه', '#111827' ),
		'blue'  => array( 'آبی', '#2563EB' ),
		'red'   => array( 'قرمز', '#DC2626' ),
	);

	foreach ( $colors as $slug => $pair ) {
		$term = term_exists( $slug, 'pa_color' );
		if ( ! $term ) {
			$term = wp_insert_term( $pair[0], 'pa_color', array( 'slug' => $slug ) );
		}
		$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
		update_term_meta( $term_id, 'hamta_color_hex', $pair[1] );
	}

	return true;
}

/**
 * Create or update a simple product by slug.
 *
 * @param array $args Args.
 * @return WC_Product
 */
function hamta_seed_simple_product( $args ) {
	$existing = get_page_by_path( $args['slug'], OBJECT, 'product' );
	$product  = $existing ? wc_get_product( $existing->ID ) : new WC_Product_Simple();

	$product->set_name( $args['name'] );
	$product->set_slug( $args['slug'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_regular_price( $args['regular'] );
	if ( ! empty( $args['sale'] ) ) {
		$product->set_sale_price( $args['sale'] );
		$product->set_price( $args['sale'] );
	} else {
		$product->set_sale_price( '' );
		$product->set_price( $args['regular'] );
	}
	$product->set_manage_stock( true );
	$product->set_stock_quantity( isset( $args['stock'] ) ? $args['stock'] : 12 );
	$product->set_stock_status( 'instock' );
	$product->set_average_rating( isset( $args['rating'] ) ? $args['rating'] : 4.5 );
	$product->set_rating_counts( array( 5 => 8, 4 => 3 ) );
	$product->set_review_count( isset( $args['reviews'] ) ? $args['reviews'] : 11 );

	if ( ! empty( $args['sale_to'] ) ) {
		$product->set_date_on_sale_to( $args['sale_to'] );
		$product->set_date_on_sale_from( time() - DAY_IN_SECONDS );
	}

	$id = $product->save();

	if ( ! empty( $args['badges'] ) ) {
		wp_set_object_terms( $id, $args['badges'], 'product_badge' );
	}

	return wc_get_product( $id );
}

echo "Seeding badges...\n";
$badge_original = hamta_seed_badge( 'اصل', 'asl', '#16A34A' );
$badge_warranty = hamta_seed_badge( 'گارانتی', 'garanti', '#2563EB' );
$badge_install  = hamta_seed_badge( 'اقساطی', 'aghsati', '#D97706' );

echo "Seeding color attribute...\n";
hamta_seed_color_attribute();

echo "Seeding simple product...\n";
hamta_seed_simple_product(
	array(
		'name'    => 'هدفون بی‌سیم نمونه',
		'slug'    => 'hamta-demo-simple',
		'regular' => '1250000',
		'stock'   => 18,
		'rating'  => 4.2,
		'reviews' => 9,
	)
);

echo "Seeding sale + timer product...\n";
hamta_seed_simple_product(
	array(
		'name'    => 'ساعت هوشمند ویژه',
		'slug'    => 'hamta-demo-sale',
		'regular' => '4500000',
		'sale'    => '3590000',
		'stock'   => 7,
		'sale_to' => time() + ( 3 * DAY_IN_SECONDS ) + 3600,
		'rating'  => 4.8,
		'reviews' => 24,
	)
);

echo "Seeding badges product...\n";
hamta_seed_simple_product(
	array(
		'name'    => 'گوشی گارانتی‌دار',
		'slug'    => 'hamta-demo-badges',
		'regular' => '18900000',
		'sale'    => '17500000',
		'stock'   => 4,
		'badges'  => array( 'asl', 'garanti', 'aghsati' ),
		'rating'  => 4.6,
		'reviews' => 41,
	)
);

echo "Seeding variable color product...\n";
$existing_var = get_page_by_path( 'hamta-demo-variable', OBJECT, 'product' );
$variable     = $existing_var ? wc_get_product( $existing_var->ID ) : new WC_Product_Variable();
if ( ! $variable || ! $variable->is_type( 'variable' ) ) {
	// Convert / create fresh variable.
	if ( $existing_var ) {
		wp_delete_post( $existing_var->ID, true );
	}
	$variable = new WC_Product_Variable();
}

$variable->set_name( 'کفش ورزشی رنگی' );
$variable->set_slug( 'hamta-demo-variable' );
$variable->set_status( 'publish' );
$variable->set_catalog_visibility( 'visible' );
$variable->set_manage_stock( false );
$variable->set_stock_status( 'instock' );
$variable->set_average_rating( 4.0 );
$variable->set_review_count( 6 );

$attribute = new WC_Product_Attribute();
$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'color' ) );
$attribute->set_name( 'pa_color' );
$attribute->set_options( array( 'black', 'blue', 'red' ) );
$attribute->set_visible( true );
$attribute->set_variation( true );
$variable->set_attributes( array( $attribute ) );
$parent_id = $variable->save();
wp_set_object_terms( $parent_id, array( 'black', 'blue', 'red' ), 'pa_color' );

$prices = array(
	'black' => '2200000',
	'blue'  => '2350000',
	'red'   => '2290000',
);

foreach ( $prices as $slug => $price ) {
	$children = $variable->get_children();
	$found_id = 0;
	foreach ( $children as $child_id ) {
		$child = wc_get_product( $child_id );
		if ( $child && $child->get_attribute( 'pa_color' ) === $slug ) {
			$found_id = $child_id;
			break;
		}
		// get_attribute returns name; compare slug via meta.
		$attr_val = $child ? $child->get_meta( 'attribute_pa_color' ) : '';
		if ( $attr_val === $slug ) {
			$found_id = $child_id;
			break;
		}
	}

	$variation = $found_id ? wc_get_product( $found_id ) : new WC_Product_Variation();
	$variation->set_parent_id( $parent_id );
	$variation->set_attributes( array( 'pa_color' => $slug ) );
	$variation->set_regular_price( $price );
	$variation->set_price( $price );
	$variation->set_stock_status( 'instock' );
	$variation->set_manage_stock( true );
	$variation->set_stock_quantity( 5 );
	$variation->save();
}

WC_Product_Variable::sync( $parent_id );

// Style guide page.
$page = get_page_by_path( 'style-guide' );
if ( ! $page ) {
	wp_insert_post(
		array(
			'post_title'  => 'Style Guide',
			'post_name'   => 'style-guide',
			'post_status' => 'publish',
			'post_type'   => 'page',
			'post_content'=> '',
			'page_template' => 'template-styleguide.php',
		)
	);
	echo "Created Style Guide page.\n";
} else {
	update_post_meta( $page->ID, '_wp_page_template', 'template-styleguide.php' );
	echo "Updated Style Guide page template.\n";
}

echo "Done.\n";
echo "Shop: " . home_url( '/shop/' ) . "\n";
echo "Style Guide: " . home_url( '/style-guide/' ) . "\n";
