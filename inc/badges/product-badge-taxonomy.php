<?php
/**
 * Product badge taxonomy (product_badge) with color & icon term meta.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register product_badge taxonomy.
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

/**
 * Add color & icon fields on Add term screen.
 */
function hamta_badge_add_form_fields() {
	?>
	<div class="form-field">
		<label for="hamta_badge_color"><?php esc_html_e( 'رنگ بج', 'hamta-base' ); ?></label>
		<input type="color" name="hamta_badge_color" id="hamta_badge_color" value="#E11D48" />
		<p><?php esc_html_e( 'رنگ پس‌زمینه بج روی کارت محصول.', 'hamta-base' ); ?></p>
	</div>
	<div class="form-field">
		<label for="hamta_badge_icon"><?php esc_html_e( 'آیکون (SVG یا URL تصویر)', 'hamta-base' ); ?></label>
		<textarea name="hamta_badge_icon" id="hamta_badge_icon" rows="3" class="large-text" dir="ltr"></textarea>
		<p><?php esc_html_e( 'SVG اینلاین یا آدرس تصویر. خالی بگذارید تا فقط متن نمایش داده شود.', 'hamta-base' ); ?></p>
	</div>
	<?php
}
add_action( 'product_badge_add_form_fields', 'hamta_badge_add_form_fields' );

/**
 * Edit term fields.
 *
 * @param WP_Term $term Term object.
 */
function hamta_badge_edit_form_fields( $term ) {
	$color = get_term_meta( $term->term_id, 'hamta_badge_color', true );
	$icon  = get_term_meta( $term->term_id, 'hamta_badge_icon', true );
	if ( ! $color ) {
		$color = '#E11D48';
	}
	?>
	<tr class="form-field">
		<th scope="row"><label for="hamta_badge_color"><?php esc_html_e( 'رنگ بج', 'hamta-base' ); ?></label></th>
		<td>
			<input type="color" name="hamta_badge_color" id="hamta_badge_color" value="<?php echo esc_attr( $color ); ?>" />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="hamta_badge_icon"><?php esc_html_e( 'آیکون (SVG یا URL تصویر)', 'hamta-base' ); ?></label></th>
		<td>
			<textarea name="hamta_badge_icon" id="hamta_badge_icon" rows="4" class="large-text" dir="ltr"><?php echo esc_textarea( $icon ); ?></textarea>
		</td>
	</tr>
	<?php
}
add_action( 'product_badge_edit_form_fields', 'hamta_badge_edit_form_fields' );

/**
 * Save badge term meta.
 *
 * @param int $term_id Term ID.
 */
function hamta_save_badge_term_meta( $term_id ) {
	if ( isset( $_POST['hamta_badge_color'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$color = sanitize_hex_color( wp_unslash( $_POST['hamta_badge_color'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $color ) {
			update_term_meta( $term_id, 'hamta_badge_color', $color );
		}
	}

	if ( isset( $_POST['hamta_badge_icon'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$icon = wp_kses(
			wp_unslash( $_POST['hamta_badge_icon'] ), // phpcs:ignore WordPress.Security.NonceVerification.Missing
			array(
				'svg'  => array(
					'xmlns'       => true,
					'viewbox'     => true,
					'viewBox'     => true,
					'width'       => true,
					'height'      => true,
					'fill'        => true,
					'stroke'      => true,
					'class'       => true,
					'aria-hidden' => true,
				),
				'path' => array(
					'd'            => true,
					'fill'         => true,
					'stroke'       => true,
					'stroke-width' => true,
				),
				'img'  => array(
					'src'    => true,
					'alt'    => true,
					'width'  => true,
					'height' => true,
					'class'  => true,
				),
			)
		);
		update_term_meta( $term_id, 'hamta_badge_icon', $icon );
	}
}
add_action( 'created_product_badge', 'hamta_save_badge_term_meta' );
add_action( 'edited_product_badge', 'hamta_save_badge_term_meta' );

/**
 * Get badge display data for a product.
 *
 * @param int $product_id Product ID.
 * @return array<int, array{name:string,color:string,icon:string,slug:string}>
 */
function hamta_get_product_badges( $product_id ) {
	$terms = get_the_terms( $product_id, 'product_badge' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return array();
	}

	$badges = array();
	foreach ( $terms as $term ) {
		$color = get_term_meta( $term->term_id, 'hamta_badge_color', true );
		$icon  = get_term_meta( $term->term_id, 'hamta_badge_icon', true );
		$badges[] = array(
			'name'  => $term->name,
			'slug'  => $term->slug,
			'color' => $color ? $color : '#E11D48',
			'icon'  => $icon ? $icon : '',
		);
	}

	return $badges;
}
