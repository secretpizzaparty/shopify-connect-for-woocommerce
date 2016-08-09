<?php

add_filter( 'product_type_selector', 'shopify_wc_connect_product_type_selector' );
function shopify_wc_connect_product_type_selector( $types ) {
	$types = array( 'shopify' => __( 'Shopify', 'shopify-wc-connect' ) );
	return $types;
}

add_filter( 'woocommerce_product_data_tabs', 'shopify_wc_connect_product_tabs', 98 );
function shopify_wc_connect_product_tabs( $tabs ) {
	unset($tabs['shipping']);
	$shopify_tab = array( 'shopify' => array(
		'label'  => __( 'Shopify', 'shopify-wc-connect' ),
		'target' => 'shopify_product_options',
		'class'  => array( 'shopify_tab', 'show_if_shopify' ),
	) );
	// puts the shopify tab on top
	return $shopify_tab + $tabs;
}
}
