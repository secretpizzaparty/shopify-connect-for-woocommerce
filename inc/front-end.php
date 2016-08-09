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