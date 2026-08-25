<?php
/**
 * Import Digikala sample products into WooCommerce (local test data).
 * Usage: php bin/import-digikala-products.php [path-to-json]
 *
 * @package Hamta_Base
 */

if ( php_sapi_name() !== 'cli' ) {
	exit( "CLI only\n" );
}

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
require_once $wp_load;

if ( ! class_exists( 'WooCommerce' ) ) {
	fwrite( STDERR, "WooCommerce required\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$json_path = $argv[1] ?? 'c:/wamp64/tmp/dk-import.json';
if ( ! file_exists( $json_path ) ) {
	fwrite( STDERR, "JSON not found: {$json_path}\n" );
	exit( 1 );
}

$items = json_decode( file_get_contents( $json_path ), true );
if ( ! is_array( $items ) || empty( $items ) ) {
	fwrite( STDERR, "Empty import list\n" );
	exit( 1 );
}

/**
 * Digikala API prices are in Rials → convert to Toman.
 *
 * @param int $rial Price in Rials.
 * @return string
 */
function hamta_dk_to_toman( $rial ) {
	$toman = (int) round( ( (int) $rial ) / 10 );
	return (string) max( 0, $toman );
}

/**
 * Sideload remote image as product thumbnail.
 *
 * @param string $url        Image URL.
 * @param int    $product_id Product ID.
 * @param string $desc       Description.
 * @return int Attachment ID or 0.
 */
function hamta_dk_sideload_image( $url, $product_id, $desc ) {
	if ( ! $url ) {
		return 0;
	}

	// Prefer larger image for product gallery.
	$url = preg_replace( '/h_300,w_300/', 'h_800,w_800', $url );

	$tmp = download_url( $url, 30 );
	if ( is_wp_error( $tmp ) ) {
		echo "  image download failed: " . $tmp->get_error_message() . "\n";
		return 0;
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	$name = $path ? basename( $path ) : 'digikala.jpg';
	$name = preg_replace( '/[^a-zA-Z0-9._-]/', '', $name );
	if ( ! preg_match( '/\.(jpe?g|png|webp|gif)$/i', $name ) ) {
		$name .= '.jpg';
	}

	$file_array = array(
		'name'     => $name,
		'tmp_name' => $tmp,
	);

	$id = media_handle_sideload( $file_array, $product_id, $desc );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore
		echo "  image sideload failed: " . $id->get_error_message() . "\n";
		return 0;
	}

	return (int) $id;
}

/**
 * Find existing product by Digikala SKU.
 *
 * @param string $sku SKU.
 * @return WC_Product|null
 */
function hamta_dk_find_by_sku( $sku ) {
	$id = wc_get_product_id_by_sku( $sku );
	return $id ? wc_get_product( $id ) : null;
}

$badge_asl = term_exists( 'asl', 'product_badge' );
if ( ! $badge_asl ) {
	$badge_asl = wp_insert_term( 'اصل', 'product_badge', array( 'slug' => 'asl' ) );
	if ( ! is_wp_error( $badge_asl ) ) {
		update_term_meta( (int) $badge_asl['term_id'], 'hamta_badge_color', '#16A34A' );
	}
}

$imported = 0;
foreach ( $items as $index => $item ) {
	$dk_id = (int) $item['id'];
	$sku   = 'DK-' . $dk_id;
	$title = sanitize_text_field( $item['title'] );
	echo ( $index + 1 ) . ". {$title} ({$sku})\n";

	$selling = hamta_dk_to_toman( $item['selling'] );
	$rrp     = hamta_dk_to_toman( $item['rrp'] );
	if ( (int) $rrp < (int) $selling ) {
		$rrp = $selling;
	}

	$product = hamta_dk_find_by_sku( $sku );
	if ( ! $product ) {
		$product = new WC_Product_Simple();
		$product->set_sku( $sku );
	}

	$product->set_name( $title );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_regular_price( $rrp );
	if ( ! empty( $item['discount'] ) && (int) $selling < (int) $rrp ) {
		$product->set_sale_price( $selling );
		$product->set_price( $selling );
		// Short flash sale window for timer demos on discounted items.
		$product->set_date_on_sale_from( time() - HOUR_IN_SECONDS );
		$product->set_date_on_sale_to( time() + ( 2 * DAY_IN_SECONDS ) );
	} else {
		$product->set_sale_price( '' );
		$product->set_price( $selling );
		$product->set_date_on_sale_from( '' );
		$product->set_date_on_sale_to( '' );
	}

	$product->set_manage_stock( true );
	$product->set_stock_quantity( wp_rand( 3, 25 ) );
	$product->set_stock_status( 'instock' );
	$product->set_short_description(
		sprintf(
			/* translators: %s: Digikala product id */
			__( 'محصول نمونه استخراج‌شده از دیجیکالا (شناسه %s) — فقط برای تست تم.', 'hamta-base' ),
			$dk_id
		)
	);
	$product->set_description(
		sprintf(
			'<p>%s</p><p><a href="%s" target="_blank" rel="nofollow noopener">%s</a></p>',
			esc_html( $title ),
			esc_url( 'https://www.digikala.com/product/dkp-' . $dk_id . '/' ),
			esc_html__( 'منبع: دیجیکالا', 'hamta-base' )
		)
	);

	$rate = isset( $item['rating'] ) ? ( (float) $item['rating'] ) / 20 : 0; // Digikala 0–100 → 0–5.
	$rate = max( 0, min( 5, round( $rate, 1 ) ) );
	$count = (int) ( $item['rating_count'] ?? 0 );
	if ( $rate > 0 ) {
		$product->set_average_rating( (string) $rate );
		$product->set_review_count( $count );
		$product->set_rating_counts(
			array(
				5 => (int) max( 1, round( $count * 0.5 ) ),
				4 => (int) max( 0, round( $count * 0.3 ) ),
				3 => (int) max( 0, round( $count * 0.15 ) ),
				2 => (int) max( 0, round( $count * 0.03 ) ),
				1 => (int) max( 0, round( $count * 0.02 ) ),
			)
		);
	}

	$product_id = $product->save();
	update_post_meta( $product_id, '_hamta_digikala_id', $dk_id );
	update_post_meta( $product_id, '_hamta_source', 'digikala' );

	if ( ! $product->get_image_id() && ! empty( $item['image'] ) ) {
		$attach_id = hamta_dk_sideload_image( $item['image'], $product_id, $title );
		if ( $attach_id ) {
			$product->set_image_id( $attach_id );
			$product->save();
			echo "  image OK (#{$attach_id})\n";
		}
	} else {
		echo "  image skipped (exists or empty)\n";
	}

	// Attach "اصل" badge to first half of imports.
	if ( $index < 5 && taxonomy_exists( 'product_badge' ) ) {
		wp_set_object_terms( $product_id, array( 'asl' ), 'product_badge', true );
	}

	echo "  saved #{$product_id} price={$selling} تومان (rrp={$rrp})\n";
	$imported++;
}

echo "Done. Imported/updated: {$imported}\n";
echo "Shop: " . home_url( '/?post_type=product' ) . "\n";
