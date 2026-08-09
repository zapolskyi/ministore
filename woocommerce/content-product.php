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

	<?php
	/**
	 * Обгортка зображення — <div>, а не <a>.
	 *
	 * Кнопку «В кошик» друкує WooCommerce, і це посилання <a>.
	 * Посилання всередині посилання — невалідний HTML: браузер розриває
	 * розмітку по-своєму, і клік починає працювати непередбачувано.
	 */
	?>
	<div class="product-card__media">

		<?php
		// Зображення теж веде на товар, але з навігації клавіатурою прибране:
		// нижче є посилання на назві, і два таби на один товар зайві.
		?>
		<a class="product-card__link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<img
				class="product-card__image"
				src="<?php echo esc_url( $product_image ); ?>"
				alt="<?php echo esc_attr( $product_title ); ?>"
				width="600"
				height="600"
				loading="lazy"
			>
		</a>

		<div class="product-card__add">
			<?php
			/**
			 * Справжня кнопка WooCommerce.
			 *
			 * Вона сама вміє все, що нам потрібно:
			 *   • додає товар без перезавантаження сторінки (AJAX);
			 *   • для варіативного товару веде на сторінку вибору, а не додає наосліп;
			 *   • ховається, якщо товару немає в наявності;
			 *   • після додавання показує посилання «Переглянути кошик».
			 *
			 * Писати це руками означало б переписати add-to-cart.js.
			 */
			woocommerce_template_loop_add_to_cart();
			?>
		</div>

	</div>

	<div class="product-card__info">
		<h3 class="product-card__title">
			<a href="<?php the_permalink(); ?>"><?php echo esc_html( $product_title ); ?></a>
		</h3>
		<span class="product-card__price"><?php echo wp_kses_post( $product_price ); ?></span>
	</div>
</li>
