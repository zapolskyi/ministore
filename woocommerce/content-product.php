<?php
/**
 * Картка товару в сітці.
 *
 * WooCommerce виводить цим файлом ОДИН товар усередині циклу — і в каталозі,
 * і в блоці схожих товарів.
 *
 * Товар приходить у глобальній змінній $product — її наповнює сам WooCommerce
 * на кожній ітерації циклу (через the_post()).
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || !$product->is_visible() ) {
  return;
}

$product_title = $product->get_name();
$product_image = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
$product_price = $product->get_price_html();

if ( ! $product_image ) {
	$product_image = wc_placeholder_img_src( 'woocommerce_thumbnail' );
}

?>

<li class="product-card">
	<a class="product-card__media" href="<?php the_permalink(); ?>">
		<img
			class="product-card__image"
			src="<?php echo esc_url( $product_image ); ?>"
			alt="<?php echo esc_attr( $product_title ); ?>"
			width="600"
			height="600"
			loading="lazy"
		>

		<span class="product-card__add">
			Add to cart
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
				<path d="M3 4h2.5l2 12h11l2-8H7" stroke-linecap="round" stroke-linejoin="round"/>
				<circle cx="9.5" cy="20" r="1.4" fill="currentColor" stroke="none"/>
				<circle cx="17.5" cy="20" r="1.4" fill="currentColor" stroke="none"/>
			</svg>
		</span>
	</a>

	<div class="product-card__info">
		<h3 class="product-card__title">
			<a href="<?php the_permalink(); ?>"><?php echo esc_html( $product_title ); ?></a>
		</h3>
		<span class="product-card__price"><?php echo wp_kses_post( $product_price ); ?></span>
	</div>
</li>
