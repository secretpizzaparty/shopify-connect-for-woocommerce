<?php

class Shopify_WC_Connect_Meta_Box {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	public function add_meta_box( $post_type ) {
		$post_types = 'product';
		if ( $post_types = 'product') {
			add_meta_box(
				'shopify_embed_code',
				'Shopify Embed Code',
				array( $this, 'render_form'),
				$post_type,
				'side',
				'low'
			);
		}
	}

	public function save( $post_id ) {
		if ( array_key_exists('shopify_embed_code', $_POST ) ) {
			update_post_meta( $post_id,
				'_shopify_embed_code',
				$_POST['shopify_embed_code']
			);
		}
	}

	public function render_form( $post ) {
		?>
		<?php $value = get_post_meta( $post->ID, '_shopify_embed_code', true ); ?>
		<textarea class="widefat" cols="50" rows="5" name="shopify_embed_code" id="shopify_embed_code"/><?php if ( isset( $value ) ) echo esc_textarea( $value ); ?></textarea>
		<?php
	}
}