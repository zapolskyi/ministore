<?php
/**
 * Резервний шаблон.
 *
 * WordPress вимагає наявності index.php — це останній шаблон в ієрархії,
 * який використовується, коли жоден інший не підійшов.
 * Повноцінно заповнимо його у фазі 1 (блог і архіви).
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="site-main container">
	<?php
	while ( have_posts() ) :
		the_post();

		the_title( '<h1 class="entry-title">', '</h1>' );
		the_content();

	endwhile;
	?>
</main>

<?php
get_footer();
