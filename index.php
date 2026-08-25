<?php
/**
 * Main template fallback.
 *
 * @package Hamta_Base
 */

get_header();
?>
<main id="main" class="hamta-main container mx-auto px-4 py-8" role="main">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class( 'mb-8' ); ?>>
				<h1 class="text-2xl font-bold text-[var(--color-text)] mb-4"><?php the_title(); ?></h1>
				<div class="prose max-w-none text-[var(--color-text)]">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p class="text-[var(--color-muted)]"><?php esc_html_e( 'محتوایی یافت نشد.', 'hamta-base' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
