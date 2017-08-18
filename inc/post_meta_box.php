<?php
/**
 * Add a privacy setting to post pages
 * 
 * @todo, make screen types configurable from a settings page
 */
function ssc_members_add_post_privacy_setting() {
	add_meta_box(
		'ssc_members_post_privacy', // id
		'Members only', // title
		'ssc_members_render_post_privacy_box', // rendering callback
		'post', // which screen to display on
		'normal', // display context (normal, side, advanced)
		'default' // display priority (high, low, default)
	);
}

add_action( 'add_meta_boxes', 'ssc_members_add_post_privacy_setting' );

function ssc_members_render_post_privacy_box() {

	// @var WP_Post $post;
	global $post;

	// @todo tidy
	$post_meta_values = get_post_custom( $post->ID );

	$privacy_value = ( isset( $post_meta_values['ssc_members_post_privacy'] ) &&
	                   is_array( $post_meta_values['ssc_members_post_privacy'] ) &&
	                   isset( $post_meta_values['ssc_members_post_privacy'][0] )
	)
		?
		esc_attr( $post_meta_values['ssc_members_post_privacy'][0] ) : false;

	wp_nonce_field( 'my_delete_action', 'ssc_wpnonce' );
	?>
	<p>
		<input type="checkbox" id="ssc_members_post_privacy"
		       name="ssc_members_post_privacy" <?php echo checked( $privacy_value, 'on' ); ?> />
		<label for="ssc_members_post_privacy">Yes</label>
	</p>
	<?php
}


function cd_meta_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$retrieved_nonce = $_REQUEST['ssc_wpnonce'];

	if ( ! wp_verify_nonce( $retrieved_nonce, 'my_delete_action' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post' ) ) {
		return;
	}

	$checkbox_value = isset( $_POST['ssc_members_post_privacy'] ) && $_POST['ssc_members_post_privacy'] ? 'on' : false;

	if ( $checkbox_value ) {
		update_post_meta( $post_id, 'ssc_members_post_privacy', 'on' );
	} else {
		delete_post_meta( $post_id, 'ssc_members_post_privacy', 'on' );
	}
}

add_action( 'save_post', 'cd_meta_box_save' );