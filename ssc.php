<?php
/*
 Plugin Name: SSC Member section
 Plugin URI: TBD
 Description: Work in progress for creating a members section
 Author: Klaus Harris
 Version: -1
 Author URI: https://klaus.blog
 */

define( 'SSC_MEMBERS_PRIVACY_TAXONOMY', 'post_tag' );
define( 'SSC_MEMBERS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSC_MEMBERS_PLUGIN_FILE', __FILE__ );


include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/admin.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/dashboard_widgets.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/edit_screen.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/scripts_styles.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/data.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/menu.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/users.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/post_types.php' );
include_once( SSC_MEMBERS_PLUGIN_DIR . 'inc/utilities.php' );

/**
 * Redirect the user to a login screen if he/she tries to access member content.
 */
function ssc_member_handle_redirects() {

	if ( is_user_logged_in() ) {
		return;
	}

	$slug = basename( get_permalink() );

	// @var WP_POST $post
	global $post;

	if ( $slug == 'members' || ( is_singular( $post ) && ssc_member_is_private_post( $post ) ) ) {
		wp_redirect( wp_login_url() . '?mbo=1' );
	}
}

add_action( 'wp', 'ssc_member_handle_redirects' );


function ssc_member_admin_bar_visibility() {

	if ( is_user_logged_in() && is_generic_member_user() ) {
		show_admin_bar( false );
	}
}

add_action( 'wp', 'ssc_member_admin_bar_visibility' );


/**
 * Adding for completeness
 *
 * @param $output
 *
 * @return string
 */
function ssc_members_robots_override( $output ) {
	$output .= "Disallow: /members/*\n";

	return $output;
}

add_filter( 'robots_txt', 'ssc_members_robots_override', 0, 2 );


/**
 * Disable commenting for the generic user
 *
 * @param $open
 * @param $post_id
 *
 * @return bool
 */
function ssc_members_comments_open( $open, $post_id ) {

	if ( is_user_logged_in() && is_generic_member_user() ) {
		return false;
	}
}

add_filter( 'comments_open', 'ssc_members_comments_open', 10, 2 );


