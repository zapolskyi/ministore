<?php
/**
 * Секція «Shop our insta» — стрічка з п'яти зображень.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

$images = array(
	'placeholder-phone.svg',
	'placeholder-watch.svg',
	'placeholder-phone.svg',
	'placeholder-audio.svg',
	'placeholder-camera.svg',
);
?>

<section class="insta">
	<div class="container">

		<h2 class="section-header">Shop Our Insta</h2>

		<ul class="insta__grid">
			<?php foreach ( $images as $image ) : ?>
				<li class="insta__item">
					<a class="insta__link" href="#" aria-label="Відкрити в Instagram">
						<img
							class="insta__image"
							src="<?php echo esc_url( MYTHEME_URI . '/assets/img/' . $image ); ?>"
							alt=""
							width="600"
							height="600"
							loading="lazy"
						>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
