<?php
/**
 * Empty cart (not in the design): same card as the cart, with a way back.
 */
defined( 'ABSPATH' ) || exit;

woocommerce_output_all_notices();
?>
<div class="l-cart l-cart--empty">
	<h1 class="l-cart__title">Pregled korpe</h1>
	<div class="l-cart__card">
		<p class="l-cart__empty wc-empty-cart-message">Korpa je prazna.</p>
	</div>
	<div class="l-cart__actions">
		<a class="l-btn l-btn--primary l-btn--pill" href="<?php echo esc_url( larita_products_url() ); ?>">POGLEDAJ MAJICE →</a>
	</div>
</div>
