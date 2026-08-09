<?php
/**
 * Способи оплати й кнопка «Place an order».
 *
 * Оверрайд woocommerce/templates/checkout/payment.php (версія оригіналу 10.9.0).
 * Причина: власна розмітка радіокнопок і кнопки за макетом.
 *
 * ⚠ Обов'язково зберегти, інакше оформлення зламається:
 *   #payment                          — цей блок Woo оновлює через AJAX
 *   ul.wc_payment_methods             — по ньому шукає методи
 *   input[name="payment_method"]      — вибір способу оплати
 *   #place_order з name="woocommerce_checkout_place_order" — кнопка відправки
 *
 * @package my-theme
 *
 * @var array  $available_gateways Доступні платіжні шлюзи.
 * @var string $order_button_text  Напис на кнопці замовлення.
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>

<div id="payment" class="woocommerce-checkout-payment checkout-payment">

	<?php if ( WC()->cart->needs_payment() ) : ?>
		<ul class="wc_payment_methods payment_methods methods">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				// Жодного шлюзу не увімкнено — покупець має зрозуміти, що сталось.
				wc_print_notice(
					apply_filters(
						'woocommerce_no_available_payment_methods_message',
						WC()->customer->get_billing_country()
							? esc_html__( 'Sorry, it seems that there are no available payment methods.', 'woocommerce' )
							: esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' )
					),
					'notice'
				);
			}
			?>
		</ul>
	<?php endif; ?>

	<div class="form-row place-order">

		<noscript>
			<?php esc_html_e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the Update Totals button before placing your order.', 'woocommerce' ); ?>
			<br>
			<button type="submit" class="btn btn--outline" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>">
				<?php esc_html_e( 'Update totals', 'woocommerce' ); ?>
			</button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php
		/**
		 * Кнопка замовлення.
		 *
		 * Проходить через фільтр woocommerce_order_button_html — деякі
		 * платіжні шлюзи підмінюють її власною кнопкою, тому ламати цей
		 * фільтр не можна.
		 */
		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'woocommerce_order_button_html',
			'<button type="submit" class="btn btn--dark checkout-page__submit" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>'
		);
		?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

	</div>

</div>

<?php
if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
