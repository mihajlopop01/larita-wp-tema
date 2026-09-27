<?php
/**
 * Larita theme functions.
 *
 * Classic PHP templates render markup that mirrors the Claude Design mockup
 * (design/ui-dizajn-za-larita-shop/project/Larita Shop.dc.html); WooCommerce
 * supplies all product, cart and order data.
 */

defined( 'ABSPATH' ) || exit;

define( 'LARITA_VERSION', '2.0.0' );

/* ---------------------------------------------------------------------------
 * Setup & assets
 * ------------------------------------------------------------------------- */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'woocommerce' );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'larita-fonts', 'https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'larita', larita_asset( 'css/larita.css' ), array(), larita_asset_ver( 'css/larita.css' ) );
	wp_enqueue_script( 'larita', larita_asset( 'js/larita.js' ), array(), larita_asset_ver( 'js/larita.js' ), array( 'in_footer' => true ) );
	wp_localize_script( 'larita', 'LARITA', array(
		'addToCartUrl' => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( 'add_to_cart' ) : '',
	) );

	// Block styles are not used by these templates.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wc-blocks-style' );
}, 20 );

// WooCommerce's own stylesheets fight the design; the theme styles everything.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action( 'wp_head', function () {
	echo '<link rel="icon" href="' . esc_url( larita_asset( 'images/favicon.png' ) ) . '">' . "\n";
}, 2 );

function larita_asset( $path ) {
	return get_theme_file_uri( 'assets/' . $path );
}

function larita_asset_ver( $path ) {
	$file = get_theme_file_path( 'assets/' . $path );
	return file_exists( $file ) ? (string) filemtime( $file ) : LARITA_VERSION;
}

/* ---------------------------------------------------------------------------
 * Prices: the design shows "1.699 RSD" (dot thousands, no decimals, RSD after).
 * ------------------------------------------------------------------------- */

add_filter( 'wc_get_price_thousand_separator', fn() => '.' );
add_filter( 'wc_get_price_decimal_separator', fn() => ',' );
add_filter( 'wc_get_price_decimals', fn() => 0 );
add_filter( 'woocommerce_price_format', fn() => '%2$s&nbsp;%1$s' );
add_filter( 'woocommerce_currency_symbol', fn( $symbol, $currency ) => 'RSD' === $currency ? 'RSD' : $symbol, 10, 2 );

/** Plain-text price ("1.699 RSD") for places where wc_price() markup is unwanted. */
function larita_price( $amount ) {
	return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
}

/* ---------------------------------------------------------------------------
 * Small markup helpers
 * ------------------------------------------------------------------------- */

/** Inline SVG icons copied from the mockup. */
function larita_icon( $name, $size = 20, $attrs = '' ) {
	$paths = array(
		'cart'    => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
		'arrow'   => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'back'    => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
		'menu'    => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'ruler'   => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/>',
		'heart'   => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
		'return'  => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
		'truck'   => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
		'check'   => '<path d="M20 6 9 17l-5-5"/>',
		'trash'   => '<path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>',
		'cash'    => '<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
	);
	$stroke = array( 'check' => 3, 'heart' => 2, 'return' => 2, 'truck' => 2, 'trash' => 2, 'cash' => 2, 'ruler' => 2, 'arrow' => 2.4 );
	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%2$s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" %3$s>%4$s</svg>',
		$size,
		$stroke[ $name ] ?? 2.2,
		$attrs,
		$paths[ $name ]
	);
}

/** Double chevron under the hero. */
function larita_chevrons( $w, $h, $overlap ) {
	$one = '<svg width="' . $w . '" height="' . $h . '" viewBox="0 0 64 34" fill="none" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"%s><path d="M6 6l26 22L58 6"/></svg>';
	return sprintf( $one, '' ) . sprintf( $one, ' style="margin-top:-' . $overlap . 'px"' );
}

/**
 * The logo PNG has transparent padding; the mockup crops it with a fixed box
 * and a negative top margin (height ≈ .31 × width, offset ≈ .15 × width).
 */
function larita_logo( $class = '' ) {
	return '<span class="l-logo ' . esc_attr( $class ) . '"><img src="' . esc_url( larita_asset( 'images/logo.png' ) ) . '" alt="Larita"></span>';
}

function larita_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function larita_products_url() {
	return home_url( '/#proizvodi' );
}

