jQuery( document ).ready( function( $ ){
	var $add_button = $( '#shopify-wc-connect-add-shortcode' ),
		$remove_button = $( '#shopify-wc-connect-clear-shortcode' ),
		shortcode_textarea_id = 'shopify-wc-connect-shortcode',
		$shortcode_textarea = $( '#' + shortcode_textarea_id );

	// ensures value gets replaced and trimmed properly
	$.valHooks.textarea = {
		get: function(elem) {
			if ( elem.id === shortcode_textarea_id ) {
				return '';
			}
		},
		set: function(elem, value) {
			if ( elem.id === shortcode_textarea_id ) {
				if ( -1 !== value.indexOf( 'embed_type="collection"' ) ) {
					console.log( value );
					setTimeout( function(){
						$shortcode_textarea.val( '' );
						alert( 'The WooCommerce Shopify Connect integration only allows the product embed to be used.' );
					}, 0 );
				} else if ( 0 === value.indexOf( "\n" ) ) {
					setTimeout( function(){
						$shortcode_textarea.val( $.trim( value ) );
					}, 0 );
				}
			}
		}
	};

	$add_button.on( 'click', function() {
		setTimeout( function(){
			var $modal = $('.secp-modal-wrap'),
				$undeseirable_input_label = $modal.find( '.secp-modal-secondpage .secp-modal-content .secp-show-label:first-child' ),
				$undeseirable_input = $undeseirable_input_label.find( 'input' ),
				$desirable_input =  $modal.find( '.secp-modal-secondpage .secp-modal-content .secp-show-label' ).not( ':first-child' ).find( 'input' );

			$modal.addClass( 'shopify-wc-connect-modal-wrap' );
			$undeseirable_input_label.append( '<br>' + shopify_wc_connect.shortcode_type_not_supported );
			$undeseirable_input.prop( 'disabled', true );
			$undeseirable_input.prop( 'checked', false );
			$desirable_input.prop( 'checked', true );
		}, 100 );
	} );

	$remove_button.on( 'click', function( e ) {
		e.preventDefault();
		$shortcode_textarea.val('');
	});

	// remove duplicate fields
	var $general_product_data_tab = $( '#general_product_data' );

	$general_product_data_tab.find( '._regular_price_field' ).remove();
	$general_product_data_tab.find( '._sale_price_field' ).remove();

	// remove some quick edit buttons
	$( '#wpbody-content' ).on( 'click', '.editinline', function(){
		setTimeout( function(){
			$( '.shipping_class' ).closest( '.alignleft' ).hide();
			$( '.stock_status' ).closest( '.alignleft' ).hide();
			$( '.backorders' ).closest( '.alignleft' ).hide();
		}, 0 );
	});
});
