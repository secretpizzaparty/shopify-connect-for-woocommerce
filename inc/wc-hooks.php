<?php

add_filter( 'product_type_selector', 'shopify_wc_connect_product_type_selector' );
function shopify_wc_connect_product_type_selector( $types ) {
	$types = array( 'shopify' => __( 'Shopify Product', 'shopify-wc-connect' ) );
	return $types;
}

add_filter( 'woocommerce_product_data_tabs', 'shopify_wc_connect_product_tabs', 98 );
function shopify_wc_connect_product_tabs( $tabs ) {
	unset($tabs['shipping']);
	$shopify_tab = array( 'shopify' => array(
		'label'  => __( 'General', 'shopify-wc-connect' ),
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
			'label' => __( 'Regular price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			'data_type' => 'price',
		) );

		echo '<p>' .  __( 'The price here should match the price you\'ve set on Shopify.', 'shopify-wc-connect' ) . '</p>';

		woocommerce_wp_text_input( array(
			'id' => '_sale_price',
			'label' => __( 'Sale price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			'data_type' => 'price',
		) );

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
	if ( empty( $_POST ) || ! isset( $_POST['shopify-wc-connect-shortcode'] ) ) {
		return;
	}

	if ( empty( $_POST['shopify-wc-connect-shortcode'] ) ) {
		delete_post_meta( $post_id, '_shopify_embed_code' );
	} else {
		$shortcode = trim( wp_kses_post( $_POST['shopify-wc-connect-shortcode'] ) );
		if ( has_shortcode( $shortcode, 'shopify' ) ) {
			update_post_meta( $post_id, '_shopify_embed_code', $shortcode );
		}
	}
}

add_filter( 'woocommerce_product_settings', 'shopify_wc_connect_product_settings' );
function shopify_wc_connect_product_settings( $settings ) {
	$settings_to_search = $settings;
	$settings_to_remove = array(
		'woocommerce_cart_redirect_after_add',
		'woocommerce_enable_ajax_add_to_cart',
	);

	foreach( $settings_to_search as $setting_key => $setting ) {
		if ( in_array( $setting['id'], $settings_to_remove ) ) {
			unset( $settings[$setting_key] );
			continue;
		}
	}

	return $settings;
}

add_filter( 'woocommerce_general_settings', 'shopify_wc_connect_general_settings' );
function shopify_wc_connect_general_settings( $settings ) {
	$settings_to_search = $settings;
	$settings_to_remove = array(
		'woocommerce_default_country',
		'woocommerce_allowed_countries',
		'woocommerce_all_except_countries',
		'woocommerce_specific_allowed_countries',
		'woocommerce_ship_to_countries',
		'woocommerce_specific_ship_to_countries',
		'woocommerce_default_customer_address',
		'woocommerce_calc_taxes',
	);

	foreach( $settings_to_search as $setting_key => $setting ) {
		if ( in_array( $setting['id'], $settings_to_remove ) ) {
			unset( $settings[$setting_key] );
			continue;
		}
	}

	return $settings;
}

add_filter( 'woocommerce_get_settings_pages', 'shopify_wc_connect_get_settings_pages' );
function shopify_wc_connect_get_settings_pages( $settings_pages ) {
	$settings_to_search = $settings_pages;
	$setting_classes_to_remove = array(
		'email' => 'WC_Settings_Emails',
	);

	// if we have a site we can use, then redirect (in shopify-connect.php) will kick in instead of adding these tabs
	$shopify_site = get_option( 'secp_shop', false );
	if ( ! $shopify_site || false !== strpos( $shopify_site, 'embeds.shopify.com' ) || false !== strpos( $shopify_site, 'embeds.myshopify.com' ) ) {
		$setting_classes_to_remove['shipping'] = 'WC_Settings_Shipping';
		$setting_classes_to_remove['checkout'] = 'WC_Settings_Payment_Gateways';
		$setting_classes_to_remove['account'] = 'WC_Settings_Accounts';
	}

	foreach( $settings_to_search as $setting_key => $setting_class ) {
		foreach( $setting_classes_to_remove as $id => $setting_class_to_remove ) {
			if ( is_a( $setting_class, $setting_class_to_remove ) ) {
				remove_filter( 'woocommerce_settings_tabs_array', array( $setting_class, 'add_settings_page' ), 20 );
				remove_action( 'woocommerce_sections_' . $id, array( $setting_class, 'output_sections' ) );
				remove_action( 'woocommerce_settings_' . $id, array( $setting_class, 'output' ) );
				remove_action( 'woocommerce_settings_save_' . $id, array( $setting_class, 'save' ) );
				remove_action( 'woocommerce_admin_field_email_notification', array( $setting_class, 'email_notification_setting' ) );
				unset( $settings_pages[ $setting_key ] );
				continue;
			}
		}
	}

	return $settings_pages;
}


function shopify_wc_connect_remove_admin_menu_items() {
	// if we have a site we can use, then redirect (in shopify-connect.php) will kick in instead
	$shopify_site = get_option( 'secp_shop', false );
	if ( $shopify_site && false === strpos( $shopify_site, 'embeds.shopify.com' ) || false !== strpos( $shopify_site, 'embeds.myshopify.com' ) ) {
		return;
	}

	$to_remove = array(
		'edit.php?post_type=shop_order',
		'edit.php?post_type=shop_coupon',
		'wc-reports',
	);

	global $submenu;
	foreach( $submenu as $section => $menu_items ) {
		if ( $section !== 'woocommerce' ) {
			continue;
		}

		foreach( $menu_items as $index => $submenu_item ) {
			if ( in_array( $submenu_item[2], $to_remove ) ) {
				unset( $submenu['woocommerce'][$index] );
			}
		}
	}

}
add_action( 'admin_head', 'shopify_wc_connect_remove_admin_menu_items' );

add_filter( 'manage_product_posts_columns', 'shopify_wc_connect_product_columns', 20 );
function shopify_wc_connect_product_columns( $columns ) {
	unset( $columns['is_in_stock'] );
	return $columns;
}

add_filter( 'woocommerce_get_sections_products', 'shopify_wc_connect_get_sections_products' );
function shopify_wc_connect_get_sections_products( $sections ) {
	unset( $sections['downloadable'] );

	// if we have a site we can use, then redirect (in shopify-connect.php) will kick in instead of adding these tabs
	$shopify_site = get_option( 'secp_shop', false );
	if ( ! $shopify_site || false !== strpos( $shopify_site, 'embeds.shopify.com' ) || false !== strpos( $shopify_site, 'embeds.myshopify.com' ) ) {
		unset( $sections['inventory'] );
	}

	return $sections;
}

add_action( 'wp_dashboard_setup', 'shopify_wc_connect_remove_dashboard_widget' );
function shopify_wc_connect_remove_dashboard_widget() {
	remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'side' );
}

add_filter( 'woocommerce_downloadable_products_settings', '__return_empty_array' );

add_filter( 'wc_tax_enabled', '__return_false' );
add_filter( 'wc_product_weight_enabled', '__return_false' );
add_filter( 'wc_product_dimensions_enabled', '__return_false' );
add_filter( 'option_woocommerce_manage_stock', '__return_false' );