/** Main navigation, as in the mockup. */
function larita_nav_items() {
	return array(
		'home'   => array( 'Početna', home_url( '/' ) ),
		'shop'   => array( 'Majice', larita_products_url() ),
		'guide'  => array( 'Vodič za veličine', larita_page_url( 'vodic-za-velicine' ) ),
		'contact'=> array( 'Kontakt', larita_page_url( 'kontakt' ) ),
	);
}

function larita_current_nav() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( function_exists( 'is_woocommerce' ) && ( is_product() || is_shop() || is_product_taxonomy() ) ) {
		return 'shop';
	}
	if ( is_page( 'vodic-za-velicine' ) ) {
		return 'guide';
	}
	if ( is_page( 'kontakt' ) ) {
		return 'contact';
	}
	return '';
}

function larita_cart_count() {
	return function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/* ---------------------------------------------------------------------------
 * Product data for the custom colour / size pickers
 * ------------------------------------------------------------------------- */

/** Swatch colours for the "Boja" attribute values; order = display order. */
function larita_color_hex() {
	return array(
		'roze' => '#F27BC9',
		'bela' => '#FFFFFF',
	);
}

/**
 * Normalised data for a variable product:
 * colors (label, slug, hex, image), sizes, variations, gallery.
 */
function larita_product_data( $product ) {
	$data = array(
		'id'         => $product->get_id(),
		'colorKey'   => '',
		'sizeKey'    => '',
		'colors'     => array(),
		'sizes'      => array(),
		'variations' => array(),
		'gallery'    => array(),
	);

	$image_ids = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );
	foreach ( array_unique( $image_ids ) as $id ) {
		$data['gallery'][] = array(
			'id'    => (int) $id,
			'large' => wp_get_attachment_image_url( $id, 'large' ),
			'thumb' => wp_get_attachment_image_url( $id, 'woocommerce_thumbnail' ),
		);
	}

	if ( ! $product->is_type( 'variable' ) ) {
		return $data;
	}

	// get_variation_attributes() keys a taxonomy attribute ("Boja") by its
	// taxonomy slug (pa_boja) and a custom attribute by its sanitized label
	// (boja); values are term slugs for the former, raw option text for the
	// latter. Match either shape so both attribute types work the same way.
	$attributes  = $product->get_variation_attributes();
	$product_attrs = $product->get_attributes();
	$colors = array();
	$sizes  = array();
	foreach ( $attributes as $raw_name => $values ) {
		$attr    = $product_attrs[ $raw_name ] ?? null;
		$is_tax  = $attr && $attr->is_taxonomy();
		$base    = $is_tax ? str_replace( 'pa_', '', $raw_name ) : sanitize_title( $raw_name );
		$options = array();
		foreach ( $values as $value ) {
			// $value is a term slug for taxonomy attributes, the option text otherwise.
			$label = $is_tax ? ( get_term_by( 'slug', $value, $raw_name )->name ?? $value ) : $value;
			$options[ $value ] = $label; // slug/value => display label
		}
		if ( 'boja' === $base ) {
			$data['colorKey'] = 'attribute_' . $raw_name;
			$colors           = $options;
		} elseif ( ! $data['sizeKey'] ) {
			$data['sizeKey'] = 'attribute_' . $raw_name;
			$sizes           = $options;
		}
	}

	foreach ( $product->get_available_variations( 'objects' ) as $variation ) {
		$attrs    = $variation->get_attributes(); // keyed by taxonomy/base name, values = slug or option text
		$image_id = $variation->get_image_id();
		$data['variations'][] = array(
			'id'      => $variation->get_id(),
			'color'   => $data['colorKey'] ? ( $attrs[ substr( $data['colorKey'], 10 ) ] ?? '' ) : '',
			'size'    => $data['sizeKey'] ? ( $attrs[ substr( $data['sizeKey'], 10 ) ] ?? '' ) : '',
			'inStock' => $variation->is_in_stock(),
			'imageId' => (int) $image_id,
			'image'   => $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '',
			'thumb'   => $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '',
		);
	}

	$hex   = larita_color_hex();
	$order = array_keys( $hex );
	if ( ! empty( $colors ) ) {
		uksort( $colors, function ( $a, $b ) use ( $order ) {
			$ia = array_search( sanitize_title( $a ), $order, true );
			$ib = array_search( sanitize_title( $b ), $order, true );
			return ( false === $ia ? 99 : $ia ) <=> ( false === $ib ? 99 : $ib );
		} );
		foreach ( $colors as $value => $label ) {
			$match = current( array_filter( $data['variations'], fn( $v ) => $v['color'] === $value && $v['image'] ) );
			$data['colors'][] = array(
				'value'   => $value, // what the hidden form input / JS state must send back
				'label'   => $label, // what's shown to the customer
				'slug'    => sanitize_title( $label ),
				'hex'     => $hex[ sanitize_title( $label ) ] ?? '#DDDDDD',
				'image'   => $match ? $match['image'] : '',
				'imageId' => $match ? $match['imageId'] : 0,
			);
		}
	}
	if ( ! empty( $sizes ) ) {
		uksort( $sizes, fn( $a, $b ) => strnatcmp( $sizes[ $a ], $sizes[ $b ] ) );
		foreach ( $sizes as $value => $label ) {
			$data['sizes'][] = array( 'value' => $value, 'label' => $label );
		}
	}

	return $data;
}

