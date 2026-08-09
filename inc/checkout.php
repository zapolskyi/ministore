<?php
/**
 * Оформлення замовлення: поля, валідація, збереження.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID способу доставки «Нова Пошта».
 *
 * Метод створено в зоні «Україна» як flat_rate. Ідентифікатор має вигляд
 * flat_rate:2 — число це номер екземпляра методу в зоні.
 * Виносимо в одне місце, щоб не шукати рядок по всьому коду.
 */
const MYTHEME_SHIPPING_NOVA_POSHTA = 'flat_rate';

/**
 * Прибирає зайві поля з форми оформлення.
 *
 * Стандартний набір Woo розрахований на міжнародну торгівлю: країна, штат,
 * дві адресні строки, поштовий індекс, назва компанії. Для доставки на
 * відділення Нової Пошти жодне з них не потрібне, а кожне зайве поле —
 * це відсоток покупців, які не дійшли до кінця.
 *
 * Лишаємо: ім'я, прізвище, телефон, e-mail.
 *
 * @param array $fields Поля форми, згруповані за секціями.
 * @return array
 */
function mytheme_checkout_fields( $fields ) {
	$remove = array(
		'billing_company',
		'billing_country',
		'billing_address_1',
		'billing_address_2',
		'billing_city',
		'billing_state',
		'billing_postcode',
	);

	foreach ( $remove as $key ) {
		unset( $fields['billing'][ $key ] );
	}

	// Порядок полів у колонці. priority — саме він визначає послідовність.
	$order = array(
		'billing_first_name' => 10,
		'billing_last_name'  => 20,
		'billing_phone'      => 30,
		'billing_email'      => 40,
	);

	foreach ( $order as $key => $priority ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			$fields['billing'][ $key ]['priority'] = $priority;
			$fields['billing'][ $key ]['class']    = array( 'form-row-wide' );
		}
	}

	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['placeholder'] = '+380';

		// Для українського магазину телефон обов'язковий: без нього
		// служба доставки не зможе зв'язатися з покупцем.
		$fields['billing']['billing_phone']['required'] = true;
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'mytheme_checkout_fields' );

/**
 * Підставляє країну, бо поле вибору країни ми прибрали.
 *
 * WooCommerce перевіряє країну доставки перед створенням замовлення
 * (class-wc-checkout.php, рядок 1016) і без неї віддає помилку
 * «Please enter an address to continue.» Магазин працює тільки по Україні,
 * тому країна тут — константа, а не вибір покупця.
 *
 * @param array $data Дані, надіслані формою.
 * @return array
 */
function mytheme_force_checkout_country( $data ) {
	$data['billing_country']  = 'UA';
	$data['shipping_country'] = 'UA';

	return $data;
}
add_filter( 'woocommerce_checkout_posted_data', 'mytheme_force_checkout_country' );

/**
 * Чи обрано зараз доставку Новою Поштою.
 *
 * @return bool
 */
function mytheme_is_nova_poshta_selected() {
	$chosen = WC()->session ? WC()->session->get( 'chosen_shipping_methods' ) : array();
	$method = is_array( $chosen ) && isset( $chosen[0] ) ? $chosen[0] : '';

	// Ідентифікатор має вигляд «flat_rate:2», нас цікавить частина до двокрапки.
	return 0 === strpos( $method, MYTHEME_SHIPPING_NOVA_POSHTA );
}

/**
 * Поля Нової Пошти — місто й відділення.
 *
 * Виводяться нашим шаблоном form-checkout.php одразу під вибором доставки.
 * Показ і приховування робить JS, а обов'язковість перевіряється на сервері —
 * бо приховане поле може повернути будь-хто, хто вміє відкрити DevTools.
 */
function mytheme_nova_poshta_fields() {
	$checkout = WC()->checkout();

	woocommerce_form_field(
		'np_city',
		array(
			'type'        => 'text',
			'label'       => __( 'Місто', 'my-theme' ),
			'placeholder' => __( 'Почніть вводити назву міста', 'my-theme' ),
			'required'    => true,
			'class'       => array( 'form-row-wide' ),
		),
		$checkout->get_value( 'np_city' )
	);

	woocommerce_form_field(
		'np_warehouse',
		array(
			'type'        => 'text',
			'label'       => __( 'Відділення', 'my-theme' ),
			'placeholder' => __( 'Номер або адреса відділення', 'my-theme' ),
			'required'    => true,
			'class'       => array( 'form-row-wide' ),
		),
		$checkout->get_value( 'np_warehouse' )
	);
}

