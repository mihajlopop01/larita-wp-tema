<?php
/**
 * Home: hero with floating nav, product grid, footer.
 */
get_header( null, array( 'variant' => 'hero' ) );
$current = larita_current_nav();
?>
<main class="l-home">
	<section class="l-hero">
		<div class="l-hero__bar">
			<nav class="l-hero__nav" aria-label="Glavni meni">
				<?php foreach ( larita_nav_items() as $key => [ $label, $url ] ) : ?>
					<a href="<?php echo esc_url( $url ); ?>"<?php echo $key === $current ? ' class="is-active" aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>
			<button class="l-hero__menu" type="button" aria-label="Meni" aria-expanded="false" data-l-menu-toggle><?php echo larita_icon( 'menu', 22 ); ?></button>
			<a class="l-cart-pill l-cart-pill--hero" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="Korpa">
				<?php echo larita_icon( 'cart', 20 ); ?><span class="l-cart-pill__label">KORPA</span>
				<span class="l-cart-pill__badge"><span class="l-cart-count"><?php echo (int) larita_cart_count(); ?></span></span>
			</a>
		</div>
		<h1 class="l-hero__logo"><?php echo larita_logo(); ?></h1>
		<a class="l-hero__cta" href="#proizvodi">NOVE LARITA MAJICE SU U PRODAJI</a>
		<a class="l-hero__down" href="#proizvodi" aria-label="Na proizvode"><?php echo larita_chevrons( 64, 34, 6 ); ?></a>
	</section>

	<?php get_template_part( 'template-parts/product-grid' ); ?>
</main>
<?php
get_footer( null, array( 'variant' => 'full' ) );
