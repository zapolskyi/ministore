<?php
/**
 * Оформлення замовлення.
 *
 * Оверрайд woocommerce/templates/checkout/form-checkout.php.
 *
 * Поки що ЛИШЕ ВЕРСТКА: поля статичні, підсумки статичні.
 * PHP накладемо наступним кроком.
 *
 * ⚠ Сторінка має містити шорткод [woocommerce_checkout], а не блок
 *   wp:woocommerce/checkout — інакше цей шаблон не використовується.
 *
 * ВІДМІННОСТІ ВІД МАКЕТА — свідомі, під український магазин:
 *   прибрано Company, Country, Street address, State, ZIP Code;
 *   натомість вибір способу доставки й поля Нової Пошти.
 *   Адреса потрібна лише для кур'єрської доставки, а на відділення
 *   вистачає міста й номера відділення.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="checkout-page">

	<form class="checkout-page__form" action="#" method="post">

		<div class="checkout-page__cols">

			<section class="checkout-page__col">
				<h2 class="checkout-page__title">Billing details</h2>

				<p class="form-row">
					<label for="billing_first_name">First name <abbr class="required" title="обов'язкове">*</abbr></label>
					<input type="text" id="billing_first_name" name="billing_first_name" required>
				</p>

				<p class="form-row">
					<label for="billing_last_name">Last name <abbr class="required" title="обов'язкове">*</abbr></label>
					<input type="text" id="billing_last_name" name="billing_last_name" required>
				</p>

				<p class="form-row">
					<label for="billing_phone">Phone <abbr class="required" title="обов'язкове">*</abbr></label>
					<input type="tel" id="billing_phone" name="billing_phone" placeholder="+380" required>
				</p>

				<p class="form-row">
					<label for="billing_email">Email address <abbr class="required" title="обов'язкове">*</abbr></label>
					<input type="email" id="billing_email" name="billing_email" required>
				</p>
			</section>

			<section class="checkout-page__col">
				<h2 class="checkout-page__title">Additional information</h2>

				<p class="form-row">
					<label for="order_comments">Order notes (optional)</label>
					<textarea id="order_comments" name="order_comments" rows="5"
						placeholder="Notes about your order, i.e. special notes for delivery"></textarea>
				</p>
			</section>

		</div>

		<?php
		/**
		 * Спосіб доставки.
		 *
		 * Перемикання блоків робить JS (initCheckoutShipping у main.js) —
		 * це стан інтерфейсу, він не залежить від сервера.
		 *
		 * Коли накладемо PHP, радіокнопки замінить справжній список
		 * способів доставки з зони «Україна».
		 */
		?>
		<section class="checkout-shipping">
			<h2 class="checkout-page__title">Доставка</h2>

			<div class="checkout-shipping__methods">
				<label class="radio-option">
					<input type="radio" name="shipping_method" value="nova_poshta" checked
						data-shipping-toggle="delivery">
					<span class="radio-option__label">Нова Пошта</span>
				</label>

				<label class="radio-option">
					<input type="radio" name="shipping_method" value="pickup"
						data-shipping-toggle="pickup">
					<span class="radio-option__label">Самовивіз</span>
				</label>
			</div>

			<div class="checkout-shipping__fields" data-shipping-fields="delivery">
				<p class="form-row">
					<label for="np_city">Місто <abbr class="required" title="обов'язкове">*</abbr></label>
					<input type="text" id="np_city" name="np_city" placeholder="Почніть вводити назву міста" required>
				</p>

				<p class="form-row">
					<label for="np_warehouse">Відділення <abbr class="required" title="обов'язкове">*</abbr></label>
					<select id="np_warehouse" name="np_warehouse" required>
						<option value="">Спершу оберіть місто</option>
					</select>
				</p>
			</div>

			<div class="checkout-shipping__fields" data-shipping-fields="pickup" hidden>
				<p class="checkout-shipping__pickup-address">
					<strong>Самовивіз зі складу</strong><br>
					м. Київ, вул. Прикладна, 1<br>
					Пн–Пт 10:00–19:00, Сб 10:00–16:00
				</p>
			</div>
		</section>

		<section class="cart-totals checkout-totals">

			<h2 class="cart-totals__title">Cart Totals</h2>

			<table class="cart-totals__table">
				<tbody>
					<tr class="cart-totals__row">
						<th class="cart-totals__label">Subtotal</th>
						<td class="cart-totals__value">$1500.00</td>
					</tr>
					<tr class="cart-totals__row cart-totals__row--total">
						<th class="cart-totals__label">Total</th>
						<td class="cart-totals__value">$1500.00</td>
					</tr>
				</tbody>
			</table>

			<div class="checkout-payment">
				<label class="radio-option">
					<input type="radio" name="payment_method" value="bacs" checked>
					<span class="radio-option__label">Direct bank transfer</span>
				</label>

				<label class="radio-option">
					<input type="radio" name="payment_method" value="cheque">
					<span class="radio-option__label">Check payments</span>
				</label>

				<label class="radio-option">
					<input type="radio" name="payment_method" value="cod">
					<span class="radio-option__label">Cash on delivery</span>
				</label>

				<label class="radio-option">
					<input type="radio" name="payment_method" value="paypal">
					<span class="radio-option__label">PayPal</span>
				</label>
			</div>

			<button class="btn btn--dark checkout-page__submit" type="submit">Place an order</button>

		</section>

	</form>

</div>
