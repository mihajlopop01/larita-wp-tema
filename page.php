<?php
/**
 * Pages. Cart and checkout render WooCommerce's classic templates (overridden in
 * theme/woocommerce/) instead of the page's block content; other pages
 * (legal/info) get a white content card, which the design leaves unspecified.
 */

if ( function_exists( 'is_cart' ) && is_cart() ) {
	get_header( null, array( 'variant' => 'default' ) );
	echo '<main class="l-cart-page">';
	echo '<div class="l-crumbs l-crumbs--bar"><a href="' . esc_url( home_url( '/' ) ) . '">Početna</a><span>›</span><span class="is-current">Korpa</span></div>';
	larita_render_cart();
	echo '</main>';
	get_footer( null, array( 'variant' => 'bar' ) );
	return;
}

if ( function_exists( 'is_checkout' ) && is_checkout() ) {
	$thanks = is_wc_endpoint_url( 'order-received' );
	get_header( null, array( 'variant' => $thanks ? 'bare' : 'checkout' ) );
	echo '<main class="' . ( $thanks ? 'l-thanks-page' : 'l-checkout-page' ) . '">';
	larita_render_checkout();
	echo '</main>';
	get_footer( null, array( 'variant' => 'none' ) );
	return;
}

get_header( null, array( 'variant' => 'default' ) );
?>
<main class="l-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<article class="l-page__card">
			<h1 class="l-page__title"><?php the_title(); ?></h1>
			<div class="l-page__content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer( null, array( 'variant' => 'full' ) );
