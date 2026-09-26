<?php
/**
 * Product page (mockup screen 02), size-guide popup (03) and the
 * "Dodato u korpu" drawer (04).
 *
 * Colour/size buttons fill a normal WooCommerce add-to-cart form
 * (add-to-cart + variation_id + attribute_* + quantity), so it also works
 * without JavaScript; larita.js adds the AJAX add + drawer on top.
 */
defined( 'ABSPATH' ) || exit;

get_header( null, array( 'variant' => 'default' ) );

while ( have_posts() ) :
	the_post();
	global $product;
	$product = wc_get_product( get_the_ID() );
	$data    = larita_product_data( $product );
	$ci      = larita_initial_color( $data );
	$color   = $data['colors'][ $ci ] ?? null;
	$price   = $product->is_type( 'variable' ) ? $product->get_variation_price( 'min', true ) : $product->get_price();
	$price_s = larita_price( $price );

	// Main image: the selected colour's variation image, else the first gallery image.
	$main_id  = $color && $color['imageId'] ? $color['imageId'] : ( $data['gallery'][0]['id'] ?? 0 );
	$main_src = $color && $color['image'] ? $color['image'] : ( $data['gallery'][0]['large'] ?? wc_placeholder_img_src( 'large' ) );
	?>
	<main class="l-product">
		<div class="l-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Početna</a><span>›</span><span class="is-current"><?php the_title(); ?></span></div>

		<?php woocommerce_output_all_notices(); ?>

		<div class="l-product__grid">
			<div class="l-gallery">
				<div class="l-gallery__main"><img src="<?php echo esc_url( $main_src ); ?>" alt="<?php the_title_attribute(); ?>" data-l-main-img></div>
				<?php if ( count( $data['gallery'] ) > 1 ) : ?>
					<div class="l-gallery__thumbs">
						<?php foreach ( $data['gallery'] as $img ) : ?>
							<button type="button" class="l-thumb<?php echo $img['id'] === $main_id ? ' is-active' : ''; ?>" data-l-thumb data-id="<?php echo (int) $img['id']; ?>" data-large="<?php echo esc_url( $img['large'] ); ?>" aria-label="Slika proizvoda">
								<img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="" loading="lazy">
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<form class="l-buy" method="post" action="<?php echo esc_url( $product->get_permalink() ); ?>" data-l-buy
				data-variations="<?php echo esc_attr( wp_json_encode( $data['variations'] ) ); ?>"
				data-name="<?php echo esc_attr( $product->get_name() ); ?>"
				data-price="<?php echo esc_attr( $price_s ); ?>"
				data-unit="<?php echo esc_attr( (float) $price ); ?>">
				<div class="l-buy__title">
					<h1><?php the_title(); ?></h1>
					<div class="l-buy__price"><?php echo esc_html( $price_s ); ?></div>
				</div>
				<div class="l-rule"></div>

				<?php if ( $data['colors'] ) : ?>
					<div class="l-buy__group">
						<div class="l-label">BOJA: <span data-l-color-label><?php echo esc_html( $color['label'] ); ?></span></div>
						<div class="l-colors">
							<?php foreach ( $data['colors'] as $i => $c ) : ?>
								<button type="button" class="l-color<?php echo $i === $ci ? ' is-selected' : ''; ?>" aria-pressed="<?php echo $i === $ci ? 'true' : 'false'; ?>"
									data-l-color data-value="<?php echo esc_attr( $c['value'] ); ?>" data-label="<?php echo esc_attr( $c['label'] ); ?>" data-image="<?php echo esc_url( $c['image'] ); ?>" data-image-id="<?php echo (int) $c['imageId']; ?>">
									<span class="l-color__dot" style="background:<?php echo esc_attr( $c['hex'] ); ?>"></span><?php echo esc_html( $c['label'] ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $data['sizes'] ) : ?>
					<div class="l-buy__group">
						<div class="l-buy__size-head">
							<div class="l-label">VELIČINA: <span data-l-size-label>—</span></div>
							<button type="button" class="l-guide-link" data-l-guide-open><?php echo larita_icon( 'ruler', 16 ); ?>VODIČ ZA VELIČINE</button>
						</div>
						<div class="l-sizes" data-l-sizes>
							<?php foreach ( $data['sizes'] as $s ) : ?>
								<button type="button" class="l-size" aria-pressed="false" data-l-size data-value="<?php echo esc_attr( $s['value'] ); ?>" data-label="<?php echo esc_attr( $s['label'] ); ?>"><?php echo esc_html( $s['label'] ); ?></button>
							<?php endforeach; ?>
						</div>
						<div class="l-buy__error" data-l-error hidden>Izaberi veličinu.</div>
					</div>
				<?php endif; ?>
				<div class="l-rule"></div>

				<div class="l-buy__group">
					<div class="l-label l-label--qty">KOLIČINA</div>
					<div class="l-buy__row">
						<div class="l-qty">
							<button type="button" aria-label="Manje" data-l-qty="-1">−</button>
							<span data-l-qty-value>1</span>
							<button type="button" aria-label="Više" data-l-qty="1">+</button>
						</div>
						<button type="submit" class="l-btn l-btn--primary l-buy__add" data-l-add>DODAJ U KORPU <?php echo larita_icon( 'cart', 20 ); ?></button>
						<button type="submit" class="l-btn l-btn--outline l-buy__now l-buy__now--m" name="larita_buy_now" value="1">NARUČI ODMAH →</button>
					</div>
					<button type="submit" class="l-btn l-btn--outline l-buy__now l-buy__now--d" name="larita_buy_now" value="1">NARUČI ODMAH →</button>
				</div>

				<div class="l-trust">
					<div><?php echo larita_icon( 'heart', 20 ); ?>100% pamuk</div>
					<span class="l-trust__sep"></span>
					<div><?php echo larita_icon( 'return', 20 ); ?>Povrat 14 dana</div>
					<span class="l-trust__sep"></span>
					<div><?php echo larita_icon( 'truck', 20 ); ?>Brza isporuka</div>
				</div>

				<input type="hidden" name="add-to-cart" value="<?php echo (int) $product->get_id(); ?>">
				<input type="hidden" name="product_id" value="<?php echo (int) $product->get_id(); ?>">
				<input type="hidden" name="variation_id" value="" data-l-variation>
				<input type="hidden" name="quantity" value="1" data-l-qty-input>
				<?php if ( $data['colorKey'] ) : ?>
					<input type="hidden" name="<?php echo esc_attr( $data['colorKey'] ); ?>" value="<?php echo esc_attr( $color['value'] ?? '' ); ?>" data-l-color-input>
				<?php endif; ?>
				<?php if ( $data['sizeKey'] ) : ?>
					<input type="hidden" name="<?php echo esc_attr( $data['sizeKey'] ); ?>" value="" data-l-size-input>
				<?php endif; ?>

				<div class="l-sticky-buy">
					<div class="l-sticky-buy__info">
						<span class="l-sticky-buy__price"><?php echo esc_html( $price_s ); ?></span>
						<span class="l-sticky-buy__variant"><span data-l-color-label><?php echo esc_html( $color['label'] ?? '' ); ?></span> · <span data-l-size-label>—</span></span>
					</div>
					<button type="submit" class="l-btn l-btn--primary" data-l-add>DODAJ U KORPU</button>
				</div>
			</form>
		</div>
	</main>

	<?php /* Size guide: centred modal on desktop, bottom sheet on mobile. */ ?>
	<div class="l-overlay l-guide" hidden data-l-guide>
		<div class="l-guide__box" role="dialog" aria-modal="true" aria-labelledby="l-guide-title">
			<div class="l-sheet-handle"></div>
			<div class="l-dialog-head">
				<div class="l-dialog-title" id="l-guide-title">Vodič za veličine</div>
				<button type="button" class="l-close" aria-label="Zatvori" data-l-close>✕</button>
			</div>
			<img src="<?php echo esc_url( larita_asset( 'images/vodic.png' ) ); ?>" alt="Tabela veličina">
		</div>
	</div>

	<?php /* "Dodato u korpu": right drawer on desktop, bottom sheet on mobile. */ ?>
	<div class="l-overlay l-drawer" hidden data-l-drawer>
		<div class="l-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="l-drawer-title">
			<div class="l-sheet-handle"></div>
			<div class="l-drawer__head">
				<div class="l-drawer__title" id="l-drawer-title"><span class="l-drawer__check"><?php echo larita_icon( 'check', 18 ); ?></span>Dodato u korpu</div>
				<button type="button" class="l-close" aria-label="Zatvori" data-l-close>✕</button>
			</div>
			<div class="l-drawer__item">
				<div class="l-thumbframe"><img src="" alt="" data-l-drawer-img></div>
				<div class="l-drawer__meta">
					<div class="l-drawer__name" data-l-drawer-name></div>
					<div class="l-drawer__variant" data-l-drawer-variant></div>
					<div class="l-drawer__price" data-l-drawer-price></div>
				</div>
			</div>
			<div class="l-drawer__spacer"></div>
			<div class="l-drawer__foot">
				<div class="l-drawer__total"><span class="l-drawer__total-label">UKUPNO<span class="l-desktop-only"> U KORPI</span> (<span class="l-drawer-count"><?php echo (int) larita_cart_count(); ?></span>)</span><span class="l-drawer-total"></span></div>
				<a class="l-btn l-btn--primary" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">NARUČI →</a>
				<a class="l-btn l-btn--outline l-btn--flat" href="<?php echo esc_url( wc_get_cart_url() ); ?>">POGLEDAJ KORPU</a>
				<a class="l-drawer__continue" href="<?php echo esc_url( larita_products_url() ); ?>">Nastavi kupovinu</a>
			</div>
		</div>
	</div>
	<?php
endwhile;

get_footer( null, array( 'variant' => 'full' ) );
