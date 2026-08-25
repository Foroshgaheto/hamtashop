<?php
/**
 * Template Name: Style Guide
 * Description: کتابخانه زنده کامپوننت‌های تم — فقط برای توسعه.
 *
 * @package Hamta_Base
 */

get_header();

$demo_slugs = array(
	'hamta-demo-simple'   => __( 'حالت ساده', 'hamta-base' ),
	'hamta-demo-sale'     => __( 'تخفیف‌دار + تایمر', 'hamta-base' ),
	'hamta-demo-variable' => __( 'واریانت رنگ (pa_color)', 'hamta-base' ),
	'hamta-demo-badges'   => __( 'با بج‌های محصول', 'hamta-base' ),
);
?>
<main id="main" class="hamta-main container mx-auto px-4 py-10" role="main">
	<header class="mb-10">
		<h1 class="text-3xl font-bold mb-2"><?php esc_html_e( 'Style Guide — همـتا', 'hamta-base' ); ?></h1>
		<p class="text-[var(--color-muted)]"><?php esc_html_e( 'کتابخانه زنده کامپوننت‌ها برای تست حین توسعه.', 'hamta-base' ); ?></p>
	</header>

	<section class="mb-14">
		<h2 class="text-xl font-bold mb-2"><?php esc_html_e( 'کارت محصول', 'hamta-base' ); ?></h2>
		<p class="text-sm text-[var(--color-muted)] mb-6"><?php esc_html_e( 'نمونه‌ی هر حالت کارت با داده‌ی واقعی محصول.', 'hamta-base' ); ?></p>

		<div class="space-y-10">
			<?php foreach ( $demo_slugs as $slug => $label ) : ?>
				<?php
				$post = get_page_by_path( $slug, OBJECT, 'product' );
				$product = $post ? wc_get_product( $post->ID ) : null;
				?>
				<div>
					<h3 class="text-base font-semibold mb-3"><?php echo esc_html( $label ); ?></h3>
					<?php if ( $product ) : ?>
						<div class="hamta-product-card-grid max-w-xs sm:max-w-none sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
							<?php get_template_part( 'template-parts/product', 'card', array( 'product' => $product ) ); ?>
						</div>
					<?php else : ?>
						<p class="text-sm text-[var(--color-danger)]">
							<?php
							printf(
								/* translators: %s: product slug */
								esc_html__( 'محصول نمونه «%s» یافت نشد. اسکریپت seed را اجرا کنید.', 'hamta-base' ),
								esc_html( $slug )
							);
							?>
						</p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="mb-12">
		<h2 class="text-xl font-bold mb-4"><?php esc_html_e( 'دکمه‌ها', 'hamta-base' ); ?></h2>
		<div class="flex flex-wrap gap-3">
			<button type="button" class="hamta-btn"><?php esc_html_e( 'دکمه اصلی', 'hamta-base' ); ?></button>
			<button type="button" class="hamta-btn" style="background-color: var(--color-secondary);">
				<?php esc_html_e( 'دکمه ثانویه', 'hamta-base' ); ?>
			</button>
		</div>
	</section>

	<section class="mb-12">
		<h2 class="text-xl font-bold mb-4"><?php esc_html_e( 'رنگ‌ها (Design Tokens)', 'hamta-base' ); ?></h2>
		<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
			<?php
			$swatches = array(
				'primary'   => 'var(--color-primary)',
				'secondary' => 'var(--color-secondary)',
				'success'   => 'var(--color-success)',
				'warning'   => 'var(--color-warning)',
				'danger'    => 'var(--color-danger)',
				'muted'     => 'var(--color-muted)',
				'bg'        => 'var(--color-bg)',
				'text'      => 'var(--color-text)',
			);
			foreach ( $swatches as $name => $css_var ) :
				?>
				<div class="text-center">
					<div class="h-16 rounded-card shadow-card border border-gray-200 mb-2" style="background: <?php echo esc_attr( $css_var ); ?>"></div>
					<span class="text-xs text-[var(--color-muted)]"><?php echo esc_html( $name ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</main>
<?php
get_footer();
