<?php
/**
 * Theme settings panel via WordPress Settings API (no custom admin CSS).
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings page under Appearance.
 */
function hamta_register_settings() {
	register_setting( 'hamta_theme_settings', 'hamta_theme_options', array(
		'type'              => 'array',
		'sanitize_callback' => 'hamta_sanitize_theme_options',
		'default'           => array(),
	) );

	add_settings_section(
		'hamta_general',
		__( 'تنظیمات عمومی', 'hamta-base' ),
		'__return_false',
		'hamta-theme-settings'
	);

	add_settings_field(
		'store_phone',
		__( 'شماره تماس فروشگاه', 'hamta-base' ),
		'hamta_render_store_phone_field',
		'hamta-theme-settings',
		'hamta_general'
	);
}
add_action( 'admin_init', 'hamta_register_settings' );

/**
 * Add Appearance → تنظیمات تم menu.
 */
function hamta_add_settings_page() {
	add_theme_page(
		__( 'تنظیمات تم همتا', 'hamta-base' ),
		__( 'تنظیمات تم همتا', 'hamta-base' ),
		'manage_options',
		'hamta-theme-settings',
		'hamta_render_settings_page'
	);
}
add_action( 'admin_menu', 'hamta_add_settings_page' );

/**
 * Sanitize options.
 *
 * @param array $input Raw input.
 * @return array
 */
function hamta_sanitize_theme_options( $input ) {
	$output = array();

	if ( isset( $input['store_phone'] ) ) {
		$output['store_phone'] = sanitize_text_field( $input['store_phone'] );
	}

	return $output;
}

/**
 * Render phone field.
 */
function hamta_render_store_phone_field() {
	$options = get_option( 'hamta_theme_options', array() );
	$value   = isset( $options['store_phone'] ) ? $options['store_phone'] : '';
	printf(
		'<input type="text" name="hamta_theme_options[store_phone]" value="%s" class="regular-text" dir="ltr" />',
		esc_attr( $value )
	);
}

/**
 * Render settings page markup.
 */
function hamta_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'hamta_theme_settings' );
			do_settings_sections( 'hamta-theme-settings' );
			submit_button( __( 'ذخیره تغییرات', 'hamta-base' ) );
			?>
		</form>
	</div>
	<?php
}
