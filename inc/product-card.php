<?php
/**
 * Product card helpers and data builders.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert Western digits to Persian digits.
 *
 * @param string|int|float $value Input.
 * @return string
 */
function hamta_persian_digits( $value ) {
	$map = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
	return strtr( (string) $value, $map );
}

/**
 * Format a price HTML string with Persian digits (keeps WC markup).
 *
 * @param string $html Price HTML from wc_price / get_price_html.
 * @return string
 */
function hamta_format_price_html( $html ) {
	return hamta_persian_digits( $html );
}

/**
 * Build card data array from a WC product.
 *
 * @param WC_Product|null $product Product.
 * @param array           $args    Optional overrides for demos.
 * @return array|null
 */
function hamta_get_product_card_data( $product, $args = array() ) {
	if ( ! $product instanceof WC_Product ) {
		return null;
	}

	$product_id   = $product->get_id();
	$regular      = (float) $product->get_regular_price();
	$sale         = $product->get_sale_price();
	$is_on_sale   = $product->is_on_sale() && '' !== $sale && $regular > 0;
	$discount_pct = 0;

	if ( $is_on_sale ) {
		$discount_pct = (int) round( ( ( $regular - (float) $sale ) / $regular ) * 100 );
	}

	$sale_to = $product->get_date_on_sale_to();
	$timer_end = ( $sale_to && $is_on_sale ) ? $sale_to->getTimestamp() : 0;

	$stock_qty  = $product->get_stock_quantity();
	$stock_text = '';
	if ( $product->managing_stock() && null !== $stock_qty ) {
		/* translators: %s: stock quantity */
		$stock_text = sprintf( __( '%s در انبار', 'hamta-base' ), hamta_persian_digits( (string) $stock_qty ) );
	} elseif ( $product->is_in_stock() ) {
		$stock_text = __( 'موجود', 'hamta-base' );
	} else {
		$stock_text = __( 'ناموجود', 'hamta-base' );
	}

	$is_variable = $product->is_type( 'variable' );
	$can_ajax    = $product->is_purchasable() && $product->is_in_stock() && ! $is_variable && $product->supports( 'ajax_add_to_cart' );

	$rating = (float) $product->get_average_rating();
	$count  = (int) $product->get_rating_count();

	$min_price = $is_variable ? (float) $product->get_variation_price( 'min', true ) : (float) $product->get_price();
	$price_amount = hamta_persian_digits( number_format( max( 0, $min_price ), 0, '.', ',' ) );
	$regular_for_amount = $is_variable ? (float) $product->get_variation_regular_price( 'min', true ) : $regular;
	$regular_amount = ( $is_on_sale && $regular_for_amount > $min_price )
		? hamta_persian_digits( number_format( $regular_for_amount, 0, '.', ',' ) )
		: ( ( $is_variable && $regular_for_amount > $min_price )
			? hamta_persian_digits( number_format( $regular_for_amount, 0, '.', ',' ) )
			: '' );

	// Variable on sale: compute discount from min regular vs min price when possible.
	if ( $is_variable && $regular_for_amount > $min_price && $regular_for_amount > 0 ) {
		$discount_pct = (int) round( ( ( $regular_for_amount - $min_price ) / $regular_for_amount ) * 100 );
		$is_on_sale   = true;
		if ( ! $timer_end ) {
			$sale_to = $product->get_date_on_sale_to();
			$timer_end = $sale_to ? $sale_to->getTimestamp() : 0;
		}
	}

	$data = array(
		'id'             => $product_id,
		'title'          => $product->get_name(),
		'permalink'      => get_permalink( $product_id ),
		'image'          => array(
			'url' => wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src( 'woocommerce_thumbnail' ),
			'alt' => $product->get_name(),
		),
		'price_html'     => hamta_format_price_html( $product->get_price_html() ),
		'price_amount'   => $price_amount,
		'regular_amount' => $regular_amount,
		'price_from'     => $is_variable,
		'regular_html'   => $regular_amount ? hamta_format_price_html( wc_price( $regular_for_amount ) ) : '',
		'is_on_sale'     => $is_on_sale || ( $regular_amount && $regular_for_amount > $min_price ),
		'discount_pct'   => $discount_pct,
		'timer_end'      => $timer_end,
		'stock_text'     => $stock_text,
		'in_stock'       => $product->is_in_stock(),
		'rating'         => $rating,
		'rating_count'   => $count,
		'badges'         => hamta_get_product_badges( $product_id ),
		'colors'         => hamta_get_product_color_swatches( $product ),
		'cta'            => array(
			'type'  => $can_ajax ? 'add_to_cart' : 'view',
			'label' => $can_ajax ? __( 'افزودن به سبد', 'hamta-base' ) : __( 'مشاهده محصول', 'hamta-base' ),
			'url'   => $can_ajax ? $product->add_to_cart_url() : get_permalink( $product_id ),
		),
	);

	return array_replace_recursive( $data, $args );
}

/**
 * Color swatches from pa_color (+ variation image map).
 *
 * @param WC_Product $product Product.
 * @return array<int, array{slug:string,name:string,hex:string,image:string,permalink:string}>
 */
