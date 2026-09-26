<?php
/**
 * Cart (mockup screen 05, "Pregled korpe").
 *
 * Keeps WooCommerce's form contract (woocommerce-cart-form, cart[key][qty],
 * update_cart, nonce, .product-remove > a) so wc cart.js handles AJAX
 * quantity updates and removal. Desktop = 5-column table, mobile = stacked
 * rows (same markup, CSS grid areas).
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>
<form class="woocommerce-cart-form l-cart" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" data-count="<?php echo (int) WC()->cart->get_cart_contents_count(); ?>">
	<h1 class="l-cart__title">Pregled korpe</h1>
	<div class="l-cart__card woocommerce-cart-form__contents">
		<div class="l-cart__cols"><span>PROIZVOD</span><span>CENA</span><span>KOLIČINA</span><span>UKUPNO</span><span></span></div>

		<?php
		foreach ( WC()->cart->get_cart() as $key => $item ) :
			$_product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
			if ( ! $_product || ! $_product->exists() || $item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $item, $key ) ) {
				continue;
			}
			$link  = $_product->is_visible() ? $_product->get_permalink( $item ) : '';
			$name  = $_product->is_type( 'variation' ) ? wc_get_product( $item['product_id'] )->get_name() : $_product->get_name();
			$thumb = wp_get_attachment_image_url( $_product->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src();
			?>
			<div class="l-cart-row woocommerce-cart-form__cart-item cart_item">
				<div class="l-cart-row__product">
					<span class="l-thumbframe"><img src="<?php echo esc_url( $thumb ); ?>" alt=""></span>
					<div class="l-cart-row__info">
						<?php if ( $link ) : ?>
							<a class="l-cart-row__name" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a>
						<?php else : ?>
							<span class="l-cart-row__name"><?php echo esc_html( $name ); ?></span>
						<?php endif; ?>
						<div class="l-cart-row__variant"><?php echo esc_html( larita_cart_item_variant( $item ) ); ?></div>
					</div>
				</div>
				<span class="l-cart-row__price"><?php echo esc_html( larita_price( $_product->get_price() ) ); ?></span>
				<div class="l-cart-row__qty">
					<div class="l-qty l-qty--pill" data-l-cart-qty>
						<button type="button" aria-label="Manje" data-step="-1">−</button>
						<input type="number" class="qty" name="cart[<?php echo esc_attr( $key ); ?>][qty]" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="0" step="1" inputmode="numeric" aria-label="Količina" <?php echo $_product->is_sold_individually() ? 'readonly' : ''; ?>>
						<button type="button" aria-label="Više" data-step="1">+</button>
					</div>
				</div>
				<span class="l-cart-row__total"><?php echo esc_html( larita_price( $item['line_subtotal'] ) ); ?></span>
				<div class="l-cart-row__remove product-remove">
					<a class="l-remove" href="<?php echo esc_url( wc_get_cart_remove_url( $key ) ); ?>" aria-label="Ukloni" data-product_id="<?php echo esc_attr( $item['product_id'] ); ?>"><?php echo larita_icon( 'trash', 18 ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="l-cart__sum"><span class="l-cart__sum-label">UKUPNO:</span><span class="l-cart__sum-value"><?php echo esc_html( larita_price( WC()->cart->get_subtotal() ) ); ?></span></div>
	</div>

	<div class="l-cart__actions">
		<div class="l-cart__buttons">
			<a class="l-btn l-btn--outline l-btn--flat l-btn--pill l-cart__more" href="<?php echo esc_url( larita_products_url() ); ?>">DODAJ JOŠ PROIZVODA</a>
			<a class="l-btn l-btn--primary l-btn--pill l-cart__order" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">NARUČI →</a>
		</div>
		<p class="l-terms">Klikom na dugme <b>Naruči</b> potvrđujem da sam saglasna/an sa <a href="<?php echo esc_url( larita_page_url( 'uslovi-koriscenja' ) ); ?>">Uslovima korišćenja</a> i <a href="<?php echo esc_url( larita_page_url( 'politika-privatnosti' ) ); ?>">Politikom privatnosti</a>.</p>
	</div>

	<button type="submit" name="update_cart" value="1" class="l-hidden-submit" tabindex="-1" aria-hidden="true">Ažuriraj</button>
	<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
</form>
<?php
do_action( 'woocommerce_after_cart' );
