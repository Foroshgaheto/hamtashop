<?php
/**
 * Minimal WooCommerce archive template using theme header/footer.
 *
 * @package Hamta_Base
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="hamta-main container mx-auto px-4 py-8" role="main">
	<?php if ( woocommerce_product_loop() ) : ?>
		<header class="mb-6">
			<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
				<h1 class="text-2xl font-bold"><?php woocommerce_page_title(); ?></h1>
			<?php endif; ?>
			<?php do_action( 'woocommerce_archive_description' ); ?>
		</header>

		<?php do_action( 'woocommerce_before_shop_loop' ); ?>
		<?php woocommerce_product_loop_start(); ?>
		<?php
		if ( wc_get_loop_prop( 'total' ) ) {
			while ( have_posts() ) {
				the_post();
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
			}
		}
		?>
		<?php woocommerce_product_loop_end(); ?>
		<?php do_action( 'woocommerce_after_shop_loop' ); ?>
	<?php else : ?>
		<?php do_action( 'woocommerce_no_products_found' ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
