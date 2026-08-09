<?php
/**
 * Шаблон звичайної сторінки.
 *
 * Використовується для «Про нас», «Контакти», кошика, оформлення,
 * особистого кабінету — тобто для всього, що не товар і не запис блогу.
 *
 * Плашку із заголовком і крихтами малює саме цей файл, а не вміст сторінки:
 * у кошика й оформлення власного шаблону немає, вони віддають лише
 * внутрішню частину через шорткод.
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
		?>

		<section class="page-hero">
			<div class="container">
				<h1 class="page-hero__title"><?php the_title(); ?></h1>

				<?php
				get_template_part(
					'template-parts/common/breadcrumbs',
					null,
					array(
						'items' => array(
							'Home'        => home_url( '/' ),
							get_the_title() => '',
						),
					)
				);
				?>
			</div>
		</section>

		<div class="container page-content">
			<?php
			// the_content() виводить вміст сторінки з редактора.
			// Для кошика тут спрацює шорткод [woocommerce_cart],
			// який підключить woocommerce/cart/cart.php.
			the_content();
			?>
		</div>

	<?php endwhile; ?>

	<?php get_template_part( 'template-parts/common/newsletter' ); ?>
	<?php get_template_part( 'template-parts/common/insta' ); ?>

</main>

<?php
get_footer();
