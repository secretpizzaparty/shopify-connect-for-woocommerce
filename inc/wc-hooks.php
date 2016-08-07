<?php

add_filter( 'product_type_selector', 'shopify_wc_connect_product_type_selector' );
function shopify_wc_connect_product_type_selector( $types ) {
	$types['shopify'] = __( 'Shopify', 'shopify-wc-connect' );
	return $types;
}
