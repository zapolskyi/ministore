<?php
/**
 * Бічна панель каталогу: пошук і фільтри.
 *
 * Чиста верстка — списки заповнені вручну, щоб було видно всі стани.
 * Далі кожен блок по черзі переводимо на реальні дані:
 *   Categories → get_terms( 'product_cat' )
 *   Tags       → get_terms( 'product_tag' )
 *   Brands     → get_terms( 'product_brand' )
 *   Price      → посилання з min_price / max_price
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

$shop_page_url = get_permalink(wc_get_page_id('shop'));

$products_categories = get_terms([
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	// Службова категорія «Uncategorized» — прибираємо її запитом, 
	// це чистіше, ніж пропускати вже отриманий термін через continue.
	'exclude'    => (int) get_option('default_product_cat'),
]);

$products_tags = get_terms([
	'taxonomy'   => 'product_tag',
	'hide_empty' => true,
]);

$products_brands = get_terms([
	'taxonomy'   => 'product_brand',
	'hide_empty' => true,
]);

/**
 * Який термін відкрито зараз — байдуже, категорія це, тег чи бренд.
 *
 * Три окремі змінні не потрібні: ID термінів у WordPress наскрізні.
 * Усі терміни всіх таксономій лежать в одній таблиці wp_terms, тому
 * категорія й тег ніколи не матимуть однакового ID — одного значення досить.
 *
 * is_tax() приймає масив таксономій; is_product_category() — це просто
 * зручна обгортка над is_tax( 'product_cat' ).
 *
 * На звичайній сторінці магазину жодного терміна не відкрито, тому тут 0.
 */
$current_term_id = is_tax( array( 'product_cat', 'product_tag', 'product_brand' ) )
	? get_queried_object_id()
	: 0;

/**
 * Діапазони цін для фільтра.
 *
 * Значення — у валюті магазину (у нас гривня). Правити треба тільки цей масив,
 * розмітка й посилання зберуться самі. max => null означає «і вище».
 */
$price_ranges = array(
	array( 'min' => 0,    'max' => 700 ),
	array( 'min' => 700,  'max' => 900 ),
	array( 'min' => 900,  'max' => 1100 ),
	array( 'min' => 1100, 'max' => 1500 ),
	array( 'min' => 1500, 'max' => null ),
);

/**
 * Що зараз стоїть у фільтрі.
 *
 * Фільтр — це навігація посиланнями, а не форма, що змінює дані,
 * тому nonce тут не потрібен. Санітизація потрібна завжди.
 */
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$current_min_price = isset( $_GET['min_price'] ) ? (float) wp_unslash( $_GET['min_price'] ) : null;
$current_max_price = isset( $_GET['max_price'] ) ? (float) wp_unslash( $_GET['max_price'] ) : null;
// phpcs:enable

/**
 * Базова адреса для посилань фільтра: поточна сторінка, але БЕЗ пагінації.
 *
 * get_pagenum_link( 1 ) віддає першу сторінку поточного запиту разом з усіма
 * наявними параметрами. Без цього клік по фільтру зі /shop/page/3/ повів би
 * на третю сторінку результату, у якому сторінка одна — тобто в 404.
 */
$filter_base_url = get_pagenum_link( 1, false );

$price_filter_active = ( null !== $current_min_price || null !== $current_max_price );
?>

