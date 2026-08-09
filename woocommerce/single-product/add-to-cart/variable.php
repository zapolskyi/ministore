<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// В оригіналі тут ще був $attribute_keys — він потрібен лише для того,
// щоб домалювати «Clear» після останнього атрибута. Ми виводимо це посилання
// один раз після циклу, тому змінна більше не потрібна.
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<?php
		/**
		 * ЗМІНЕНО ПРОТИ ОРИГІНАЛУ WooCommerce.
		 *
		 * Оригінал малює <table class="variations"> з <select> у комірках.
		 * Ми виводимо кнопки-свотчі за макетом, але лишаємо все, на що
		 * спирається скрипт Woo (add-to-cart-variation.js):
		 *
		 *   .variations         — усередині нього він шукає селекти (рядок 10)
		 *   .variations select  — самі поля, їх ховає CSS, а не disabled
		 *   .reset_variations   — посилання «Clear» (рядок 13)
		 *
		 * Прибрати <select> не можна: саме він зберігає вибір і саме його
		 * читає Woo, коли шукає потрібну варіацію. Кнопки лише пишуть у нього
		 * значення — див. initVariationSwatches() в assets/js/main.js.
		 */
		?>
		<div class="variations product__variations">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<div class="product__option">

					<h2 class="product__option-title">
						<label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
							<?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?>
						</label>
					</h2>

					<div class="product__option-field">
						<?php
						// Малює <select id="color" name="attribute_color"> з потрібними
						// опціями, вибраним значенням і значенням за замовчуванням.
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
							)
						);
						?>
					</div>

					<div class="swatches" data-swatches-for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
						<?php foreach ( $options as $option ) : ?>
							<?php
							/**
							 * У глобальному атрибуті $option — це слаг терміна,
							 * тому для підпису треба дістати сам термін.
							 * У локального атрибута слаг і назва збігаються.
							 */
							$option_label = $option;

							if ( taxonomy_exists( $attribute_name ) ) {
								$term = get_term_by( 'slug', $option, $attribute_name );

								if ( $term && ! is_wp_error( $term ) ) {
									$option_label = $term->name;
								}
							}
							?>
							<button
								class="swatches__item"
								type="button"
								data-value="<?php echo esc_attr( $option ); ?>"
								aria-pressed="false"
							><?php echo esc_html( $option_label ); ?></button>
						<?php endforeach; ?>
					</div>

				</div>
			<?php endforeach; ?>

			<?php
			/**
			 * Filters the reset variation button.
			 *
			 * @since 2.5.0
			 *
			 * @param string $button The reset variation button HTML.
			 */
			echo wp_kses_post(
				apply_filters(
					'woocommerce_reset_variations_link',
					'<a class="reset_variations product__variations-reset" href="#" aria-label="' . esc_attr__( 'Clear options', 'woocommerce' ) . '">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>'
				)
			);
			?>
		</div>
		<div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
		<?php
		// Reset snapshot for cases where a theme/plugin loads the variation form later, like quick-view modals.
		if ( \Automattic\WooCommerce\Internal\VariationGallery\Package::is_enabled() ) :
			?>
			<script type="text/template" class="wc-product-gallery-default-template"><?php echo wc_get_product_gallery_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
			<?php
		endif;
		?>
		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
