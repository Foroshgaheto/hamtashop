<?php
/**
 * Template Name: Style Guide
 * Description: کتابخانه زنده کامپوننت‌های تم — فقط برای توسعه.
 *
 * @package Hamta_Base
 */

get_header();
?>
<main id="main" class="hamta-main container mx-auto px-4 py-10" role="main">
	<header class="mb-10">
		<h1 class="text-3xl font-bold mb-2"><?php esc_html_e( 'Style Guide — همـتا', 'hamta-base' ); ?></h1>
		<p class="text-[var(--color-muted)]"><?php esc_html_e( 'کتابخانه زنده کامپوننت‌ها برای تست حین توسعه.', 'hamta-base' ); ?></p>
	</header>

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
		<h2 class="text-xl font-bold mb-4"><?php esc_html_e( 'کارت', 'hamta-base' ); ?></h2>
		<div class="hamta-card p-6 max-w-sm">
			<h3 class="font-bold mb-2"><?php esc_html_e( 'نمونه کارت', 'hamta-base' ); ?></h3>
			<p class="text-sm text-[var(--color-muted)]"><?php esc_html_e( 'جایگزین کارت محصول در فاز ۱.', 'hamta-base' ); ?></p>
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