<aside class="shop-sidebar" aria-label="Фільтри каталогу">

	<div class="widget">
		<?php
		/**
		 * Пошук по товарах.
		 *
		 * method="get" — щоб запит потрапив в адресу: ?s=watch&post_type=product.
		 * Тоді результат можна переслати посиланням, а «назад» працює як треба.
		 *
		 * action веде на корінь сайту: саме там WordPress обробляє пошук.
		 * Приховане поле post_type обмежує пошук товарами — без нього
		 * у результати полізуть сторінки й записи блогу.
		 */
		?>
		<form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
			<label class="visually-hidden" for="shop-search">Пошук по товарах</label>
			<input
				class="search-form__input"
				type="search"
				id="shop-search"
				name="s"
				placeholder="Search"
				value="<?php echo esc_attr(get_search_query()); ?>"
			>
			<input type="hidden" name="post_type" value="product">
			<button class="search-form__submit" type="submit" aria-label="Шукати">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
					<circle cx="11" cy="11" r="7"/>
					<path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
				</svg>
			</button>
		</form>
	</div>

	<div class="widget">
		<h2 class="widget__title">Categories</h2>
		<ul class="widget__list">
			<li class="widget__item">
				<?php // «All» = всі категорії, тому воно активне, доки ми не всередині категорії. ?>
				<a
					class="widget__link<?php echo !is_product_category() ? ' widget__link--active' : ''; ?>"
					href="<?php echo esc_url($shop_page_url); ?>"
				>All</a>
			</li>
			<?php if (!empty($products_categories) && !is_wp_error($products_categories)) : ?>
			<?php foreach ($products_categories as $category) : ?>
				<?php
				$category_link = get_term_link($category);

				$is_active = ($category->term_id === $current_term_id);
				?>
				<li class="widget__item">
					<a
						class="widget__link<?php echo $is_active ? ' widget__link--active' : ''; ?>"
						href="<?php echo esc_url($category_link); ?>"
						<?php echo $is_active ? 'aria-current="page"' : ''; ?>
					>
						<?php echo esc_html($category->name); ?>
					</a>
				</li>
			<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>

	<div class="widget">
		<h2 class="widget__title">Tags</h2>
		<ul class="widget__list">
			<?php if (!empty($products_tags) && !is_wp_error($products_tags)) : ?>
			<?php foreach ($products_tags as $tag) : ?>
				<?php
				$tag_link = get_term_link($tag);

				$is_active = ($tag->term_id === $current_term_id);
				?>
				<li class="widget__item">
					<?php // aria-current має бути АТРИБУТОМ усередині <a>, а не текстом після нього. ?>
					<a
						class="widget__link<?php echo $is_active ? ' widget__link--active' : ''; ?>"
						href="<?php echo esc_url($tag_link); ?>"
						<?php echo $is_active ? 'aria-current="page"' : ''; ?>
					><?php echo esc_html($tag->name); ?></a>
				</li>
			<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>

	<div class="widget">
		<h2 class="widget__title">Brands</h2>
		<ul class="widget__list">
			<?php if (!empty($products_brands) && !is_wp_error($products_brands)) : ?>
			<?php foreach ($products_brands as $brand) : ?>	
				<?php
				$brand_link = get_term_link($brand);

				$is_active = ($brand->term_id === $current_term_id);
				?>
				<li class="widget__item">
					<a
						class="widget__link<?php echo $is_active ? ' widget__link--active' : ''; ?>"
						href="<?php echo esc_url($brand_link); ?>"
						<?php echo $is_active ? 'aria-current="page"' : ''; ?>
					><?php echo esc_html($brand->name); ?></a>
				</li>
			<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	</div>

	<div class="widget">
		<h2 class="widget__title">Filter by price</h2>
		<ul class="widget__list">
			<?php foreach ($price_ranges as $range) : ?>
				<?php
				// Збираємо параметри посилання. add_query_arg() із базовою адресою
				// ДОДАЄ параметри до вже наявних, а не замінює їх —
				// тому ціна складається з обраною категорією, тегом і брендом.
				$range_url = add_query_arg('min_price', $range['min'], $filter_base_url);

				if (null === $range['max']) {
					// «і вище» — верхньої межі немає, прибираємо параметр зовсім.
					$range_url = remove_query_arg('max_price', $range_url);
					$range_label = sprintf('%s+', wc_price($range['min']));
				} else {
					$range_url = add_query_arg('max_price', $range['max'], $range_url);
					$range_label = 0 === $range['min']
						? sprintf('Less than %s', wc_price($range['max']))
						: sprintf('%s - %s', wc_price($range['min']), wc_price($range['max']));
				}

				// Діапазон активний, коли ОБИДВІ межі збігаються з тим, що в адресі.
				$range_max = null === $range['max'] ? null : (float) $range['max'];
				$is_active = ($current_min_price === (float) $range['min'] && $current_max_price === $range_max);
				?>
				<li class="widget__item">
					<a
						class="widget__link<?php echo $is_active ? ' widget__link--active' : ''; ?>"
						href="<?php echo esc_url($range_url); ?>"
						<?php echo $is_active ? 'aria-current="page"' : ''; ?>
					><?php echo wp_kses_post($range_label); ?></a>
				</li>
			<?php endforeach; ?>

			<?php if ($price_filter_active) : ?>
				<li class="widget__item">
					<?php // remove_query_arg() робить зворотну дію — знімає фільтр, лишаючи решту. ?>
					<a
						class="widget__link widget__link--reset"
						href="<?php echo esc_url(remove_query_arg(array('min_price', 'max_price'), $filter_base_url)); ?>"
					>&times; Clear price</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>

</aside>
