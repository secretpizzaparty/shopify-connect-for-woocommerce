<?php

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

function shopify_wc_connect_single_product_summary() {
	$shortcode = wp_kses_post( get_post_meta( get_the_ID(), '_shopify_embed_code', true ) );
	if ( ! empty( $shortcode ) && has_shortcode( $shortcode, 'shopify' ) ) {
		echo '<p>';
		echo do_shortcode( $shortcode );
		echo '</p>';
	}
}
add_action( 'woocommerce_single_product_summary', 'shopify_wc_connect_single_product_summary', 40 );

function shopify_wc_connect_takeover_cart_and_checkout() {
	if ( is_cart() || is_checkout() || is_checkout_pay_page() ) {
		wp_redirect( get_permalink( wc_get_page_id( 'shop' ) ) . '#shopify_cart' );
		exit();
	}
}
add_action( 'parse_query', 'shopify_wc_connect_takeover_cart_and_checkout', 10 );

function shopify_wc_connect_enqueue_scripts() {
	wp_enqueue_script( 'shopify-wc-connect', plugins_url( '../scripts/shopify-wc-connect.js', __FILE__ ), array(), SHOPIFY_WC_CONNECT_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'shopify_wc_connect_enqueue_scripts' );
