<?php
/**
 * Reusable product card — Figma: red accent, vertical swatches, sale price.
 *
 * @package Hamta_Base
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product = isset( $args['product'] ) ? $args['product'] : null;
if ( ! $product && function_exists( 'wc_get_product' ) ) {
	$product = wc_get_product( get_the_ID() );
}

$overrides = isset( $args['data'] ) && is_array( $args['data'] ) ? $args['data'] : array();
$data      = hamta_get_product_card_data( $product, $overrides );

if ( ! $data ) {
	return;
}

$has_timer = ! empty( $data['timer_end'] ) && (int) $data['timer_end'] > time();

$alpine_config = array(
	'productId' => (int) $data['id'],
	'image'     => $data['image']['url'],
	'permalink' => $data['permalink'],
	'colors'    => array_values( $data['colors'] ),
	'timerEnd'  => (int) $data['timer_end'],
);
?>
<article
	class="hamta-product-card"
	data-product-id="<?php echo esc_attr( (string) $data['id'] ); ?>"
	data-hamta-card="<?php echo esc_attr( wp_json_encode( $alpine_config ) ); ?>"
	x-data="hamtaProductCard(JSON.parse($el.dataset.hamtaCard))"
>
	<div class="hamta-product-card__panel">
		<div class="hamta-product-card__media">
			<?php if ( ! empty( $data['is_on_sale'] ) && ! empty( $data['discount_pct'] ) ) : ?>
				<span class="hamta-product-card__discount">
					٪<?php echo esc_html( hamta_persian_digits( (string) $data['discount_pct'] ) ); ?>
				</span>
			<?php endif; ?>

			<button
				type="button"
				class="hamta-product-card__wishlist"
				:class="{ 'is-active': wishlisted }"
				@click.prevent="toggleWishlist()"
				:aria-pressed="wishlisted.toString()"
				aria-label="<?php esc_attr_e( 'افزودن به علاقه‌مندی‌ها', 'hamta-base' ); ?>"
			>
				<?php echo hamta_icon_heart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<a class="hamta-product-card__image-link" :href="permalink" :aria-label="<?php echo esc_attr( $data['title'] ); ?>">
				<img
					class="hamta-product-card__image"
					:src="image"
					src="<?php echo esc_url( $data['image']['url'] ); ?>"
					alt="<?php echo esc_attr( $data['image']['alt'] ); ?>"
					loading="lazy"
					width="191"
					height="191"
				/>
			</a>

			<?php if ( ! empty( $data['colors'] ) ) : ?>
				<ul class="hamta-product-card__swatches" role="list">
					<template x-for="color in colors" :key="color.slug">
						<li>
							<button
								type="button"
								class="hamta-product-card__swatch"
								:class="{ 'is-active': selectedColor === color.slug }"
								:style="'background-color:' + color.hex"
								:title="color.name"
								:aria-label="color.name"
								@click.prevent="selectColor(color)"
							></button>
						</li>
					</template>
				</ul>
			<?php endif; ?>
		</div>

		<div class="hamta-product-card__body">
			<h3 class="hamta-product-card__title">
				<a :href="permalink" href="<?php echo esc_url( $data['permalink'] ); ?>">
					<?php echo esc_html( $data['title'] ); ?>
				</a>
			</h3>

			<?php if ( $has_timer ) : ?>
				<div class="hamta-product-card__timer" x-show="timer.visible" x-cloak>
					<span class="hamta-product-card__timer-label"><?php esc_html_e( 'فروش فوق‌العاده', 'hamta-base' ); ?></span>
					<span class="hamta-product-card__timer-digits" dir="ltr" x-text="timer.label"></span>
				</div>
			<?php endif; ?>

			<div class="hamta-product-card__footer">
				<?php if ( 'add_to_cart' === $data['cta']['type'] ) : ?>
					<a
						href="<?php echo esc_url( $data['cta']['url'] ); ?>"
						class="hamta-product-card__cart-btn add_to_cart_button ajax_add_to_cart"
						data-quantity="1"
						data-product_id="<?php echo esc_attr( (string) $data['id'] ); ?>"
						data-product_sku="<?php echo esc_attr( $product ? $product->get_sku() : '' ); ?>"
						aria-label="<?php echo esc_attr( $data['cta']['label'] ); ?>"
						rel="nofollow"
					>
						<?php echo hamta_icon_cart_plus(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php else : ?>
					<a
						class="hamta-product-card__cart-btn"
						:href="permalink"
						href="<?php echo esc_url( $data['cta']['url'] ); ?>"
						aria-label="<?php echo esc_attr( $data['cta']['label'] ); ?>"
					>
						<?php echo hamta_icon_cart_plus(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				<?php endif; ?>

				<div class="hamta-product-card__price-block">
					<?php if ( ! empty( $data['regular_amount'] ) ) : ?>
						<span class="hamta-product-card__price-regular" dir="ltr"><?php echo esc_html( $data['regular_amount'] ); ?></span>
					<?php endif; ?>
					<div class="hamta-product-card__price-row">
						<span class="hamta-product-card__price-amount" dir="ltr"><?php echo esc_html( $data['price_amount'] ); ?></span>
						<span class="hamta-product-card__price-currency"><?php esc_html_e( 'تومان', 'hamta-base' ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</article>
