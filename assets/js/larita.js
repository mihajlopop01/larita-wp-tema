/**
 * Larita Shop interactions (mirrors the mockup's DCLogic state machine):
 * mobile menu, home-card colour swatches, product colour/size/qty/gallery,
 * size-guide popup, AJAX add-to-cart + "Dodato u korpu" drawer, cart +/-.
 */
( function () {
	'use strict';

	const $ = ( sel, root = document ) => root.querySelector( sel );
	const $$ = ( sel, root = document ) => Array.from( root.querySelectorAll( sel ) );

	/* ---------- Overlays (size guide, drawer) ---------- */
	let lastFocus = null;
	function openOverlay( el ) {
		lastFocus = document.activeElement;
		el.hidden = false;
		document.body.classList.add( 'l-locked' );
		const close = $( '[data-l-close]', el );
		if ( close ) close.focus();
	}
	function closeOverlay( el ) {
		el.hidden = true;
		document.body.classList.remove( 'l-locked' );
		if ( lastFocus ) lastFocus.focus();
	}
	$$( '.l-overlay' ).forEach( ( el ) => {
		el.addEventListener( 'click', ( e ) => {
			if ( e.target === el || e.target.closest( 'button[data-l-close]' ) ) closeOverlay( el );
		} );
	} );
	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key !== 'Escape' ) return;
		$$( '.l-overlay' ).filter( ( el ) => ! el.hidden ).forEach( closeOverlay );
		closeMenu();
	} );

	/* ---------- Mobile menu ---------- */
	const menu = $( '[data-l-menu]' );
	function closeMenu() {
		if ( ! menu ) return;
		menu.hidden = true;
		$$( '[data-l-menu-toggle]' ).forEach( ( b ) => b.setAttribute( 'aria-expanded', 'false' ) );
	}
	$$( '[data-l-menu-toggle]' ).forEach( ( btn ) => {
		btn.addEventListener( 'click', ( e ) => {
			e.stopPropagation();
			if ( ! menu ) return;
			menu.hidden = ! menu.hidden;
			btn.setAttribute( 'aria-expanded', String( ! menu.hidden ) );
		} );
	} );
	document.addEventListener( 'click', ( e ) => {
		if ( menu && ! menu.hidden && ! menu.contains( e.target ) ) closeMenu();
	} );
	if ( menu ) menu.addEventListener( 'click', ( e ) => e.target.closest( 'a' ) && closeMenu() );

	/* ---------- Home / shop cards ---------- */
	$$( '[data-l-card]' ).forEach( ( card ) => {
		const img = $( '[data-l-card-img]', card );
		const label = $( '[data-l-card-color]', card );
		$$( '[data-l-swatch]', card ).forEach( ( sw ) => {
			sw.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				e.stopPropagation();
				$$( '[data-l-swatch]', card ).forEach( ( s ) => {
					s.classList.toggle( 'is-selected', s === sw );
					s.setAttribute( 'aria-pressed', String( s === sw ) );
				} );
				if ( sw.dataset.image ) img.src = sw.dataset.image;
				if ( label ) label.textContent = sw.dataset.label;
				$$( '[data-l-card-link]', card ).forEach( ( a ) => ( a.href = sw.dataset.href ) );
			} );
		} );
		// The whole card is clickable, as in the mockup.
		card.addEventListener( 'click', ( e ) => {
			if ( e.target.closest( 'a, button' ) ) return;
			const link = $( '[data-l-card-link]', card );
			if ( link ) window.location.href = link.href;
		} );
	} );

	/* ---------- Product page ---------- */
	const form = $( '[data-l-buy]' );
	if ( form ) initProduct( form );

	function initProduct( form ) {
		const variations = JSON.parse( form.dataset.variations || '[]' );
		const mainImg = $( '[data-l-main-img]' );
		const thumbs = $$( '[data-l-thumb]' );
		const colorBtns = $$( '[data-l-color]', form );
		const sizeBtns = $$( '[data-l-size]', form );
		const colorInput = $( '[data-l-color-input]', form );
		const sizeInput = $( '[data-l-size-input]', form );
		const variationInput = $( '[data-l-variation]', form );
		const qtyInput = $( '[data-l-qty-input]', form );
		const error = $( '[data-l-error]', form );
		const initialColorBtn = colorBtns.find( ( b ) => b.dataset.value === ( colorInput ? colorInput.value : '' ) );
		const state = {
			color: colorInput ? colorInput.value : '', // slug/value posted to WooCommerce
			colorLabel: initialColorBtn ? initialColorBtn.dataset.label : '', // shown to the customer
			size: '',
			sizeLabel: '',
			qty: 1,
		};

		function setMain( src, id ) {
			if ( src ) mainImg.src = src;
			thumbs.forEach( ( t ) => t.classList.toggle( 'is-active', String( t.dataset.id ) === String( id ) ) );
		}

		function findVariation() {
			return variations.find(
				( v ) => ( ! colorInput || v.color === state.color ) && ( ! sizeInput || v.size === state.size )
			);
		}

		function render() {
			$$( '[data-l-color-label]' ).forEach( ( el ) => ( el.textContent = state.colorLabel ) );
			$$( '[data-l-size-label]' ).forEach( ( el ) => ( el.textContent = state.sizeLabel || '—' ) );
			colorBtns.forEach( ( b ) => {
				const on = b.dataset.value === state.color;
				b.classList.toggle( 'is-selected', on );
				b.setAttribute( 'aria-pressed', String( on ) );
			} );
			sizeBtns.forEach( ( b ) => {
				const on = b.dataset.value === state.size;
				const available = variations.some(
					( v ) => v.size === b.dataset.value && ( ! colorInput || v.color === state.color ) && v.inStock
				);
				b.disabled = ! available;
				b.classList.toggle( 'is-selected', on );
				b.setAttribute( 'aria-pressed', String( on ) );
			} );
			$( '[data-l-qty-value]', form ).textContent = state.qty;
			qtyInput.value = state.qty;
			if ( colorInput ) colorInput.value = state.color;
			if ( sizeInput ) sizeInput.value = state.size;
			const v = findVariation();
			variationInput.value = v && v.inStock ? v.id : '';
		}

		colorBtns.forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.color = b.dataset.value;
				state.colorLabel = b.dataset.label;
				// Keep the size only if it exists in stock for the new colour.
				if ( state.size && ! variations.some( ( v ) => v.color === state.color && v.size === state.size && v.inStock ) ) {
					state.size = '';
					state.sizeLabel = '';
				}
				setMain( b.dataset.image, b.dataset.imageId );
				render();
			} )
		);
		sizeBtns.forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.size = b.dataset.value;
				state.sizeLabel = b.dataset.label;
				if ( error ) error.hidden = true;
				$( '[data-l-sizes]', form ).classList.remove( 'is-error' );
				render();
			} )
		);
		thumbs.forEach( ( t ) => t.addEventListener( 'click', () => setMain( t.dataset.large, t.dataset.id ) ) );
		$$( '[data-l-qty]', form ).forEach( ( b ) =>
			b.addEventListener( 'click', () => {
				state.qty = Math.max( 1, state.qty + Number( b.dataset.lQty ) );
				render();
			} )
		);

		const guide = $( '[data-l-guide]' );
		$$( '[data-l-guide-open]' ).forEach( ( b ) => b.addEventListener( 'click', () => openOverlay( guide ) ) );

		function needsSize() {
			if ( variationInput.value ) return false;
			if ( error ) error.hidden = false;
			const sizes = $( '[data-l-sizes]', form );
			if ( sizes ) {
				sizes.classList.add( 'is-error' );
				sizes.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}
			return true;
		}

		form.addEventListener( 'submit', ( e ) => {
			const submitter = e.submitter;
			if ( needsSize() ) {
				e.preventDefault();
				return;
			}
			// "Naruči odmah" posts normally (server redirects to checkout).
			if ( submitter && submitter.name === 'larita_buy_now' ) return;
			if ( ! window.LARITA || ! LARITA.addToCartUrl || ! window.fetch ) return;
			e.preventDefault();
			addToCart( submitter );
		} );

		function addToCart( button ) {
			const v = findVariation();
			const body = new URLSearchParams( {
				product_id: v ? v.id : form.querySelector( '[name="add-to-cart"]' ).value,
				quantity: state.qty,
			} );
			$$( '[data-l-add]', form ).forEach( ( b ) => ( b.disabled = true ) );
			fetch( LARITA.addToCartUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body,
			} )
				.then( ( r ) => r.json() )
				.then( ( data ) => {
					if ( ! data || data.error ) {
						// Let WooCommerce show its own error (e.g. out of stock) via a normal post.
						HTMLFormElement.prototype.submit.call( form );
						return;
					}
					applyFragments( data.fragments || {} );
					showDrawer( v );
					if ( window.jQuery ) {
						window.jQuery( document.body ).trigger( 'added_to_cart', [ data.fragments, data.cart_hash, window.jQuery( button ) ] );
					}
				} )
				.catch( () => HTMLFormElement.prototype.submit.call( form ) )
				.finally( () => $$( '[data-l-add]', form ).forEach( ( b ) => ( b.disabled = false ) ) );
		}

		function showDrawer( v ) {
			const drawer = $( '[data-l-drawer]' );
			const unit = Number( form.dataset.unit ) || 0;
			const parts = [];
			if ( colorInput ) parts.push( state.colorLabel );
			if ( sizeInput ) parts.push( 'Veličina ' + state.sizeLabel );
			parts.push( state.qty + ' kom' );
			$( '[data-l-drawer-img]', drawer ).src = ( v && v.thumb ) || mainImg.src;
			$( '[data-l-drawer-name]', drawer ).textContent = form.dataset.name;
			$( '[data-l-drawer-variant]', drawer ).textContent = parts.join( ' · ' );
			$( '[data-l-drawer-price]', drawer ).textContent = formatPrice( unit * state.qty );
			openOverlay( drawer );
		}

		render();
	}

	function formatPrice( n ) {
		return Math.round( n ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, '.' ) + ' RSD';
	}

	function applyFragments( fragments ) {
		Object.keys( fragments ).forEach( ( sel ) => {
			let nodes;
			try {
				nodes = $$( sel );
			} catch ( err ) {
				return;
			}
			nodes.forEach( ( node ) => {
				const tpl = document.createElement( 'template' );
				tpl.innerHTML = fragments[ sel ].trim();
				if ( tpl.content.firstElementChild ) node.replaceWith( tpl.content.firstElementChild );
			} );
		} );
	}

	/* ---------- Cart page: +/- trigger WooCommerce's AJAX cart update ---------- */
	document.addEventListener( 'click', ( e ) => {
		const btn = e.target.closest( '[data-l-cart-qty] button[data-step]' );
		if ( ! btn ) return;
		const input = $( 'input.qty', btn.parentElement );
		const next = Math.max( 1, ( parseInt( input.value, 10 ) || 1 ) + Number( btn.dataset.step ) );
		if ( String( next ) === input.value ) return;
		input.value = next;
		const update = $( '.woocommerce-cart-form [name="update_cart"]' );
		if ( update ) {
			update.disabled = false;
			update.click();
		}
	} );

	// Keep the header badge in sync after WooCommerce's AJAX cart update / removal.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_wc_div', () => {
			const cartForm = $( '.woocommerce-cart-form' );
			if ( cartForm ) $$( '.l-cart-count' ).forEach( ( el ) => ( el.textContent = cartForm.dataset.count ) );
		} );
	}
} )();
