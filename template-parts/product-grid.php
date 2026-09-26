<?php
/**
 * "Larita majice" section: heading + product cards with colour swatches.
 * Used by the front page and the shop archive.
 */
$products = wc_get_products( array(
	'status'  => 'publish',
	'limit'   => -1,
	'orderby' => 'menu_order',
	'order'   => 'ASC',
) );
?>
<section class="l-products" id="proizvodi">
	<div class="l-products__head">
		<h2 class="l-products__title">Larita majice</h2>
		<p class="l-products__sub">Požurite, količine su ograničene.</p>
	</div>
	<div class="l-products__grid">
		<?php foreach ( $products as $product ) :
			$data   = larita_product_data( $product );
			$url    = $product->get_permalink();
			$first  = $data['colors'][0] ?? null;
			$image  = $first && $first['image'] ? $first['image'] : ( $data['gallery'][0]['large'] ?? wc_placeholder_img_src( 'large' ) );
			$link   = $first ? add_query_arg( 'boja', $first['slug'], $url ) : $url;
			$price  = $product->is_type( 'variable' ) ? $product->get_variation_price( 'min', true ) : $product->get_price();
			?>
			<div class="l-card" data-l-card>
				<a class="l-card__media" href="<?php echo esc_url( $link ); ?>" data-l-card-link tabindex="-1">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" data-l-card-img loading="lazy">
				</a>
				<div class="l-card__info">
					<div class="l-card__titles">
						<a class="l-card__name" href="<?php echo esc_url( $link ); ?>" data-l-card-link><?php echo esc_html( $product->get_name() ); ?></a>
						<div class="l-card__price"><?php echo esc_html( larita_price( $price ) ); ?></div>
					</div>
					<?php if ( $data['colors'] ) : ?>
						<div class="l-card__colors">
							<div class="l-card__color-label">BOJA: <span data-l-card-color><?php echo esc_html( $first['label'] ); ?></span></div>
							<div class="l-card__swatches">
								<?php foreach ( $data['colors'] as $i => $color ) : ?>
									<button type="button" class="l-swatch<?php echo 0 === $i ? ' is-selected' : ''; ?>" aria-label="<?php echo esc_attr( $color['label'] ); ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"
										data-l-swatch data-label="<?php echo esc_attr( $color['label'] ); ?>" data-image="<?php echo esc_url( $color['image'] ); ?>" data-href="<?php echo esc_url( add_query_arg( 'boja', $color['slug'], $url ) ); ?>">
										<span style="background:<?php echo esc_attr( $color['hex'] ); ?>"></span>
									</button>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<a class="l-card__cta" href="<?php echo esc_url( $link ); ?>" data-l-card-link>IZABERI VELIČINU <?php echo larita_icon( 'arrow', 18 ); ?></a>
			</div>
		<?php endforeach; ?>
	</div>
</section>
