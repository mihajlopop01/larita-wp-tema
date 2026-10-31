<?php
/**
 * Payment box + "ZAVRŠI KUPOVINU". Returned as an AJAX fragment, so the root
 * keeps id="payment" / woocommerce-checkout-payment.
 *
 * One gateway: its radio stays in the form (hidden) so WooCommerce receives payment_method
 * as usual. Several (card next to COD): one visible box each. COD only in Serbia (functions.php).
 */
defined( 'ABSPATH' ) || exit;

$gateways = WC()->cart->needs_payment() ? WC()->payment_gateways()->get_available_payment_gateways() : array();
$first    = $gateways ? array_key_first( $gateways ) : '';
?>
<?php
$chosen  = WC()->session ? WC()->session->get( 'chosen_payment_method' ) : '';
$current = isset( $gateways[ $chosen ] ) ? $chosen : $first;
$multi   = count( $gateways ) > 1;
?>
<div id="payment" class="woocommerce-checkout-payment l-payment">
	<?php if ( $gateways ) : ?>
		<ul class="wc_payment_methods payment_methods methods l-payment__methods<?php echo $multi ? ' l-payment__methods--multi' : ''; ?>">
			<?php foreach ( $gateways as $id => $gateway ) : ?>
				<li class="wc_payment_method payment_method_<?php echo esc_attr( $id ); ?>">
					<?php if ( $multi ) : ?><label class="l-cod"><?php endif; ?>
					<input id="payment_method_<?php echo esc_attr( $id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $current ); ?>>
					<?php if ( $multi ) : ?>
						<div class="l-cod__text">
							<?php if ( 'cod' === $id ) : ?>
								<b>Plaćanje pouzećem</b><span>Plaćate kuriru prilikom dostave</span>
							<?php else : ?>
								<b><?php echo esc_html( $gateway->get_title() ); ?></b><?php if ( $gateway->get_description() ) : ?><span><?php echo wp_kses_post( $gateway->get_description() ); ?></span><?php endif; ?>
							<?php endif; ?>
						</div>
					</label>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( ! $multi ) : ?>
		<div class="l-cod">
			<?php echo larita_icon( 'cash', 24 ); ?>
			<div class="l-cod__text">
				<?php if ( 'cod' === $first ) : ?>
					<b>Plaćanje pouzećem</b><span>Plaćate kuriru prilikom dostave</span><span>Post Express 1–3 radnih dana</span>
				<?php else : ?>
					<b><?php echo esc_html( $gateways[ $first ]->get_title() ); ?></b>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
	<?php else : ?>
		<p class="l-payment__none">Za dostavu van Srbije plaćanje je moguće samo karticom, a ono trenutno nije dostupno. Pišite nam na email prodavnice.</p>
	<?php endif; ?>

	<div class="form-row place-order l-place-order">
		<noscript><button type="submit" class="button" name="woocommerce_checkout_update_totals" value="1">Ažuriraj</button></noscript>
		<?php if ( wc_terms_and_conditions_checkbox_enabled() ) : ?>
			<input type="hidden" name="terms" value="on"><input type="hidden" name="terms-field" value="1">
		<?php endif; ?>
		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
		<button type="submit" class="l-btn l-btn--primary l-place-order__btn" name="woocommerce_checkout_place_order" id="place_order" value="Završi kupovinu">ZAVRŠI KUPOVINU</button>
		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
		<p class="l-terms">Klikom na dugme potvrđujem saglasnost sa <a href="<?php echo esc_url( larita_page_url( 'uslovi-koriscenja' ) ); ?>">Uslovima korišćenja</a> i <a href="<?php echo esc_url( larita_page_url( 'politika-privatnosti' ) ); ?>">Politikom privatnosti</a>.</p>
	</div>
</div>
