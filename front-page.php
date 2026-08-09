<?php
/**
 * Головна сторінка.
 *
 * front-page.php WordPress підхоплює для головної автоматично —
 * жодних налаштувань в адмінці не потрібно, і шаблон обирати теж не треба.
 *
 * Сам файл нічого не верстає: він лише підключає секції по черзі.
 * Кожна секція — окремий файл у template-parts/home/.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="site-main">

	<?php get_template_part( 'template-parts/home/hero' ); ?>
	<?php get_template_part( 'template-parts/home/benefits' ); ?>
	<?php get_template_part( 'template-parts/home/featured-products' ); ?>

	<?php get_template_part( 'template-parts/common/newsletter' ); ?>
	<?php get_template_part( 'template-parts/common/insta' ); ?>

</main>

<?php
get_footer();
