<?php
/**
 * Смуга переваг під першим екраном.
 *
 * Іконки лежать прямо в масиві як SVG-розмітка: їх чотири, вони не змінюються,
 * і окремі файли тут дали б чотири зайві запити до сервера.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

$icon_attrs = 'width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" aria-hidden="true"';

$benefits = array(
	array(
		'title' => 'Free Delivery',
		'text'  => 'Consectetur adipi elit lorem ipsum dolor sit amet.',
		'icon'  => '<svg ' . $icon_attrs . '><path d="M2 7h11v9H2z"/><path d="M13 10h4l3 3v3h-7z"/><circle cx="6" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>',
	),
	array(
		'title' => 'Quality Guarantee',
		'text'  => 'Dolor sit amet lorem ipsu mcons ectetur adipi elit.',
		'icon'  => '<svg ' . $icon_attrs . '><circle cx="12" cy="9" r="5.5"/><path d="M8.5 13.5L7 21l5-2.5L17 21l-1.5-7.5"/></svg>',
	),
	array(
		'title' => 'Daily Offers',
		'text'  => 'Amet consectetur adipi elit loreme ipsum dolor sit.',
		'icon'  => '<svg ' . $icon_attrs . '><path d="M3 12.5V4h8.5L21 13.5 13.5 21z"/><circle cx="7.5" cy="8" r="1.4"/></svg>',
	),
	array(
		'title' => '100% Secure Payment',
		'text'  => 'Rem Lopsum dolor sit amet, consectetur adipi elit.',
		'icon'  => '<svg ' . $icon_attrs . '><path d="M12 3l8 3v6c0 4.5-3.2 8.1-8 9-4.8-.9-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/></svg>',
	),
);
?>

<section class="benefits">
	<div class="container benefits__grid">
		<?php foreach ( $benefits as $benefit ) : ?>
			<article class="benefits__item">
				<span class="benefits__icon">
					<?php
					// Розмітку іконок ми написали самі кількома рядками вище,
					// тому виводимо як є — це не дані від користувача.
					echo $benefit['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</span>

				<div class="benefits__body">
					<h2 class="benefits__title"><?php echo esc_html( $benefit['title'] ); ?></h2>
					<p class="benefits__text"><?php echo esc_html( $benefit['text'] ); ?></p>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
