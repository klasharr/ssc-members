<?php
/**
 * @see inc/post_meta_box.php
 *
 * @todo explore using a custom post with capabilities to implement privacy
 *
 * @param WP_Query $query
 */
function ssc_member_exclude_private_posts( WP_Query $query ) {
	
	if ( is_user_logged_in() ) {
		return;
	}

	$query->is_main_query();

	$query->set( 'meta_query',
		array(
			array(
				'key'     => 'ssc_members_post_privacy',
				'compare' => 'NOT EXISTS',
			)
		)
	);

}

add_action( 'pre_get_posts', 'ssc_member_exclude_private_posts' );


/**
 * Hide member-page results from logged out user searches
 *
 * @param $args
 * @param $post_type
 *
 * @return mixed
 */
function ssc_member_search_modifications( $args, $post_type ) {

	if( 'member-page' !== $post_type || is_user_logged_in() ) {
		return $args;
	}

	$args['exclude_from_search'] = true;

	return $args;
}

add_filter( 'register_post_type_args', 'ssc_member_search_modifications', 20, 2 );
