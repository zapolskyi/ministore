<?php
/**
 * Добірка товарів на головній.
 *
 * Використовує ті самі блоки, що й сторінка товару:
 * .section-bar (заголовок + посилання) і .products-slider (стрічка з крапками).
 * Картка товару — спільна, з woocommerce/content-product.php.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

// Без WooCommerce товарів не існує — секцію просто не виводимо.
if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

/**
 * Спершу беремо товари, позначені зіркою «Рекомендований» в адмінці —
 * так замовник керує вмістом головної сам, без розробника.
 */
$products = wc_get_products(
	array(
		'featured' => true,
		'limit'    => 4,
		'status'   => 'publish',
	)
);

// Якщо нічого не позначено, показуємо останні додані,
// щоб на новому магазині блок не був порожнім.
if ( ! $products ) {
	$products = wc_get_products(
		array(
			'limit'   => 4,
			'status'  => 'publish',
			'orderby' => 'date',
			'order'   => 'DESC',
		)
	);
}

if ( ! $products ) {
	return;
}
?>

<section class="featured-products">
	<div class="container">

		<div class="section-bar">
			<h2 class="section-bar__title"><?php esc_html_e( 'Mobile Products', 'my-theme' ); ?></h2>
			<a class="section-bar__link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Go to shop', 'my-theme' ); ?>
			</a>
		</div>

		<div class="products-slider" data-slider>
			<ul class="products-slider__track" data-slider-track>
				<?php
				foreach ( $products as $product ) {
					/**
					 * content-product.php читає глобальні $post і $product,
					 * бо розрахований на звичайний цикл WordPress.
					 * Тут циклу немає, тому підставляємо обидві змінні вручну.
					 */
					$GLOBALS['post']    = get_post( $product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$GLOBALS['product'] = $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

					setup_postdata( $GLOBALS['post'] );

					wc_get_template_part( 'content', 'product' );
				}

				// Повертаємо глобальні змінні на місце — далі на сторінці
				// є інші секції, і вони не мають бачити останній товар.
				wp_reset_postdata();
				?>
			</ul>

			<div class="slider-dots" data-slider-dots aria-label="<?php esc_attr_e( 'Гортати товари', 'my-theme' ); ?>">
				<button class="slider-dots__dot slider-dots__dot--active" type="button" aria-label="1"></button>
				<button class="slider-dots__dot" type="button" aria-label="2"></button>
				<button class="slider-dots__dot" type="button" aria-label="3"></button>
			</div>
		</div>

	</div>
</section>