/** Colour preselected via ?boja=slug (from the home-page swatches), else the first. */
function larita_initial_color( $data ) {
	$wanted = isset( $_GET['boja'] ) ? sanitize_title( wp_unslash( $_GET['boja'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	foreach ( $data['colors'] as $i => $color ) {
		if ( $color['slug'] === $wanted ) {
			return $i;
		}
	}
	return 0;
}

/* ---------------------------------------------------------------------------
 * Cart behaviour
 * ------------------------------------------------------------------------- */

// "Naruči odmah" posts the normal add-to-cart form plus larita_buy_now=1.
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	if ( ! empty( $_REQUEST['larita_buy_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return wc_get_checkout_url();
	}
	return $url;
} );

// Live cart count + subtotal for the header badges and the add-to-cart drawer.
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$count = larita_cart_count();
	$fragments['.l-cart-count'] = '<span class="l-cart-count">' . $count . '</span>';
	$fragments['.l-drawer-count'] = '<span class="l-drawer-count">' . $count . '</span>';
	$fragments['.l-drawer-total'] = '<span class="l-drawer-total">' . esc_html( larita_price( WC()->cart->get_subtotal() ) ) . '</span>';
	return $fragments;
} );

// Only products and variation text in cart rows: "Roze · Veličina 10".
function larita_cart_item_variant( $cart_item ) {
	$parts = array();
	foreach ( (array) ( $cart_item['variation'] ?? array() ) as $key => $value ) {
		$taxonomy = str_replace( 'attribute_', '', $key );
		$name     = wc_attribute_label( $taxonomy, $cart_item['data'] );
		// $value is a term slug for a taxonomy attribute (pa_boja), the raw
		// option text otherwise; resolve the slug to its display name.
		$label = taxonomy_exists( $taxonomy ) ? ( get_term_by( 'slug', $value, $taxonomy )->name ?? $value ) : $value;
		if ( 'boja' === sanitize_title( $name ) ) {
			$parts[] = $label;
		} else {
			$parts[] = 'Veličina ' . $label;
		}
	}
	return implode( ' · ', $parts );
}

/* ---------------------------------------------------------------------------
 * Checkout fields — the mockup's eight fields, in its order and wording.
 * ------------------------------------------------------------------------- */

function larita_checkout_field_spec() {
	return array(
		'email'      => array( 'Email', 'ime@email.com', 10, 'form-row-wide' ),
		'first_name' => array( 'Ime', 'Ime', 20, 'form-row-first' ),
		'last_name'  => array( 'Prezime', 'Prezime', 30, 'form-row-last' ),
		'country'    => array( 'Država', '', 40, 'form-row-wide' ),
		'address_1'  => array( 'Adresa', 'Ulica i broj', 50, 'form-row-wide' ),
		'city'       => array( 'Grad', 'Grad', 60, 'form-row-first' ),
		'postcode'   => array( 'Poštanski broj', '11000', 70, 'form-row-last' ),
		'phone'      => array( 'Broj telefona', '+381 6x xxx xxxx', 80, 'form-row-wide' ),
	);
}

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	$billing = array();
	foreach ( larita_checkout_field_spec() as $key => [ $label, $placeholder, $priority, $class ] ) {
		$field = $fields['billing'][ 'billing_' . $key ] ?? array();
		$field['label']       = $label;
		$field['placeholder'] = $placeholder;
		$field['priority']    = $priority;
		$field['required']    = true;
		$field['class']       = array( $class );
		$billing[ 'billing_' . $key ] = $field;
	}
	$fields['billing'] = $billing;
	unset( $fields['order'] );
	return $fields;
}, 20 );

