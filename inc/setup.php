<?php
/**
 * Базові можливості теми.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Реєструє підтримку можливостей WordPress.
 *
 * Хук after_setup_theme спрацьовує одразу після завантаження теми —
 * це стандартне місце для всіх add_theme_support().
 */
function mytheme_setup() {

	// WordPress сам формує <title> сторінки. Без цього тег треба писати руками в header.php.
	add_theme_support( 'title-tag' );

	// Мініатюри записів і товарів.
	add_theme_support( 'post-thumbnails' );

	// Сучасна HTML5-розмітка замість застарілої XHTML у вбудованих формах.
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// Підтримка WooCommerce оголошується в inc/woocommerce-setup.php —
	// разом з рештою налаштувань магазину.
}
add_action( 'after_setup_theme', 'mytheme_setup' );
