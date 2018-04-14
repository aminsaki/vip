<?php
function wpvip_add_meta_box() {
	$screens = array( 'post', 'page' );
	foreach ( $screens as $screen ) {

		add_meta_box(
			'wpvip-meta-box',
			'تنظیمات مطلب ویژه',
			'wpvip_callback',
			$screen,
                        'side'
		);
	}
}
add_action('add_meta_boxes', 'wpvip_add_meta_box' );
function wpvip_callback($post){
 $value = get_post_meta( $post->ID, 'vip-level', true );
 ?>
<?php wp_nonce_field( 'wpvip_meta_box', 'wpvip_meta_box_nonce' ); ?>
<div>
  <select style="width: 100%" name="wpvip_level">
    <option value="normal">عادی</option>
    <option value="vip-gold" <?php echo ($value=='vip-gold')?'selected':''; ?>>طلایی</option>
    <option value="vip-silver" <?php echo ($value=='vip-silver')?'selected':''; ?>>نقره ای</option>
    <option value="vip-bronze" <?php echo ($value=='vip-bronze')?'selected':''; ?>>برنزی</option>
</select>
</div>

<?php
}
function wpvip_save_meta_box_data( $post_id ) {
	// Check if our nonce is set.
	if ( ! isset( $_POST['wpvip_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['wpvip_meta_box_nonce'], 'wpvip_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['wpvip_level'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['wpvip_level'] );
        if($my_data=='normal'){
            return;
        }
	// Update the meta field in the database.
	update_post_meta( $post_id, 'vip-level', $my_data );
}
add_action('save_post', 'wpvip_save_meta_box_data' );