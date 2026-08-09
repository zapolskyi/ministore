<?php
/**
 * Вміст сторінки товару: галерея, опис, вкладки, схожі товари.
 *
 * Підключається з woocommerce/single-product.php усередині циклу.
 * Тому тут НЕ МАЄ бути ні get_header(), ні get_footer() — вони вже викликані
 * у батьківському файлі, інакше шапка й підвал виведуться двічі.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$product_title             = $product->get_name();
$product_price             = $product->get_price_html();
$product_description       = $product->get_description();
$product_short_description = $product->get_short_description();

// get_average_rating() — середня оцінка (5.0 з макета).
// get_rating_count() — це КІЛЬКІСТЬ оцінок, зовсім інше число.
$product_rating = $product->get_average_rating();

?>

<div class="container">

	<?php
	/**
	 * Повідомлення WooCommerce: «Товар додано в кошик», помилки вибору варіації тощо.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 *
	 * Без цього хука додавання в кошик виглядає так, ніби нічого не сталося:
	 * товар у кошику є, але користувач жодного підтвердження не бачить.
	 */
	do_action( 'woocommerce_before_single_product' );
	?>

	<div class="product">

		<div class="product__gallery">
			<?php
			/**
			 * Галерея товару.
			 *
			 * Своїм <img> тут обійтися не можна: скрипт варіацій підміняє фото
			 * саме всередині .woocommerce-product-gallery. Плюс ця функція
			 * дає зум, лайтбокс, мініатюри й srcset — усе з коробки.
			 *
			 * Можливості вмикаються через add_theme_support( 'wc-product-gallery-*' )
			 * в inc/woocommerce-setup.php: без них Woo не підключить свої скрипти.
			 *
			 * Якщо фото немає — Woo сам покаже заглушку.
			 */
			woocommerce_show_product_images(); 
			?>
		</div>

		<div class="product__summary">

			<h1 class="product__title"><?php echo esc_html( $product_title ); ?></h1>

			<?php if ( $product->get_rating_count() > 0 ) : ?>
				<div class="product__rating">
					<span class="product__stars" aria-hidden="true">&#9733;</span>
					<span class="product__rating-value"><?php echo esc_html( number_format( (float) $product_rating, 1 ) ); ?></span>
				</div>
			<?php endif; ?>

			<p class="product__price"><?php echo wp_kses_post( $product_price ); ?></p>

			<?php if ( $product_short_description ) : ?>
				<div class="product__excerpt">
					<?php echo wp_kses_post( $product_short_description ); ?>
				</div>
			<?php endif; ?>

			<?php
			/**
			 * Форма додавання в кошик.
			 *
			 * Один виклик замість цілого блоку розмітки. Для варіативного товару
			 * WooCommerce підключить woocommerce/single-product/add-to-cart/variable.php
			 * (наш оверрайд зі свотчами), для простого — simple.php.
			 *
			 * Усередині він сам виведе: вибір варіації, ціну обраної варіації,
			 * наявність, поле кількості й кнопку «В кошик». Писати це руками не треба —
			 * і не можна, бо саме ці елементи оновлює скрипт варіацій.
			 */
			woocommerce_template_single_add_to_cart();
			?>

			<dl class="product__meta">
				<?php if ( $product->get_sku() ) : ?>
					<div class="product__meta-row">
						<dt>SKU:</dt>
						<dd><?php echo esc_html( $product->get_sku() ); ?></dd>
					</div>
				<?php endif; ?>

				<?php
				// Обидві функції повертають готові посилання на архіви таксономій.
				$category_list = wc_get_product_category_list( $product->get_id(), ', ' );
				$tag_list      = wc_get_product_tag_list( $product->get_id(), ', ' );
				?>

				<?php if ( $category_list ) : ?>
					<div class="product__meta-row">
						<dt>Category:</dt>
						<dd><?php echo wp_kses_post( $category_list ); ?></dd>
					</div>
				<?php endif; ?>

				<?php if ( $tag_list ) : ?>
					<div class="product__meta-row">
						<dt>Tags:</dt>
						<dd><?php echo wp_kses_post( $tag_list ); ?></dd>
					</div>
				<?php endif; ?>
			</dl>

		</div>
	</div>

	<div class="product-tabs" data-tabs>

		<div class="product-tabs__nav" role="tablist">
			<button class="product-tabs__tab product-tabs__tab--active" type="button" role="tab" aria-selected="true" aria-controls="tab-description" id="tab-btn-description">
				Description
			</button>
			<button class="product-tabs__tab" type="button" role="tab" aria-selected="false" aria-controls="tab-info" id="tab-btn-info">
				Additional Information
			</button>
			<button class="product-tabs__tab" type="button" role="tab" aria-selected="false" aria-controls="tab-reviews" id="tab-btn-reviews">
				Reviews (<?php echo esc_html( $product->get_review_count() ); ?>)
			</button>
		</div>

		<div class="product-tabs__panel product-tabs__panel--active" id="tab-description" role="tabpanel" aria-labelledby="tab-btn-description">
			<h2 class="product-tabs__title">Product Description</h2>
			<?php echo wp_kses_post( wpautop( $product_description ) ); ?>
		</div>

		<div class="product-tabs__panel" id="tab-info" role="tabpanel" aria-labelledby="tab-btn-info" hidden>
			<?php
			// Готова таблиця характеристик Woo: вага, габарити, атрибути товару.
			wc_display_product_attributes( $product );
			?>
		</div>

		<div class="product-tabs__panel" id="tab-reviews" role="tabpanel" aria-labelledby="tab-btn-reviews" hidden>
			<p>Відгуки підключимо окремим кроком.</p>
		</div>

	</div>

	<?php
	/**
	 * Схожі товари.
	 *
	 * wc_get_related_products() сам добирає товари з тих самих категорій і тегів,
	 * виключає поточний і перемішує результат. Свій запит писати не треба.
	 */
	$related_ids = wc_get_related_products( $product->get_id(), 4 );
	?>

	<?php if ( $related_ids ) : ?>
		<section class="related">

			<div class="section-bar">
				<h2 class="section-bar__title"><?php esc_html_e( 'Related Products', 'my-theme' ); ?></h2>
				<a class="section-bar__link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
					<?php esc_html_e( 'Go to shop', 'my-theme' ); ?>
				</a>
			</div>

			<div class="products-slider" data-slider>
				<ul class="products-slider__track" data-slider-track>
					<?php
					foreach ( $related_ids as $related_id ) {
						/**
						 * content-product.php читає глобальні $post і $product,
						 * бо розрахований на звичайний цикл WordPress.
						 * Тут циклу немає, тому підставляємо обидві змінні вручну.
						 */
						$GLOBALS['post']    = get_post( $related_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$GLOBALS['product'] = wc_get_product( $related_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

						setup_postdata( $GLOBALS['post'] );

						wc_get_template_part( 'content', 'product' );
					}

					// Повертаємо глобальні змінні на місце, інакше все нижче
					// думатиме, що ми досі на останньому схожому товарі.
					wp_reset_postdata();
					$GLOBALS['product'] = wc_get_product( get_the_ID() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					?>
				</ul>

				<div class="slider-dots" data-slider-dots aria-label="<?php esc_attr_e( 'Гортати товари', 'my-theme' ); ?>">
					<button class="slider-dots__dot slider-dots__dot--active" type="button" aria-label="1"></button>
					<button class="slider-dots__dot" type="button" aria-label="2"></button>
					<button class="slider-dots__dot" type="button" aria-label="3"></button>
				</div>
			</div>

		</section>
	<?php endif; ?>

</div>
