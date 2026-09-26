<?php
/**
 * Checkout (mockup screen 06).
 *
 * Left: "Podaci za dostavu" card with the billing fields (see
 * larita_checkout_field_spec()). Right: #order_review = summary card
 * (review-order.php) + COD box and submit button (payment.php). On mobile the
 * summary moves to the top via CSS order.
 *
 * @var WC_Checkout $checkout
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>
<form name="checkout" method="post" class="checkout woocommerce-checkout l-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="Plaćanje">

	<div class="l-checkout__details" id="customer_details">
		<h1 class="l-checkout__title">Podaci za dostavu</h1>
		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<div class="woocommerce-billing-fields">
			<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
			<div class="woocommerce-billing-fields__field-wrapper l-fields">
				<?php
				foreach ( $checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
				}
				?>
			</div>
			<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
		</div>
		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
	<div id="order_review" class="woocommerce-checkout-review-order l-checkout__side">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>
	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>
<?php
do_action( 'woocommerce_after_checkout_form', $checkout );
