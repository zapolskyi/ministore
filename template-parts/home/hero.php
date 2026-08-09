<?php
/**
 * Перший екран головної — слайдер зі стрілками.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

$slides = array(
	array(
		'title' => 'Your products<br>are great.',
		'image' => 'placeholder-watch.svg',
	),
	array(
		'title' => 'New phones<br>every week.',
		'image' => 'placeholder-phone.svg',
	),
	array(
		'title' => 'Sound that<br>fits you.',
		'image' => 'placeholder-audio.svg',
	),
);
?>

<section class="hero" data-slider aria-label="Промо-слайдер">

	<button class="hero__arrow hero__arrow--prev" type="button" data-slider-prev aria-label="Попередній слайд">
		<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
			<path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</button>

	<div class="hero__track" data-slider-track>
		<?php foreach ( $slides as $slide ) : ?>
			<div class="hero__slide">
				<div class="container hero__inner">

					<div class="hero__content">
						<h1 class="hero__title">
							<?php
							// wp_kses() лишає з розмітки лише дозволені теги — тут це <br>.
							// Так у заголовок не можна протягнути сторонній HTML.
							echo wp_kses( $slide['title'], array( 'br' => array() ) );
							?>
						</h1>
						<a class="btn btn--dark" href="#">Shop Product</a>
					</div>

					<div class="hero__media">
						<img
							class="hero__image"
							src="<?php echo esc_url( MYTHEME_URI . '/assets/img/' . $slide['image'] ); ?>"
							alt=""
							width="700"
							height="700"
						>
					</div>

				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<button class="hero__arrow hero__arrow--next" type="button" data-slider-next aria-label="Наступний слайд">
		<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
			<path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</button>

</section>
