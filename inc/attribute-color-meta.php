<?php
/**
 * Hex color meta for pa_color attribute terms (admin swatches).
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add hex field when creating a color term.
 */
function hamta_pa_color_add_fields() {
	?>
	<div class="form-field">
		<label for="hamta_color_hex"><?php esc_html_e( 'کد رنگ (Hex)', 'hamta-base' ); ?></label>
		<input type="color" name="hamta_color_hex" id="hamta_color_hex" value="#9CA3AF" />
	</div>
	<?php
}
add_action( 'pa_color_add_form_fields', 'hamta_pa_color_add_fields' );

/**
 * Edit hex field.
 *
 * @param WP_Term $term Term.
 */
function hamta_pa_color_edit_fields( $term ) {
	$hex = get_term_meta( $term->term_id, 'hamta_color_hex', true );
	if ( ! $hex ) {
		$hex = '#9CA3AF';
	}
	?>
	<tr class="form-field">
		<th scope="row"><label for="hamta_color_hex"><?php esc_html_e( 'کد رنگ (Hex)', 'hamta-base' ); ?></label></th>
		<td><input type="color" name="hamta_color_hex" id="hamta_color_hex" value="<?php echo esc_attr( $hex ); ?>" /></td>
	</tr>
	<?php
}
add_action( 'pa_color_edit_form_fields', 'hamta_pa_color_edit_fields' );

/**
 * Save pa_color hex.
 *
 * @param int $term_id Term ID.
 */
function hamta_save_pa_color_hex( $term_id ) {
	if ( ! isset( $_POST['hamta_color_hex'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	$hex = sanitize_hex_color( wp_unslash( $_POST['hamta_color_hex'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( $hex ) {
		update_term_meta( $term_id, 'hamta_color_hex', $hex );
	}
}
add_action( 'created_pa_color', 'hamta_save_pa_color_hex' );
add_action( 'edited_pa_color', 'hamta_save_pa_color_hex' );
