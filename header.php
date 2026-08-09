<?php
/**
 * Шапка сайту: <head>, хедер, навігація.
 *
 * Меню поки статичне — на етапі верстки це нормально.
 * У фазі 1 воно переїде на wp_nav_menu() і керуватиметься з адмінки.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); // Обов'язково. Сюди WordPress вставляє всі стилі, скрипти й мета-теги. ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link visually-hidden" href="#content">Перейти до вмісту</a>

<header class="site-header">
	<div class="container site-header__inner">

		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			MiniStore<span class="logo__dot">.</span>
		</a>

		<button
			class="burger"
			type="button"
			aria-label="Відкрити меню"
			aria-expanded="false"
			aria-controls="site-nav"
		>
			<span class="burger__line"></span>
			<span class="burger__line"></span>
			<span class="burger__line"></span>
		</button>

		<nav class="site-nav" id="site-nav" aria-label="Головне меню">
			<?php wp_nav_menu([
				'theme_location' => 'primary',
				'container' => false,
				'menu_class' => 'site-nav__list',
				'fallback_cb' => false,
			]);
			?>
		</nav>

		<div class="site-header__actions">
			<button class="icon-btn" type="button" aria-label="Пошук">
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
					<circle cx="11" cy="11" r="7"/>
					<path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
				</svg>
			</button>

			<a class="icon-btn" href="#" aria-label="Мій акаунт">
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
					<circle cx="12" cy="8" r="4"/>
					<path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7" stroke-linecap="round"/>
				</svg>
			</a>

			<a class="icon-btn cart-link" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#' ); ?>" aria-label="Кошик">
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
					<path d="M3 4h2.5l2 12h11l2-8H7" stroke-linecap="round" stroke-linejoin="round"/>
					<circle cx="9.5" cy="20" r="1.4" fill="currentColor" stroke="none"/>
					<circle cx="17.5" cy="20" r="1.4" fill="currentColor" stroke="none"/>
				</svg>
				<?php
				// Лічильник живий: після AJAX-додавання з каталогу
				// WooCommerce підмінює цей <span> через фрагменти.
				if ( function_exists( 'mytheme_cart_count_html' ) ) {
					mytheme_cart_count_html();
				}
				?>
			</a>
		</div>

	</div>
</header>

<div id="content" class="site-content">