/**
 * Перевірка полів Нової Пошти.
 *
 * Спрацьовує тільки якщо обрано саме цей спосіб доставки — інакше при
 * самовивозі покупець не зміг би оформити замовлення без номера відділення.
 *
 * @param array    $data   Дані форми.
 * @param WP_Error $errors Сюди складаємо помилки.
 */
function mytheme_validate_nova_poshta( $data, $errors ) {
	if ( ! mytheme_is_nova_poshta_selected() ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing — nonce перевіряє сам WooCommerce.
	if ( empty( $_POST['np_city'] ) ) {
		$errors->add( 'np_city_required', __( 'Вкажіть місто доставки.', 'my-theme' ) );
	}

	if ( empty( $_POST['np_warehouse'] ) ) {
		$errors->add( 'np_warehouse_required', __( 'Вкажіть відділення Нової Пошти.', 'my-theme' ) );
	}
	// phpcs:enable
}
add_action( 'woocommerce_after_checkout_validation', 'mytheme_validate_nova_poshta', 10, 2 );

/**
 * Зберігає місто й відділення в замовлення.
 *
 * Хук спрацьовує до збереження замовлення в базу, тому окремо викликати
 * save() не потрібно — Woo зробить це сам.
 *
 * @param WC_Order $order Замовлення, яке створюється.
 */
function mytheme_save_nova_poshta_fields( $order ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( ! empty( $_POST['np_city'] ) ) {
		$order->update_meta_data( '_np_city', sanitize_text_field( wp_unslash( $_POST['np_city'] ) ) );
	}

	if ( ! empty( $_POST['np_warehouse'] ) ) {
		$order->update_meta_data( '_np_warehouse', sanitize_text_field( wp_unslash( $_POST['np_warehouse'] ) ) );
	}

	/**
	 * Дублюємо адресу доставки у стандартні поля замовлення.
	 *
	 * Мета-поля бачить лише той код, який про них знає. А стандартну адресу
	 * WooCommerce сам друкує в листах покупцю й адміну, у списку замовлень
	 * і на сторінці подяки. Без цього менеджер отримав би лист із порожньою
	 * адресою доставки.
	 */
	if ( mytheme_is_nova_poshta_selected() && ! empty( $_POST['np_city'] ) ) {
		$city      = sanitize_text_field( wp_unslash( $_POST['np_city'] ) );
		$warehouse = isset( $_POST['np_warehouse'] ) ? sanitize_text_field( wp_unslash( $_POST['np_warehouse'] ) ) : '';

		$order->set_shipping_city( $city );
		$order->set_shipping_address_1( trim( 'Нова Пошта, ' . $warehouse, ', ' ) );
		$order->set_shipping_country( 'UA' );
		$order->set_shipping_first_name( $order->get_billing_first_name() );
		$order->set_shipping_last_name( $order->get_billing_last_name() );
	}
	// phpcs:enable
}
add_action( 'woocommerce_checkout_create_order', 'mytheme_save_nova_poshta_fields' );

/**
 * Показує дані Нової Пошти в адмінці замовлення.
 *
 * Без цього менеджер бачив би замовлення без адреси доставки —
 * дані лежали б у базі, але ніде не відображались.
 *
 * @param WC_Order $order Замовлення.
 */
function mytheme_show_nova_poshta_in_admin( $order ) {
	$city      = $order->get_meta( '_np_city' );
	$warehouse = $order->get_meta( '_np_warehouse' );

	if ( ! $city && ! $warehouse ) {
		return;
	}

	echo '<p><strong>' . esc_html__( 'Нова Пошта', 'my-theme' ) . ':</strong><br>';
	echo esc_html( trim( $city . ', ' . $warehouse, ', ' ) );
	echo '</p>';
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'mytheme_show_nova_poshta_in_admin' );
