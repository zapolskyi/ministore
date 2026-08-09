<?php
/**
 * Хлібні крихти.
 *
 * Очікує $args['items'] — масив пар «назва => посилання».
 * Останній елемент виводиться без посилання: це поточна сторінка.
 *
 * @package my-theme
 */

defined( 'ABSPATH' ) || exit;

$items = $args['items'] ?? array();

if ( empty( $items ) ) {
	return;
}

$last_index = count( $items ) - 1;
$index      = 0;
?>

<nav class="breadcrumbs" aria-label="Хлібні крихти">
	<?php foreach ( $items as $label => $url ) : ?>

		<?php if ( $index === $last_index ) : ?>
			<span class="breadcrumbs__current" aria-current="page"><?php echo esc_html( $label ); ?></span>
		<?php else : ?>
			<a class="breadcrumbs__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			<span class="breadcrumbs__sep" aria-hidden="true">&gt;</span>
		<?php endif; ?>

		<?php ++$index; ?>
	<?php endforeach; ?>
</nav>
