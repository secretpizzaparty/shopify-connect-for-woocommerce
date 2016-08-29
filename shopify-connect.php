<?php
/**
 * Plugin Name: Shopify Connect for WooCommerce
 * Plugin URI:  https://secretpizza.party
 * Description: WooCommerce and Shopify go together like peanut butter and jelly. Let us help you connect them together.
 * Version:     1.2
 * Author:      secret pizza party, jkudish
 * Author URI:  https://secretpizza.party
 * License:     GPLv2
 * Text Domain: shopify-connect
 */

/**
 * Copyright (c) 2016 secret pizza party (email : hey@secretpizza.party)
 * Copyright (c) 2016 Spark Consulting Ltd. (email : info@sparkdev.io)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define( 'SHOPIFY_WC_CONNECT_VERSION', 1.2 );

function shopify_wc_connect_init() {
	load_plugin_textdomain( 'shopify-wc-connect' );
	shopify_wc_connect_load_dependencies();
}
add_action( 'init', 'shopify_wc_connect_init' );

function shopify_wc_connect_load_dependencies() {
	require_once( 'inc/wc-hooks.php' );
	require_once( 'inc/product-type.php' );

	if ( is_admin() ) {
		add_action( 'all_admin_notices', 'shopify_wc_connect_requirements_notice' );
		add_action( 'admin_print_scripts-post-new.php', 'shopify_wc_connect_admin_enqueue_scripts' );
		add_action( 'admin_print_scripts-post.php', 'shopify_wc_connect_admin_enqueue_scripts' );
		add_action( 'admin_print_scripts-edit.php', 'shopify_wc_connect_admin_enqueue_scripts' );
	} else {
		require_once( 'inc/front-end.php' );
	}
}

function shopify_wc_connect_admin_enqueue_scripts() {
	global $post_type;
	if ( 'product' !== $post_type ) {
		return;
	}

	wp_enqueue_script( 'shopify-wc-connect-admin', plugins_url( '/scripts/shopify-wc-connect-admin.js', __FILE__ ), array(), SHOPIFY_WC_CONNECT_VERSION, false );
	wp_enqueue_style( 'shopify-wc-connect-admin', plugins_url( '/assets/shopify-wc-connect-admin.css', __FILE__ ), array(), SHOPIFY_WC_CONNECT_VERSION, 'screen' );
	wp_localize_script( 'shopify-wc-connect-admin', 'shopify_wc_connect', array(
		'shortcode_type_not_supported' => __( 'Not supported with WooCommerce Integration.', 'shopify-wc-connect' ),
	) );

	if ( function_exists( 'shopify_ecommerce_plugin' ) ) {
		remove_action( 'media_buttons', array( shopify_ecommerce_plugin()->shortcode, 'media_buttons' ) );
		shopify_ecommerce_plugin()->shortcode->enqueue();
	}
}

function shopify_wc_connect_requirements_notice() {
	if ( ! defined( 'WC_VERSION' )
	     || -1 === version_compare( WC_VERSION, 2.0 )
	     || ! class_exists( 'Shopify_ECommerce_Plugin' )
	     || -1 === version_compare( Shopify_ECommerce_Plugin::VERSION, 1.0 )
	) {
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'Shopify Connect for WooCommerce - This plugin requires <em>WooCommerce</em> version 2.0 or higher and <em>Shopify eCommerce Plugin - Shopping Cart</em> version 1.0 or higher to function properly. Please make sure to <a href="%s">install/update</a> those plugins.', 'shopify-wc-connect' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
		deactivate_plugins( __FILE__ );
		return;
	}

	if ( ! defined( 'WC_VERSION' )
	     || -1 === version_compare( WC_VERSION, 2.0 )
	) {
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'Shopify Connect for WooCommerce - This plugin requires <em>WooCommerce</em> version 2.0 or higher and to function properly. Please make sure to <a href="%s">install/update</a> WooCommerce. We\'ve disabled Shopify Connect for WooCommerce in the mean time.', 'shopify-wc-connect' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
		deactivate_plugins( __FILE__ );
		return;
	}

	if ( ! class_exists( 'Shopify_ECommerce_Plugin' )
         || -1 === version_compare( Shopify_ECommerce_Plugin::VERSION, 1.0 )
	) {
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'Shopify Connect for WooCommerce - This plugin requires <em>Shopify eCommerce Plugin - Shopping Cart</em> version 1.0 or higher to function properly. Please make sure to <a href="%s">install/update</a> the Shopify plugin.  We\'ve disabled Shopify Connect for WooCommerce in the mean time.', 'shopify-wc-connect' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
		deactivate_plugins( __FILE__ );
		return;
	}
}

function shopify_wc_connect_redirect_admin_pages() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	// if we don't have a site we can use, then hide menus (in inc/wc-hooks.php) instead
	$shopify_site = get_option( 'secp_shop', false );
	if ( ! $shopify_site || false !== strpos( $shopify_site, 'embeds.shopify.com' ) || false !== strpos( $shopify_site, 'embeds.myshopify.com' ) ) {
		return;
	}

	$redirects = array(
		'edit-shop_order' => 'admin/orders',
		'edit-shop_coupon' => 'admin/discounts',
		'woocommerce_page_wc-reports' => 'admin/reports',
		'woocommerce_page_wc-settings' => array(
			'tax' => 'admin/settings/taxes',
			'shipping' => 'admin/settings/shipping',
			'checkout' => 'admin/settings/checkout',
			'account' => 'admin/settings/checkout',
			'products' => array(
				'inventory' => 'admin/products/inventory',
			),
		),
	);

	$current_screen = get_current_screen()->id;

	if ( ! empty( $redirects[ $current_screen ] ) ) {
		if ( is_array( $redirects[ $current_screen ] ) ) {
			if ( ! empty( $_GET['tab'] ) && isset( $redirects[ $current_screen ][ $_GET['tab'] ] ) ) {
				if ( is_array( $redirects[ $current_screen ][ $_GET['tab'] ] ) ) {
					if ( ! empty( $_GET['section'] ) && isset( $redirects[ $current_screen ][ $_GET['tab'] ][ $_GET['section'] ] ) ) {
						$shopify_path = $redirects[ $current_screen ][ $_GET['tab'] ][ $_GET['section'] ];
					}
				} else {
					$shopify_path = $redirects[ $current_screen ][ $_GET['tab'] ];
				}
			}
		} else {
			$shopify_path = $redirects[ $current_screen ];
		}

		if ( ! isset( $shopify_path ) ) {
			return;
		}

		$redirect_uri = esc_url_raw( 'https://' . trailingslashit( $shopify_site ) . $shopify_path );
		wp_redirect( $redirect_uri );
		exit;
	}
}
add_action( 'current_screen', 'shopify_wc_connect_redirect_admin_pages' );
