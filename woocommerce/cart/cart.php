<?php
/**
 * Кошик.
 *
 * Оверрайд woocommerce/templates/cart/cart.php (версія оригіналу 11.0.0).
 * Причина: власна таблиця за макетом замість стандартної розмітки Woo.
 *
 * ⚠ Підключається лише коли в кошику Є товари. Порожній малює cart-empty.php.
 * ⚠ Сторінка кошика має містити шорткод [woocommerce_cart], а не блок
 *   wp:woocommerce/cart — інакше цей шаблон не використовується взагалі.
 *
 * Розкладка за макетом вимагає, щоб кнопка «Update cart» стояла в одному ряду
 * з «Continue shopping» і «Proceed to checkout» — тобто ПОЗА формою.
 * Тому форма має id, а кнопка звертається до неї атрибутом form="…".
 * Це штатний механізм HTML, а не хак: кнопка з form відправляє вказану форму.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

$form_id = 'mytheme-cart-form';

do_action( 'woocommerce_before_cart' );
?>

<div class="cart-page">

	<form
		id="<?php echo esc_attr( $form_id ); ?>"
		class="woocommerce-cart-form cart-page__form"
		action="<?php echo esc_url( wc_get_cart_url() ); ?>"
		method="post"
	>
		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<table class="cart-table woocommerce-cart-form__contents" cellspacing="0">
			<thead>
				<tr>
					<th class="cart-table__head cart-table__head--product"><?php esc_html_e( 'Product', 'my-theme' ); ?></th>
					<th class="cart-table__head cart-table__head--qty"><?php esc_html_e( 'Quantity', 'my-theme' ); ?></th>
					<th class="cart-table__head cart-table__head--subtotal"><?php esc_html_e( 'Subtotal', 'my-theme' ); ?></th>
					<th class="cart-table__head cart-table__head--remove">
						<span class="visually-hidden"><?php esc_html_e( 'Видалити', 'my-theme' ); ?></span>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					/**
					 * $cart_item_key — хеш рядка кошика. Саме через нього працюють
					 * зміна кількості й видалення, тому він потрібен у кожній комірці.
					 */
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					// Товар могли видалити з каталогу, поки він лежав у кошику.
					if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
						continue;
					}

					if ( ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						continue;
					}

					// Прихований товар лишається в кошику, але без посилання на нього.
					$product_permalink = apply_filters(
						'woocommerce_cart_item_permalink',
						$_product->is_visible() ? $_product->get_permalink( $cart_item ) : '',
						$cart_item,
						$cart_item_key
					);
					?>
					<tr class="cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="cart-item__product" data-label="<?php esc_attr_e( 'Product', 'my-theme' ); ?>">
							<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( $product_permalink ) {
								printf( '<a class="cart-item__thumb" href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								printf( '<span class="cart-item__thumb">%s</span>', $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>

							<div class="cart-item__info">
								<?php
								$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

								if ( $product_permalink ) {
									printf( '<a class="cart-item__name" href="%s">%s</a>', esc_url( $product_permalink ), esc_html( $product_name ) );
								} else {
									printf( '<span class="cart-item__name">%s</span>', esc_html( $product_name ) );
								}

								// Обрані варіації: Color: Orange, Size: XL.
								echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								// Ціна за одиницю.
								echo '<span class="cart-item__price">';
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo '</span>';

								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
								}

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
								?>
							</div>
						</td>

						<td class="cart-item__qty" data-label="<?php esc_attr_e( 'Quantity', 'my-theme' ); ?>">
							<?php
							if ( $_product->is_sold_individually() ) {
								// Товар «лише один на замовлення» — кількість змінювати не можна.
								$product_quantity = sprintf( '<span class="quantity-fixed">1</span><input type="hidden" name="cart[%s][qty]" value="1">', $cart_item_key );
							} else {
								/**
								 * Ім'я поля мусить бути рівно cart[ключ][qty].
								 * Будь-яке інше — і «Update cart» мовчки нічого не змінить.
								 *
								 * Кнопки − і + додає хук у inc/woocommerce-setup.php.
								 * Компактний розмір у кошику задає CSS через
								 * .cart-item__qty .quantity — без зайвих класів у PHP.
								 */
								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $_product->get_max_purchase_quantity(),
										'min_value'    => '0',
										'product_name' => $_product->get_name(),
									),
									$_product,
									false
								);
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</td>

						<td class="cart-item__subtotal" data-label="<?php esc_attr_e( 'Subtotal', 'my-theme' ); ?>">
							<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</td>

						<td class="cart-item__remove">
							<?php
							/**
							 * Посилання видалення містить nonce, тому збирати його
							 * руками не можна — тільки через цей фільтр.
							 */
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="cart-item__remove-link" aria-label="%s" data-product_id="%s" data-product_sku="%s">
										<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19" stroke-linecap="round"/></svg>
									</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									/* translators: %s — назва товару. */
									esc_attr( sprintf( __( 'Видалити %s з кошика', 'my-theme' ), $_product->get_name() ) ),
									esc_attr( $product_id ),
									esc_attr( $_product->get_sku() )
								),
								$cart_item_key
							);
							?>
						</td>

					</tr>
					<?php
				}
				?>

				<?php do_action( 'woocommerce_cart_contents' ); ?>
				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</tbody>
		</table>

		<div class="cart-page__coupon">
			<?php if ( wc_coupons_enabled() ) : ?>
				<label class="visually-hidden" for="coupon_code"><?php esc_html_e( 'Промокод', 'my-theme' ); ?></label>
				<input
					type="text"
					name="coupon_code"
					class="cart-page__coupon-input"
					id="coupon_code"
					value=""
					placeholder="<?php esc_attr_e( 'Промокод', 'my-theme' ); ?>"
				>
				<button class="btn btn--outline" type="submit" name="apply_coupon" value="<?php esc_attr_e( 'Застосувати', 'my-theme' ); ?>">
					<?php esc_html_e( 'Застосувати', 'my-theme' ); ?>
				</button>

				<?php do_action( 'woocommerce_cart_coupon' ); ?>
			<?php endif; ?>

			<?php do_action( 'woocommerce_cart_actions' ); ?>

			<?php
			// Без nonce WooCommerce мовчки проігнорує відправку форми.
			wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' );
			?>
		</div>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

	<section class="cart-totals">

		<h2 class="cart-totals__title"><?php esc_html_e( 'Cart Totals', 'my-theme' ); ?></h2>

		<?php do_action( 'woocommerce_before_cart_totals' ); ?>

		<?php
		/**
		 * Підсумки — саме <table>, а не <dl>.
		 *
		 * Помічники Woo для доставки друкують готовий <tr> з <th> і <td>
		 * (див. cart/cart-shipping.php). У списку визначень така розмітка
		 * була б невалідною, тому структуру тримаємо табличною.
		 */
		?>
		<table class="cart-totals__table">
			<tbody>

				<tr class="cart-totals__row">
					<th class="cart-totals__label"><?php esc_html_e( 'Subtotal', 'my-theme' ); ?></th>
					<td class="cart-totals__value"><?php wc_cart_totals_subtotal_html(); ?></td>
				</tr>

				<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
					<tr class="cart-totals__row coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th class="cart-totals__label"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
						<td class="cart-totals__value"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
					</tr>
				<?php endforeach; ?>

				<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
					<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
					<?php wc_cart_totals_shipping_html(); ?>
					<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
				<?php endif; ?>

				<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
					<tr class="cart-totals__row">
						<th class="cart-totals__label"><?php echo esc_html( $fee->name ); ?></th>
						<td class="cart-totals__value"><?php wc_cart_totals_fee_html( $fee ); ?></td>
					</tr>
				<?php endforeach; ?>

				<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

				<tr class="cart-totals__row cart-totals__row--total">
					<th class="cart-totals__label"><?php esc_html_e( 'Total', 'my-theme' ); ?></th>
					<td class="cart-totals__value"><?php wc_cart_totals_order_total_html(); ?></td>
				</tr>

				<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

			</tbody>
		</table>

		<div class="cart-totals__actions">
			<?php
			// Кнопка поза формою, але відправляє її — через атрибут form.
			// name="update_cart" обов'язкове: саме це ім'я шукає WooCommerce.
			?>
			<button
				class="btn btn--dark"
				type="submit"
				name="update_cart"
				form="<?php echo esc_attr( $form_id ); ?>"
				value="<?php esc_attr_e( 'Update cart', 'my-theme' ); ?>"
			>
				<?php esc_html_e( 'Update cart', 'my-theme' ); ?>
			</button>

			<a class="btn btn--dark" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Continue shopping', 'my-theme' ); ?>
			</a>

			<?php
			/**
			 * Кнопка оформлення.
			 *
			 * @hooked wc_get_pay_buttons - 10                    express-оплата
			 * @hooked woocommerce_button_proceed_to_checkout - 20 звичайна кнопка
			 *
			 * Своє посилання ставити не можна: тоді express-кнопки
			 * (Apple Pay та подібні) не з'являться ніколи.
			 */
			do_action( 'woocommerce_proceed_to_checkout' );
			?>
		</div>

		<?php do_action( 'woocommerce_cart_collaterals' ); ?>

	</section>

	<?php do_action( 'woocommerce_after_cart' ); ?>

</div>
