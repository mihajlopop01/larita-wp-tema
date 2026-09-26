<?php
/**
 * Site footer.
 *
 * $args['variant']:
 *   full – pink footer with logo, link columns and avatar (home, product, pages)
 *   bar  – single copyright/links bar (cart)
 *   none – no footer (checkout, thank-you)
 */
$variant = $args['variant'] ?? 'full';

$shop_links = array();
foreach ( wc_get_products( array( 'status' => 'publish', 'limit' => 6, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $p ) {
	$shop_links[] = array( $p->get_name(), $p->get_permalink() );
}
$shop_links[] = array( 'Vodič za veličine', larita_page_url( 'vodic-za-velicine' ) );
$shop_links[] = array( 'Zamena veličina', larita_page_url( 'zamena-velicina' ) );

$help_links = array(
	array( 'Politika privatnosti', larita_page_url( 'politika-privatnosti' ) ),
	array( 'Uslovi korišćenja', larita_page_url( 'uslovi-koriscenja' ) ),
	array( 'Odustanak od kupovine', larita_page_url( 'odustanak-od-kupovine' ) ),
);
$contact = array( 'Kontakt', larita_page_url( 'kontakt' ) );
$avatar  = larita_asset( 'images/avatar.png' );

$links = function ( $items ) {
	foreach ( $items as [ $label, $url ] ) {
		echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
};
?>

<?php if ( 'full' === $variant ) : ?>
	<footer class="l-footer">
		<img class="l-footer__avatar" src="<?php echo esc_url( $avatar ); ?>" alt="">
		<div class="l-footer__grid">
			<div class="l-footer__brand">
				<?php echo larita_logo(); ?>
				<p>Zvanična prodavnica Larita majica.</p>
			</div>
			<div class="l-footer__col">
				<div class="l-footer__head">PRODAVNICA</div>
				<?php $links( $shop_links ); ?>
			</div>
			<div class="l-footer__col">
				<div class="l-footer__head">POMOĆ</div>
				<?php $links( $help_links ); ?>
				<a class="l-footer__mobile-only" href="<?php echo esc_url( $contact[1] ); ?>"><?php echo esc_html( $contact[0] ); ?></a>
			</div>
			<div class="l-footer__col l-footer__col--contact">
				<div class="l-footer__head">KONTAKT</div>
				<?php $links( array( $contact ) ); ?>
			</div>
		</div>
		<div class="l-footer__bottom">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Larita Shop</span>
			<img class="l-footer__avatar-m" src="<?php echo esc_url( $avatar ); ?>" alt="">
		</div>
	</footer>
<?php elseif ( 'bar' === $variant ) : ?>
	<footer class="l-footbar">
		<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Larita Shop</span>
		<div class="l-footbar__links"><?php $links( array( $help_links[0], $help_links[1], $contact ) ); ?></div>
	</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
