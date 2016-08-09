jQuery( document ).ready( function( $ ){
	var $add_button = $( '#shopify-wc-connect-add-shortcode' ),
		$remove_button = $( '#shopify-wc-connect-clear-shortcode' ),
		shortcode_textarea_id = 'shopify-wc-connect-shortcode',
		$shortcode_textarea = $( '#' + shortcode_textarea_id ),
		$current_shortcode_value = $shortcode_textarea.val();

	// $shortcode_textarea.on( 'propertychange', function() {
	// 	console.log( 'change' );
	// 	var $this = $(this);
	// 	var currentVal = $shortcode_textarea.val();
	// 	if ( currentVal == oldVal ) {
	// 		return; // prevent multiple simultaneous triggers
	// 	}
	//
	// 	console.log( '1' );
	// 	oldVal = currentVal;
	// 	$this.val( $.trim( currentVal ) );
	// });

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
});