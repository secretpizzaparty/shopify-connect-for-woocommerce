jQuery( document ).ready( function( $ ){
	if ( '#shopify_cart' === window.location.hash ) {
		setTimeout( function() {
			$('div[data-embed_type="cart_content"]').addClass('active');
		}, 800 );
	}
});
