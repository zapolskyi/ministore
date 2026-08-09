<?php
/**
 * Точка входу теми.
 *
 * Тут тільки константи й підключення модулів з inc/.
 * Логіки в цьому файлі не буде — вона живе в окремих файлах за призначенням.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

define( 'MYTHEME_VERSION', '0.1.0' );
define( 'MYTHEME_DIR', get_template_directory() );      // шлях на диску: /.../themes/my-theme
define( 'MYTHEME_URI', get_template_directory_uri() );  // URL: https://сайт/wp-content/themes/my-theme

require_once MYTHEME_DIR . '/inc/setup.php';
require_once MYTHEME_DIR . '/inc/enqueue.php';
require_once MYTHEME_DIR . '/inc/menus.php';

// Магазин підключаємо лише коли плагін увімкнено — інакше вимкнення
// WooCommerce поклало б увесь сайт з fatal error.
if ( class_exists( 'WooCommerce' ) ) {
	require_once MYTHEME_DIR . '/inc/woocommerce-setup.php';
	require_once MYTHEME_DIR . '/inc/checkout.php';
}