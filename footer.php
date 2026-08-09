<?php
/**
 * Підвал сайту.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;
?>
</div><!-- /.site-content -->

<footer class="site-footer">
	<div class="container site-footer__inner">

		<div class="site-footer__col site-footer__col--brand">
			<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				MiniStore<span class="logo__dot">.</span>
			</a>
			<p class="site-footer__text">
				Nisi, purus vitae, ultricies nunc. Sit ac elit suscipit hendrerit.
				Gravida massa volutpat aenean odio erat nullam fringilla.
			</p>
			<ul class="socials">
				<li>
					<a class="socials__link" href="#" aria-label="Facebook">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8.5V7c0-.8.2-1.2 1.3-1.2H17V3h-2.6C11.7 3 11 4.4 11 6.6v1.9H9V12h2v9h3v-9h2.3l.4-3.5H14z"/></svg>
					</a>
				</li>
				<li>
					<a class="socials__link" href="#" aria-label="Instagram">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/></svg>
					</a>
				</li>
				<li>
					<a class="socials__link" href="#" aria-label="Twitter">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 5.9c-.7.3-1.5.5-2.4.6.9-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1a4.1 4.1 0 0 0-7 3.7A11.6 11.6 0 0 1 3.4 4.6a4.1 4.1 0 0 0 1.3 5.5c-.7 0-1.3-.2-1.9-.5 0 2 1.4 3.7 3.3 4.1-.6.2-1.2.2-1.9.1a4.1 4.1 0 0 0 3.8 2.9A8.3 8.3 0 0 1 2 18.4a11.6 11.6 0 0 0 6.3 1.8c7.5 0 11.7-6.3 11.7-11.7v-.5c.8-.6 1.5-1.3 2-2.1z"/></svg>
					</a>
				</li>
				<li>
					<a class="socials__link" href="#" aria-label="LinkedIn">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.9 8.5H4V20h2.9V8.5zM5.4 4a1.7 1.7 0 1 0 0 3.4 1.7 1.7 0 0 0 0-3.4zM20 13.4c0-3.1-1.7-4.6-3.9-4.6-1.8 0-2.6 1-3 1.7V8.5H10V20h3v-6.3c0-1.4.8-2.1 1.8-2.1s1.7.7 1.7 2.1V20H20v-6.6z"/></svg>
					</a>
				</li>
				<li>
					<a class="socials__link" href="#" aria-label="YouTube">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12s0-3.3-.4-4.9a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4a2.5 2.5 0 0 0-1.8 1.7C2 8.7 2 12 2 12s0 3.3.4 4.9a2.5 2.5 0 0 0 1.8 1.7C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.7c.4-1.6.4-4.9.4-4.9zM10 15V9l5.2 3-5.2 3z"/></svg>
					</a>
				</li>
			</ul>
		</div>

		<div class="site-footer__col">
			<h3 class="site-footer__title">Quick Links</h3>
			<ul class="site-footer__list">
				<li><a class="site-footer__link site-footer__link--current" href="#">Home</a></li>
				<li><a class="site-footer__link" href="#">About</a></li>
				<li><a class="site-footer__link" href="#">Shop</a></li>
				<li><a class="site-footer__link" href="#">Blogs</a></li>
				<li><a class="site-footer__link" href="#">Contact</a></li>
			</ul>
		</div>

		<div class="site-footer__col">
			<h3 class="site-footer__title">Help &amp; Info</h3>
			<ul class="site-footer__list">
				<li><a class="site-footer__link" href="#">Track Your Order</a></li>
				<li><a class="site-footer__link" href="#">Returns Policies</a></li>
				<li><a class="site-footer__link" href="#">Shipping + Delivery</a></li>
				<li><a class="site-footer__link" href="#">Contact Us</a></li>
				<li><a class="site-footer__link" href="#">FAQs</a></li>
			</ul>
		</div>

		<div class="site-footer__col">
			<h3 class="site-footer__title">Contact Us</h3>
			<p class="site-footer__text">
				Do you have any queries or suggestions?
				<a class="site-footer__contact" href="mailto:yourinfo@gmail.com">yourinfo@gmail.com</a>
			</p>
			<p class="site-footer__text">
				If you need support? Just give us a call.
				<a class="site-footer__contact" href="tel:+551112223334">+55 111 222 333 44</a>
			</p>
		</div>

	</div>

	<div class="site-footer__bottom">
		<div class="container site-footer__bottom-inner">
			<div class="site-footer__badges">
				<span class="site-footer__badges-label">We ship with</span>
				<span class="badge">DHL</span>
				<span class="badge">FedEx</span>
			</div>

			<div class="site-footer__badges">
				<span class="site-footer__badges-label">Payment options:</span>
				<span class="badge">Visa</span>
				<span class="badge">MC</span>
				<span class="badge">Amex</span>
			</div>

			<p class="site-footer__copy">
				&copy; Copyright <?php echo esc_html( gmdate( 'Y' ) ); ?> MiniStore.
				Design by <a href="#">TemplatesJungle</a>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); // Обов'язково. Сюди WordPress вставляє скрипти підвалу. ?>
</body>
</html>
