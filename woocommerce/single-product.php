<?php
/**
 * Сторінка одного товару.
 *
 * WooCommerce підхоплює цей файл для адреси окремого товару
 * (/product/назва-товару/).
 *
 * Цикл have_posts() обов'язковий: саме the_post() змушує WooCommerce
 * покласти в глобальну змінну $product справжній об'єкт товару.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="site-main">

	<?php
	while ( have_posts() ) :
		the_post();

		// Шлях рахується ВІД КОРЕНЯ ТЕМИ, а не від папки цього файлу.
		get_template_part( 'woocommerce/single-product/product-main' );

	endwhile;
	?>

	<?php get_template_part( 'template-parts/common/newsletter' ); ?>
	<?php get_template_part( 'template-parts/common/insta' ); ?>

</main>

<?php
get_footer();
