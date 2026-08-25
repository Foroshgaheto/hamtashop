<?php
/**
 * Theme header.
 *
 * @package Hamta_Base
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[var(--color-bg)] text-[var(--color-text)] font-[family-name:var(--font-body)] antialiased' ); ?>>
<?php wp_body_open(); ?>
<a class="sr-only focus:not-sr-only" href="#main"><?php esc_html_e( 'پرش به محتوا', 'hamta-base' ); ?></a>
<header class="hamta-header border-b border-gray-200 bg-white" role="banner">
	<div class="container mx-auto px-4 py-4 flex items-center justify-between gap-4">
		<div class="hamta-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-xl font-bold text-[var(--color-primary)]">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<nav class="hamta-nav hidden md:block" aria-label="<?php esc_attr_e( 'منوی اصلی', 'hamta-base' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'flex gap-6 text-sm',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>
</header>
