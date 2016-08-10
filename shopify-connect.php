<?php
/**
 * Plugin Name: Shopify Connect for WooCommerce
 * Plugin URI:  https://secretpizza.party
 * Description: Connect your WooCommerce site with Shopify
 * Version:     1.1
 * Author:      secret pizza party
 * Author URI:  https://secretpizza.party
 * License:     GPLv2
 * Text Domain: shopify-connect
 */

/**
 * Copyright (c) 2016 secret pizza party (email : hey@secretpizza.party)
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

define( 'SHOPIFY_WC_CONNECT_VERSION', 1.1 );

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
	}
}
