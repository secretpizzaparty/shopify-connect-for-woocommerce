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
		add_action( 'load-post-new.php', 'shopify_wc_connect_meta_box' );
		add_action( 'load-post.php', 'shopify_wc_connect_meta_box' );
	} else {
		require_once( 'inc/front-end.php' );
	}
}

function shopify_wc_connect_meta_box() {
	require_once( 'inc/metabox.php' );
	new Shopify_WC_Connect_Meta_Box();
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
