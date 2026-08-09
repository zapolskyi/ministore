<?php
/**
 * Меню сайту.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Реєструє позиції меню.
 */
function mytheme_register_nav_menus() {
	register_nav_menus(
		array(
			'primary' => __( 'Головне меню', 'my-theme' ),
		)
	);
}
add_action( 'init', 'mytheme_register_nav_menus' );

// -----------------------------------------------------------------------------
// BEM-класи для меню з адмінки.
//
// wp_nav_menu() друкує власні класи (menu-item, sub-menu) і зовсім не додає
// класів посиланням. Наш SCSS написаний під site-nav__item / site-nav__link,
// тому класи дописуємо фільтрами.
//
// Альтернатива — свій Nav Walker, але це цілий клас заради трьох рядків.
// -----------------------------------------------------------------------------

/**
 * Класи для <li> пункту меню.
 *
 * @param string[] $classes Класи пункту.
 * @param WP_Post  $item    Пункт меню.
 * @param stdClass $args    Аргументи wp_nav_menu().
 * @return string[]
 */
function mytheme_nav_item_class( $classes, $item, $args ) {
	if ( 'primary' !== $args->theme_location ) {
		return $classes;
	}

	$classes[] = 'site-nav__item';

	// WordPress сам позначає пункти з вкладеними — переносимо це у свій клас.
	if ( in_array( 'menu-item-has-children', $classes, true ) ) {
		$classes[] = 'site-nav__item--has-children';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'mytheme_nav_item_class', 10, 3 );

/**
 * Атрибути <a> пункту меню.
 *
 * @param array    $atts Атрибути посилання.
 * @param WP_Post  $item Пункт меню.
 * @param stdClass $args Аргументи wp_nav_menu().
 * @return array
 */
function mytheme_nav_link_attributes( $atts, $item, $args ) {
	if ( 'primary' !== $args->theme_location ) {
		return $atts;
	}

	$atts['class'] = 'site-nav__link';

	// Поточна сторінка або її батько — підсвічуємо акцентним кольором.
	if ( ! empty( $item->current ) || ! empty( $item->current_item_ancestor ) ) {
		$atts['class'] .= ' site-nav__link--current';
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'mytheme_nav_link_attributes', 10, 3 );

/**
 * Класи для <ul> вкладеного меню.
 *
 * @param string[] $classes Класи підменю.
 * @param stdClass $args    Аргументи wp_nav_menu().
 * @return string[]
 */
function mytheme_nav_submenu_class( $classes, $args ) {
	if ( 'primary' !== $args->theme_location ) {
		return $classes;
	}

	$classes[] = 'site-nav__submenu';

	return $classes;
}
add_filter( 'nav_menu_submenu_css_class', 'mytheme_nav_submenu_class', 10, 2 );
