<?php
/**
 * Підключення стилів і скриптів.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Повертає версію файлу за часом його останньої зміни.
 *
 * Навіщо: WordPress додає версію до URL — style.css?ver=1712345678.
 * Змінився файл — змінилось число — браузер завантажує новий CSS.
 * Без цього ти правиш стилі, а замовник далі бачить старі з кешу.
 *
 * @param string $relative_path Шлях від кореня теми, напр. '/assets/css/style.css'.
 * @return string
 */
function mytheme_asset_version( $relative_path ) {
	$absolute_path = MYTHEME_DIR . $relative_path;

	return file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : MYTHEME_VERSION;
}

/**
 * Підключає ресурси фронтенду.
 */
function mytheme_enqueue_assets() {

	// Шрифт Jost — геометричний гротеск, найближчий до макета.
	wp_enqueue_style(
		'mytheme-fonts',
		'https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&display=swap',
		array(),
		null // Google сам версіонує свій CSS — своя версія тут тільки заважає.
	);

	// Головний стиль: працює на всіх сторінках сайту.
	wp_enqueue_style(
		'mytheme-style',
		MYTHEME_URI . '/assets/css/style.css',
		array( 'mytheme-fonts' ),
		mytheme_asset_version( '/assets/css/style.css' )
	);

	// Стилі магазину: важкі, тому вантажимо лише там, де вони потрібні.
	if ( mytheme_needs_shop_styles() ) {
		wp_enqueue_style(
			'mytheme-shop',
			MYTHEME_URI . '/assets/css/woocommerce.css',
			array( 'mytheme-style' ), // залежність гарантує правильний порядок підключення
			mytheme_asset_version( '/assets/css/woocommerce.css' )
		);
	}

	// Скрипти. defer — браузер не зупиняє парсинг HTML заради завантаження файлу.
	wp_enqueue_script(
		'mytheme-main',
		MYTHEME_URI . '/assets/js/main.js',
		array(),
		mytheme_asset_version( '/assets/js/main.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_assets' );

/**
 * Чи потрібні на цій сторінці стилі товарів.
 *
 * Головна теж у списку: там є картки товарів і стрічка «Mobile products»,
 * а їхні стилі живуть у бандлі магазину.
 *
 * @return bool
 */
function mytheme_needs_shop_styles() {
	if ( is_front_page() ) {
		return true;
	}

	// Умовні теги WooCommerce існують лише коли плагін активний.
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}

	// is_woocommerce() — це каталог, категорія й сторінка товару.
	// Кошик, оформлення й кабінет під нього не підпадають, тому перевіряємо окремо.
	return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
}