function hamta_get_product_color_swatches( $product ) {
	$swatches = array();

	if ( $product->is_type( 'variable' ) ) {
		/** @var WC_Product_Variable $product */
		$variations = $product->get_available_variations();
		foreach ( $variations as $variation ) {
			$attrs = $variation['attributes'];
			$key   = 'attribute_pa_color';
			if ( empty( $attrs[ $key ] ) ) {
				continue;
			}
			$slug = sanitize_title( $attrs[ $key ] );
			$term = get_term_by( 'slug', $slug, 'pa_color' );
			$hex  = '';
			$name = $slug;
			if ( $term && ! is_wp_error( $term ) ) {
				$name = $term->name;
				$hex  = get_term_meta( $term->term_id, 'hamta_color_hex', true );
			}
			if ( ! $hex ) {
				$hex = hamta_guess_color_hex( $slug, $name );
			}

			$img = ! empty( $variation['image']['src'] ) ? $variation['image']['src'] : '';
			$swatches[ $slug ] = array(
				'slug'      => $slug,
				'name'      => $name,
				'hex'       => $hex,
				'image'     => $img,
				'permalink' => get_permalink( $variation['variation_id'] ),
			);
		}
		return array_values( $swatches );
	}

	// Simple product with pa_color attribute (display only).
	$attrs = $product->get_attributes();
	if ( empty( $attrs['pa_color'] ) ) {
		return array();
	}

	$attr = $attrs['pa_color'];
	$options = $attr->is_taxonomy() ? wc_get_product_terms( $product->get_id(), 'pa_color', array( 'fields' => 'all' ) ) : array();
	foreach ( $options as $term ) {
		$hex = get_term_meta( $term->term_id, 'hamta_color_hex', true );
		if ( ! $hex ) {
			$hex = hamta_guess_color_hex( $term->slug, $term->name );
		}
		$swatches[] = array(
			'slug'      => $term->slug,
			'name'      => $term->name,
			'hex'       => $hex,
			'image'     => '',
			'permalink' => get_permalink( $product->get_id() ),
		);
	}

	return $swatches;
}

/**
 * Fallback hex from common Persian/English color names.
 *
 * @param string $slug Slug.
 * @param string $name Name.
 * @return string
 */
function hamta_guess_color_hex( $slug, $name ) {
	$map = array(
		'black'   => '#111827',
		'siah'    => '#111827',
		'siyah'   => '#111827',
		'white'   => '#F9FAFB',
		'sefid'   => '#F9FAFB',
		'red'     => '#DC2626',
		'ghermez' => '#DC2626',
		'blue'    => '#2563EB',
		'abi'     => '#2563EB',
		'green'   => '#16A34A',
		'sabz'    => '#16A34A',
		'gold'    => '#D97706',
		'talaei'  => '#D97706',
		'silver'  => '#9CA3AF',
		'noghre'  => '#9CA3AF',
		'pink'    => '#EC4899',
		'soorati' => '#EC4899',
		'gray'    => '#6B7280',
		'grey'    => '#6B7280',
		'khakestari' => '#6B7280',
	);

	$slug = strtolower( $slug );
	if ( isset( $map[ $slug ] ) ) {
		return $map[ $slug ];
	}

	$lower = strtolower( $name );
	foreach ( $map as $key => $hex ) {
		if ( false !== strpos( $slug, $key ) || false !== strpos( $lower, $key ) ) {
			return $hex;
		}
	}

	return '#9CA3AF';
}

/**
 * Render star rating markup (0–5).
 *
 * @param float $rating Average rating.
 * @param int   $count  Rating count.
 */
function hamta_render_star_rating( $rating, $count = 0 ) {
	$full  = (int) floor( $rating );
	$half  = ( $rating - $full ) >= 0.5 ? 1 : 0;
	$empty = 5 - $full - $half;
	?>
	<div class="hamta-product-card__rating" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: rating 2: count */ __( 'امتیاز %1$s از ۵ (%2$s نظر)', 'hamta-base' ), hamta_persian_digits( number_format( $rating, 1 ) ), hamta_persian_digits( (string) $count ) ) ); ?>">
		<?php for ( $i = 0; $i < $full; $i++ ) : ?>
			<span class="hamta-product-card__star hamta-product-card__star--full" aria-hidden="true"><?php echo hamta_icon_star( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endfor; ?>
		<?php if ( $half ) : ?>
			<span class="hamta-product-card__star hamta-product-card__star--half" aria-hidden="true"><?php echo hamta_icon_star( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endif; ?>
		<?php for ( $i = 0; $i < $empty; $i++ ) : ?>
			<span class="hamta-product-card__star hamta-product-card__star--empty" aria-hidden="true"><?php echo hamta_icon_star( false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endfor; ?>
		<?php if ( $count > 0 ) : ?>
			<span class="hamta-product-card__rating-count">(<?php echo esc_html( hamta_persian_digits( (string) $count ) ); ?>)</span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Inline star SVG.
 *
 * @param bool $filled Filled or outline.
 * @return string
 */
function hamta_icon_star( $filled = true ) {
	$fill = $filled ? 'currentColor' : 'none';
	$stroke = 'currentColor';
	return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="' . esc_attr( $fill ) . '" stroke="' . esc_attr( $stroke ) . '" stroke-width="1.5" aria-hidden="true"><path d="M12 3.5l2.6 5.3 5.8.8-4.2 4.1 1 5.8L12 16.8 6.8 19.5l1-5.8L3.6 9.6l5.8-.8L12 3.5z"/></svg>';
}

/**
 * Heart icon SVG.
 *
 * @return string
 */
function hamta_icon_heart() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s-6.7-4.4-9.3-8C.6 9.8 1.3 5.8 4.6 4.2c2-.9 4.3-.3 5.7 1.3C11.7 3.9 14 3.3 16 4.2c3.3 1.6 4 5.6 1.9 8.8C18.7 16.6 12 21 12 21z"/></svg>';
}
