jQuery( document ).ready( function( $ ){
	if ( '#shopify_cart' === window.location.hash ) {
		history.pushState( "", document.title, window.location.pathname + window.location.search );
		setTimeout( function() {
			$('div[data-embed_type="cart_content"]').addClass('active');
		}, 800 );
	}
});