// address-i18n.js re-applies locale labels/placeholders/priorities on load; keep ours.
add_filter( 'woocommerce_get_country_locale_default', function ( $locale ) {
	foreach ( larita_checkout_field_spec() as $key => [ $label, $placeholder, $priority ] ) {
		if ( isset( $locale[ $key ] ) ) {
			$locale[ $key ]['label']       = $label;
			$locale[ $key ]['placeholder'] = $placeholder;
			$locale[ $key ]['priority']    = $priority;
		}
	}
	return $locale;
} );

add_filter( 'woocommerce_get_country_locale', function ( $locales ) {
	foreach ( $locales as $country => $fields ) {
		foreach ( array_keys( larita_checkout_field_spec() ) as $key ) {
			unset( $locales[ $country ][ $key ]['label'], $locales[ $country ][ $key ]['placeholder'], $locales[ $country ][ $key ]['priority'] );
		}
	}
	return $locales;
} );

// Not in the design: coupon/login toggles above checkout, order-details table on thank-you.
add_action( 'wp', function () {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
	remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
} );

add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

// The site language is English: translate the WooCommerce messages customers can see.
add_filter( 'gettext_woocommerce', function ( $translation, $text ) {
	$map = array(
		'%s is a required field.'           => '%s je obavezno polje.',
		'%s is not a valid email address.'  => '%s nije ispravna email adresa.',
		'%s is not a valid phone number.'   => '%s nije ispravan broj telefona.',
		'%s is not a valid postcode / ZIP.' => '%s nije ispravan poštanski broj.',
		'Cart updated.'                     => 'Korpa je ažurirana.',
		'%s removed.'                       => '%s je uklonjen iz korpe.',
		'Undo?'                             => 'Vrati?',
		'View cart'                         => 'Pogledaj korpu',
	);
	return $map[ $text ] ?? $translation;
}, 10, 2 );

add_filter( 'gettext_with_context_woocommerce', function ( $translation, $text, $context ) {
	return ( 'Billing %s' === $text && 'checkout-validation' === $context ) ? '%s' : $translation;
}, 10, 3 );

add_filter( 'ngettext_woocommerce', function ( $translation, $single, $plural, $number ) {
	if ( '%s has been added to your cart.' === $single ) {
		return 1 === (int) $number ? '%s je dodat u korpu.' : '%s su dodati u korpu.';
	}
	return $translation;
}, 10, 4 );

// The site language is English; the design shows the country in Serbian.
add_filter( 'woocommerce_countries', function ( $countries ) {
	$countries['RS'] = 'Srbija';
	return $countries;
} );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

/* ---------------------------------------------------------------------------
 * Cart & checkout pages render the classic templates (theme/woocommerce/…)
 * regardless of the page content, so the live pages don't need editing.
 * ------------------------------------------------------------------------- */

function larita_render_cart() {
	echo do_shortcode( '[woocommerce_cart]' );
}

function larita_render_checkout() {
	echo do_shortcode( '[woocommerce_checkout]' );
}

/* ---------------------------------------------------------------------------
 * Custom order numbers: L1082, L1083, ... assigned once per checkout order and
 * stored on the order, so the sequence has no gaps even though internal IDs
 * skip (checkout drafts consume IDs). The internal ID is untouched.
 * Orders created outside checkout (wp-admin, REST) keep their plain ID.
 * ------------------------------------------------------------------------- */

define( 'LARITA_ORDER_NUMBER_START', 1082 );

add_action( 'woocommerce_checkout_order_created', function ( $order ) {
	if ( $order->get_meta( '_larita_order_number' ) ) {
		return;
	}
	$next = max( LARITA_ORDER_NUMBER_START, (int) get_option( 'larita_next_order_number', 0 ) );
	update_option( 'larita_next_order_number', $next + 1, false );
	$order->update_meta_data( '_larita_order_number', 'L' . $next );
	$order->save_meta_data();
} );

add_filter( 'woocommerce_order_number', function ( $order_number, $order ) {
	return $order->get_meta( '_larita_order_number' ) ?: $order_number;
}, 10, 2 );

// Google Apps Script answers every POST with a 302; without following it,
// WooCommerce logs a failed delivery and disables the webhook after 5.
add_filter( 'woocommerce_webhook_http_args', function ( $args ) {
	$args['redirection'] = 5;
	return $args;
} );

