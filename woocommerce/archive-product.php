<?php
/**
 * Каталог товарів.
 *
 * Цей файл WooCommerce підхоплює для сторінки магазину, категорій і тегів
 * товарів — достатньо покласти його в папку woocommerce/ всередині теми.
 *
 * Дані — з головного циклу WordPress: запит уже сформований, товари в ньому є,
 * лишається їх вивести. Одна картка — woocommerce/content-product.php.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

?>

<main class="site-main">

	<section class="page-hero">
		<div class="container">
			<h1 class="page-hero__title"><?php woocommerce_page_title(); ?></h1>
			<?php
			woocommerce_breadcrumb();
			?>
		</div>
	</section>

	<div class="container shop">
		<div class="shop__layout">

			<div class="shop__content">

				<?php if ( woocommerce_product_loop() ) : ?>

					<div class="shop__toolbar">
						<?php
						// Обидві функції друкують готову розмітку самі:
						// result_count — свій <p>, ordering — свою <form> із <select>.
						// Загортати їх у власні теги не треба.
						woocommerce_result_count();
						woocommerce_catalog_ordering();
						?>
					</div>

					<ul class="product-grid">
						<?php
						while ( have_posts() ) {
							the_post();

							wc_get_template_part( 'content', 'product' );
						}
						?>
					</ul>

					<div class="shop__pagination">
						<?php woocommerce_pagination(); ?>
					</div>

				<?php else : ?>

					<?php
					// Каталог порожній або фільтр нічого не знайшов —
					// показуємо повідомлення, а не порожню сторінку.
					wc_no_products_found();
					?>

				<?php endif; ?>

			</div>

			<?php get_template_part( 'template-parts/shop/sidebar' ); ?>

		</div>
	</div>

	<?php get_template_part( 'template-parts/common/newsletter' ); ?>
	<?php get_template_part( 'template-parts/common/insta' ); ?>

</main>

<?php
get_footer();
