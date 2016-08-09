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

add_action( 'woocommerce_product_data_panels', 'shopify_wc_connect_product_data_panels' );
function shopify_wc_connect_product_data_panels() {
	global $thepostid;
	?>
	<div id="shopify_product_options" class="panel woocommerce_options_panel"><?php

		echo '<div class="options_group pricing">';

		woocommerce_wp_text_input( array(
			'id' => '_regular_price',
			'label' => __( 'Product price', 'shopify-wc-connect' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			'data_type' => 'price',
		) );

		echo '<p>' .  __( 'For a consistent user experience, this price should match the product price on Shopify.', 'shopify-wc-connect' ) . '</p>';

		echo '</div>';

		echo '<div class="options_group shopify_shortcode wp-editor-wrap">';

		?>

		<div class="shopify-wc-connect-shortcode-buttons">
			<button id="shopify-wc-connect-add-shortcode" class="button secp-add-shortcode" data-editor-id="shopify-wc-connect-shortcode">
				<?php esc_html_e( 'Generate Embed Code', 'shopify-wc-connect' ); ?>
			</button>

			<button id="shopify-wc-connect-clear-shortcode" class="button secp-clear-shortcode" data-editor-id="shopify-wc-connect-shortcode">
				<?php esc_html_e( 'Remove Embed Code', 'shopify-wc-connect' ); ?>
			</button>
		</div>
		<?php

		woocommerce_wp_textarea_input( array(
			'class' => 'wp-editor-area',
			'id' => 'shopify-wc-connect-shortcode',
			'label' => __( 'Shopify Embed Code', 'shopify-wc-connect' ),
			'custom_attributes' => array( 'readonly' => 'readonly' ),
			'value' => get_post_meta( $thepostid, '_shopify_embed_code', true ),
		) );

		echo '</div>';
		?>
	</div>
	<?php
}

add_action( 'woocommerce_process_product_meta_shopify', 'shopify_wc_connect_process_product_meta' );
function shopify_wc_connect_process_product_meta( $post_id ) {
	if ( empty( $_POST ) || empty( $_POST['shopify-wc-connect-shortcode'] ) ) {
		return;
	}

	$shortcode = trim( wp_kses_post( $_POST['shopify-wc-connect-shortcode'] ) );
	if ( has_shortcode( $shortcode, 'shopify' ) ) {
		update_post_meta( $post_id, '_shopify_embed_code', $shortcode );
	}
}
