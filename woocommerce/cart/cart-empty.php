<?php
/**
 * Порожній кошик.
 *
 * Оверрайд woocommerce/templates/cart/cart-empty.php (версія оригіналу 7.0.1).
 * Причина: власна розмітка й кнопка в стилі теми.
 *
 * WooCommerce підключає цей файл замість cart.php, коли товарів у кошику немає.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="cart-empty">

	<?php
	/**
	 * Повідомлення «Ваш кошик порожній».
	 *
	 * @hooked wc_empty_cart_message - 10
	 *
	 * Хук лишаємо: через нього плагіни підмінюють текст,
	 * а сам напис перекладається разом з WooCommerce.
	 */
	do_action( 'woocommerce_cart_is_empty' );
	?>

	<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
		<a
			class="btn btn--dark cart-empty__button"
			href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"
		>
			<?php
			/**
			 * Filter "Return To Shop" text.
			 *
			 * @since 4.6.0
			 * @param string $default_text Default text.
			 */
			echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to shop', 'woocommerce' ) ) );
			?>
		</a>
	<?php endif; ?>

</div>