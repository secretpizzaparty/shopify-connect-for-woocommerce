<?php

if ( ! class_exists( 'WC_Product' ) ) {
	return;
}

class WC_Shopify_Product extends WC_Product {

	public function __construct( $product ) {
		$this->product_type = 'shopify';
		parent::__construct( $product );
	}

}
