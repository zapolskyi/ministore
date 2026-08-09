<?php
/**
 * Оформлення замовлення.
 *
 * Оверрайд woocommerce/templates/checkout/form-checkout.php.
 * Причина: власна двоколонкова розкладка й окремий блок вибору доставки.
 *
 * ⚠ Сторінка має містити шорткод [woocommerce_checkout], а не блок
 *   wp:woocommerce/checkout — інакше цей шаблон не використовується.
 *
 * ЩО ЗМІНЕНО ПРОТИ МАКЕТА — свідомо, під український магазин:
 * прибрано Company, Country, Street address, State, ZIP Code
 * (див. inc/checkout.php). Замість них — вибір доставки й поля Нової Пошти.
 *
 * @package my-theme
 *
 * @var WC_Checkout $checkout Об'єкт оформлення, передає WooCommerce.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

// Магазин може вимагати реєстрацію — тоді форми не буде взагалі.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="checkout-page">

	<?php // Класи checkout і woocommerce-checkout обов'язкові: за ними працює JS Woo. ?>
	<form
		name="checkout"
		method="post"
		class="checkout woocommerce-checkout checkout-page__form"
		action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
		enctype="multipart/form-data"
	>

		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="checkout-page__cols">

				<section class="checkout-page__col" id="customer_details">
					<?php
					/**
					 * Поля покупця.
					 *
					 * Розмітку полів малює сам WooCommerce за списком, який ми
					 * підрізали фільтром woocommerce_checkout_fields в inc/checkout.php.
					 */
					do_action( 'woocommerce_checkout_billing' );
					?>
				</section>

				<section class="checkout-page__col">
					<?php
					/**
					 * Додаткова інформація — коментар до замовлення.
					 *
					 * Це той самий хук, що зазвичай малює адресу доставки.
					 * Оскільки адреса нам не потрібна, лишається тільки блок
					 * «Additional information» з полем нотатки — рівно як у макеті.
					 */
					do_action( 'woocommerce_checkout_shipping' );
					?>
				</section>

			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>

		<?php
		/**
		 * Спосіб доставки.
		 *
		 * Виносимо з таблиці підсумків в окремий блок за макетом. Це безпечно:
		 * checkout.js слухає зміни делегованим обробником на всій формі
		 * (input[name^="shipping_method"]), тому позиція в розмітці не важлива.
		 *
		 * Головне — зберегти імена та класи, за якими він чіпляється.
		 */
		$packages = WC()->shipping()->get_packages();
		?>

		<?php if ( WC()->cart->needs_shipping() && $packages ) : ?>
			<section class="checkout-shipping">
				<h2 class="checkout-page__title"><?php esc_html_e( 'Доставка', 'my-theme' ); ?></h2>

				<?php foreach ( $packages as $index => $package ) : ?>
					<?php
					$available = $package['rates'];
					$chosen    = WC()->session->get( 'chosen_shipping_methods' )[ $index ] ?? '';
					?>
					<div class="checkout-shipping__methods">
						<?php foreach ( $available as $rate_id => $rate ) : ?>
							<label class="radio-option" for="shipping_method_<?php echo esc_attr( $index . '_' . sanitize_title( $rate_id ) ); ?>">
								<input
									type="radio"
									name="shipping_method[<?php echo esc_attr( $index ); ?>]"
									id="shipping_method_<?php echo esc_attr( $index . '_' . sanitize_title( $rate_id ) ); ?>"
									value="<?php echo esc_attr( $rate_id ); ?>"
									class="shipping_method"
									<?php checked( $rate_id, $chosen ); ?>
								>
								<span class="radio-option__label">
									<?php echo wp_kses_post( wc_cart_totals_shipping_method_label( $rate ) ); ?>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>

				<?php
				// Поля Нової Пошти. Показ і приховування — на JS,
				// обов'язковість перевіряється на сервері (inc/checkout.php).
				?>
				<div class="checkout-shipping__fields" data-shipping-fields="nova-poshta">
					<?php mytheme_nova_poshta_fields(); ?>
				</div>

				<div class="checkout-shipping__fields" data-shipping-fields="pickup" hidden>
					<p class="checkout-shipping__pickup-address">
						<strong><?php esc_html_e( 'Самовивіз зі складу', 'my-theme' ); ?></strong><br>
						м. Київ, вул. Прикладна, 1<br>
						Пн–Пт 10:00–19:00, Сб 10:00–16:00
					</p>
				</div>
			</section>
		<?php endif; ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

		<h2 class="cart-totals__title" id="order_review_heading"><?php esc_html_e( 'Cart Totals', 'my-theme' ); ?></h2>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<?php // id="order_review" обов'язковий: саме цей блок Woo оновлює через AJAX. ?>
		<div id="order_review" class="woocommerce-checkout-review-order checkout-totals">
			<?php
			/**
			 * @hooked woocommerce_order_review - 10      таблиця підсумків
			 * @hooked woocommerce_checkout_payment - 20  оплата й кнопка замовлення
			 */
			do_action( 'woocommerce_checkout_order_review' );
			?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

	</form>

</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
