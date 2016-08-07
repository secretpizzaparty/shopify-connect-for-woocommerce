<?php

function shopify_wc_connect_remove_buy_buttons() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}
add_action( 'init', 'shopify_wc_connect_remove_buy_buttons' );

function shopify_wc_connect_add_shopify_buttons() {
	echo '<p>';
	echo do_shortcode( get_post_meta( get_the_ID(), '_shopify_embed_code', true ) );
	echo '</p>';
}
add_action( 'woocommerce_single_product_summary', 'shopify_wc_connect_add_shopify_buttons', 20 );