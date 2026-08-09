<?php
/**
 * Інтеграція з WooCommerce.
 *
 * Підключається тільки коли плагін активний — див. functions.php.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Оголошуємо підтримку WooCommerce.
 *
 * Без цього Woo не бере шаблони з папки woocommerce/ усередині теми.
 */
function mytheme_woocommerce_support() {
	add_theme_support(
		'woocommerce',
		array(
			'single_image_width'    => 900, // головне фото на сторінці товару
			'thumbnail_image_width' => 600, // картка в каталозі
		)
	);

	/**
	 * Можливості галереї товару.
	 *
	 * Кожен рядок вмикає окремий скрипт WooCommerce:
	 *   zoom     — збільшення ділянки під курсором
	 *   lightbox — відкриття фото на весь екран по кліку (PhotoSwipe)
	 *   slider   — гортання й мініатюри під головним фото (FlexSlider)
	 *
	 * Без цих рядків Woo не підключає відповідні скрипти,
	 * і галерея лишається звичайною картинкою.
	 */
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'mytheme_woocommerce_support' );

/**
 * Прибираємо стандартні стилі WooCommerce.
 *
 * Це 120 КБ CSS, розрахованого на теми БЕЗ власної верстки. У нас вона є,
 * тому ці файли не допомагають, а заважають — і перебивають нас специфічністю:
 *
 *   .woocommerce div.product div.images { float: left; width: 48% }
 *       ламав нашу сітку — галерея стискалась до половини колонки
 *
 *   .woocommerce button.button.alt { background-color: #a46497 }
 *       фарбував кнопку «В кошик» у фірмовий бузковий колір Woo
 *
 * ⚠ НЕ чіпаємо wc-blocks-style: сторінки кошика й оформлення в цьому магазині
 * зібрані з блоків, і без нього вони розваляться.
 *
 * @param array $styles Перелік стилів, які Woo збирається підключити.
 * @return array
 */
function mytheme_dequeue_woocommerce_styles( $styles ) {
	unset( $styles['woocommerce-general'] );      // кольори, кнопки, таблиці, іконки
	unset( $styles['woocommerce-layout'] );       // сітка: float і відсоткові ширини
	unset( $styles['woocommerce-smallscreen'] );  // мобільні правила під ту сітку

	return $styles;
}
add_filter( 'woocommerce_enqueue_styles', 'mytheme_dequeue_woocommerce_styles' );

/**
 * Прибираємо стандартну таблицю підсумків кошика.
 *
 * До хука woocommerce_cart_collaterals підвішено дві дії:
 *   woocommerce_cross_sell_display  — блок «з цим купують»
 *   woocommerce_cart_totals         — таблиця підсумків із cart/cart-totals.php
 *
 * Підсумки ми малюємо самі у woocommerce/cart/cart.php, тому без цього рядка
 * на сторінці була б ДРУГА таблиця підсумків. Крос-сели лишаємо.
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

/**
 * Розмітка лічильника товарів у шапці.
 *
 * Винесено в окрему функцію, бо використовується у двох місцях:
 * у header.php при завантаженні сторінки і у фрагменті AJAX нижче.
 */
function mytheme_cart_count_html() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

	printf(
		'<span class="cart-link__count">(%d)</span>',
		(int) $count
	);
}

/**
 * Оновлення лічильника без перезавантаження сторінки.
 *
 * У каталозі кнопка «Add to cart» працює через AJAX, тому сторінка не
 * перезавантажується — і лічильник лишився б старим. WooCommerce вміє
 * підміняти шматки розмітки після AJAX-додавання: ключ масиву — це CSS-селектор
 * елемента, який треба замінити, значення — новий HTML.
 *
 * @param array $fragments Шматки розмітки для підміни.
 * @return array
 */
function mytheme_cart_count_fragment( $fragments ) {
	ob_start();
	mytheme_cart_count_html();
	$fragments['span.cart-link__count'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'mytheme_cart_count_fragment' );

/**
 * Кнопка «мінус» перед полем кількості.
 *
 * Стандартний шаблон quantity-input.php виводить саме поле без кнопок,
 * але лишає два хуки навколо нього. Тому копіювати шаблон у тему не треба —
 * достатньо вбудуватись у ці хуки. Одним оверрайдом менше.
 */
function mytheme_quantity_minus_button() {
	echo '<button class="quantity__btn" type="button" data-quantity-minus aria-label="' . esc_attr__( 'Зменшити кількість', 'my-theme' ) . '">&minus;</button>';
}
add_action( 'woocommerce_before_quantity_input_field', 'mytheme_quantity_minus_button' );

/**
 * Кнопка «плюс» після поля кількості.
 */
function mytheme_quantity_plus_button() {
	echo '<button class="quantity__btn" type="button" data-quantity-plus aria-label="' . esc_attr__( 'Збільшити кількість', 'my-theme' ) . '">+</button>';
}
add_action( 'woocommerce_after_quantity_input_field', 'mytheme_quantity_plus_button' );

/**
 * Кнопка «Buy Now» — купити одразу, без заходу в кошик.
 *
 * Це звичайна кнопка submit усередині тієї самої форми, що й «Add to Cart».
 * Тобто товар додається у кошик штатним способом Woo (з варіацією й кількістю),
 * а відрізняється лише те, куди веде сайт після цього — див. фільтр нижче.
 *
 * Хук обрано за порядком у макеті: кількість → Buy Now → Add to Cart.
 * woocommerce_after_add_to_cart_quantity є і в simple.php,
 * і в variation-add-to-cart-button.php, тому кнопка з'явиться на обох типах товару.
 */
function mytheme_buy_now_button() {
	printf(
		'<button type="submit" name="mytheme_buy_now" value="1" class="btn btn--primary single_buy_now_button">%s</button>',
		esc_html__( 'Buy Now', 'my-theme' )
	);
}
add_action( 'woocommerce_after_add_to_cart_quantity', 'mytheme_buy_now_button' );

/**
 * Після «Buy Now» ведемо покупця одразу на оформлення.
 *
 * @param string $url Адреса, куди Woo збирався перейти після додавання в кошик.
 * @return string
 */
function mytheme_buy_now_redirect( $url ) {
	// Читаємо лише наявність прапорця форми, дані нікуди не зберігаємо,
	// тому перевірка nonce тут не потрібна.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['mytheme_buy_now'] ) ) {
		return wc_get_checkout_url();
	}

	return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'mytheme_buy_now_redirect' );
