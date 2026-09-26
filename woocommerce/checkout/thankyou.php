<?php
/**
 * Thank-you screen (mockup screen 07).
 *
 * @var WC_Order|false $order
 */
defined( 'ABSPATH' ) || exit;

$failed = $order && $order->has_status( 'failed' );
?>
<section class="l-thanks" style="background-image:url(<?php echo esc_url( larita_asset( 'images/banner.png' ) ); ?>)">
	<div class="l-thanks__veil"></div>
	<a class="l-thanks__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo larita_logo(); ?></a>
	<div class="l-thanks__card">
		<img class="l-thanks__avatar" src="<?php echo esc_url( larita_asset( 'images/avatar.png' ) ); ?>" alt="">
		<?php if ( $order && ! $failed ) : ?>
			<h1 class="l-thanks__title">Hvala na porudžbini!</h1>
			<p class="l-thanks__text">Porudžbina <b>#<?php echo esc_html( $order->get_order_number() ); ?></b> je primljena. Potvrdu smo poslali na tvoj email. Plaćaš kuriru prilikom dostave, Post Express 1–3 radna dana.</p>
			<div class="l-thanks__total"><span><span class="l-desktop-only">Ukupno za plaćanje</span><span class="l-mobile-only">Ukupno</span></span><span class="l-thanks__total-value"><?php echo esc_html( larita_price( $order->get_total() ) ); ?></span></div>
		<?php elseif ( $failed ) : ?>
			<h1 class="l-thanks__title">Porudžbina nije uspela</h1>
			<p class="l-thanks__text">Nažalost, porudžbina nije mogla da bude obrađena. Pokušaj ponovo.</p>
		<?php else : ?>
			<h1 class="l-thanks__title">Hvala!</h1>
			<p class="l-thanks__text">Porudžbina je primljena.</p>
		<?php endif; ?>
		<a class="l-btn l-btn--primary l-thanks__btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">NAZAD NA POČETNU</a>
	</div>
</section>
<?php
if ( $order ) {
	// Lets tracking/analytics integrations run; the default order-details table is unhooked in functions.php.
	do_action( 'woocommerce_thankyou', $order->get_id() );
}
