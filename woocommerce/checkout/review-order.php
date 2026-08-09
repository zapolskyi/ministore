<?php
/**
 * Підсумки замовлення на сторінці оформлення.
 *
 * Оверрайд woocommerce/templates/checkout/review-order.php.
 * Причина: за макетом тут тільки суми, у тому ж вигляді, що й у кошику.
 *
 * ⚠ Класи woocommerce-checkout-review-order-table і shop_table лишаємо —
 * саме цю таблицю Woo підмінює через AJAX при зміні доставки.
 *
 * Рядок доставки НЕ виводимо: вибір способу винесений в окремий блок
 * у form-checkout.php, а тут показуємо лише його вартість.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;
?>

<table class="shop_table woocommerce-checkout-review-order-table cart-totals__table">
	<tbody>

		<?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>
		<?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>

		<tr class="cart-totals__row cart-subtotal">
			<th class="cart-totals__label"><?php esc_html_e( 'Subtotal', 'my-theme' ); ?></th>
			<td class="cart-totals__value"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-totals__row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th class="cart-totals__label"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td class="cart-totals__value"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php
			/**
			 * Тільки вартість обраної доставки — самі радіокнопки вже виведені
			 * окремим блоком вище. Друкувати їх двічі не можна: у формі з'явились би
			 * два набори полів з однаковими іменами.
			 */
			foreach ( WC()->cart->get_shipping_packages() as $index => $package ) :
				$chosen = WC()->session->get( 'chosen_shipping_methods' )[ $index ] ?? '';
				$rates  = WC()->shipping()->get_packages()[ $index ]['rates'] ?? array();

				if ( ! isset( $rates[ $chosen ] ) ) {
					continue;
				}
				?>
				<tr class="cart-totals__row woocommerce-shipping-totals">
					<th class="cart-totals__label"><?php esc_html_e( 'Доставка', 'my-theme' ); ?></th>
					<td class="cart-totals__value">
						<?php echo wp_kses_post( wc_cart_totals_shipping_method_label( $rates[ $chosen ] ) ); ?>
					</td>
				</tr>
			<?php endforeach; ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="cart-totals__row fee">
				<th class="cart-totals__label"><?php echo esc_html( $fee->name ); ?></th>
				<td class="cart-totals__value"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="cart-totals__row cart-totals__row--total order-total">
			<th class="cart-totals__label"><?php esc_html_e( 'Total', 'my-theme' ); ?></th>
			<td class="cart-totals__value"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tbody>
</table>
