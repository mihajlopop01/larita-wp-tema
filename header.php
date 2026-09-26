<?php
/**
 * Document head + site header.
 *
 * $args['variant']:
 *   default  – announcement bar + white header (product, cart, pages)
 *   hero     – announcement bar only; the home hero draws its own floating nav
 *   checkout – white header with logo + "Nazad u korpu"
 *   bare     – nothing (thank-you screen)
 */
$variant = $args['variant'] ?? 'default';
$current = larita_current_nav();
$count   = larita_cart_count();
$cart    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'l-variant-' . $variant ); ?>>
<?php wp_body_open(); ?>

<?php if ( in_array( $variant, array( 'default', 'hero' ), true ) ) : ?>
	<div class="l-announce">Dostava samo za Srbiju. Uskoro dostava za ceo Balkan.</div>
<?php endif; ?>

<?php if ( 'default' === $variant ) : ?>
	<header class="l-header">
		<button class="l-header__menu" type="button" aria-label="Meni" aria-expanded="false" data-l-menu-toggle><?php echo larita_icon( 'menu', 24 ); ?></button>
		<a class="l-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo larita_logo(); ?></a>
		<nav class="l-header__nav" aria-label="Glavni meni">
			<?php foreach ( larita_nav_items() as $key => [ $label, $url ] ) : ?>
				<a href="<?php echo esc_url( $url ); ?>"<?php echo $key === $current ? ' class="is-active" aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<a class="l-cart-pill" href="<?php echo esc_url( $cart ); ?>" aria-label="Korpa">
			<?php echo larita_icon( 'cart', 20 ); ?><span class="l-cart-pill__label">KORPA</span>
			<span class="l-cart-pill__badge"><span class="l-cart-count"><?php echo (int) $count; ?></span></span>
		</a>
	</header>
<?php elseif ( 'checkout' === $variant ) : ?>
	<header class="l-header l-header--checkout">
		<a class="l-header__back" href="<?php echo esc_url( $cart ); ?>" aria-label="Nazad u korpu"><?php echo larita_icon( 'back', 18 ); ?><span>Nazad u korpu</span></a>
		<a class="l-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo larita_logo(); ?></a>
		<span class="l-header__spacer"></span>
	</header>
<?php endif; ?>

<?php if ( in_array( $variant, array( 'default', 'hero' ), true ) ) : ?>
	<div class="l-mobile-menu" hidden data-l-menu>
		<?php foreach ( larita_nav_items() as $key => [ $label, $url ] ) : ?>
			<a href="<?php echo esc_url( $url ); ?>"<?php echo $key === $current ? ' class="is-active"' : ''; ?>><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
