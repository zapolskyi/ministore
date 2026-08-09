<?php
/**
 * Темний банер підписки над підвалом.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="newsletter">
	<div class="container newsletter__inner">

		<div class="newsletter__text">
			<h2 class="newsletter__title">Subscribe Us Now</h2>
			<p class="newsletter__subtitle">
				Get latest news, updates and deals directly mailed to your inbox.
			</p>
		</div>

		<form class="newsletter__form" action="#" method="post">
			<label class="visually-hidden" for="newsletter-email">Ваш e-mail</label>
			<input
				class="newsletter__input"
				type="email"
				id="newsletter-email"
				name="email"
				placeholder="Your email address here"
				required
			>
			<button class="btn btn--primary newsletter__submit" type="submit">Subscribe</button>
		</form>

	</div>
</section>
