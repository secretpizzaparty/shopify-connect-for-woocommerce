<?php
/**
 * Plugin Name: Shopify Connect for WooCommerce
 * Plugin URI:  https://secretpizza.party
 * Description: Connect your WooCommerce site with Shopify
 * Version:     1.0.0
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

function remove_woocommerce_buy_buttons() {
  remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
  remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}
add_action( 'init', 'remove_woocommerce_buy_buttons' );

if ( is_admin() ) {  //do nothing for front end requests
    add_action( 'load-post-new.php', 'shopify_connect_meta_box' );
    add_action( 'load-post.php', 'shopify_connect_meta_box' );
}

function shopify_connect_meta_box() {
    new create_shopify_meta_box();
}

function add_shopify_buttons() {
    echo '<p>';
    echo do_shortcode( get_post_meta( get_the_ID(), '_shopify_embed_code', true ) );
    echo '</p>';
}
add_action( 'woocommerce_single_product_summary', 'add_shopify_buttons', 20 );
