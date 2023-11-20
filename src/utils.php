<?php
/**
 * Various utility functions.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Gets the byline for a post.
 *
 * @param WP_Post|int $post The post or post ID.
 * @return string
 *
 * @TODO: try to switch this to use get_the_author() instead.
 * @TODO: If we don't switch, look into wp_sprintf().
 */
function get_byline( $post ) {
	$post   = get_post( $post );
	$byline = '';
	if ( function_exists( '\Byline_Manager\get_the_byline' ) ) {
		$byline = \Byline_Manager\get_the_byline( $post->ID );
	} elseif ( function_exists( 'get_coauthors' ) ) {
		$authors      = get_coauthors( $post->ID );
		$author_names = wp_list_pluck( $authors, 'display_name' );
		if ( ! empty( $author_names ) ) {
			if ( 1 === count( $author_names ) ) {
				$byline = sprintf(
					/* translators: %s is the author name. */
					__( 'By %s', 'wp-newsletter-builder' ),
					$author_names[0]
				);
			} elseif ( 2 === count( $author_names ) ) {
				$byline = sprintf(
					/* translators: %1$s is the first author name, %2$s is the second author name. */
					__( 'By %1$s and %2$s', 'wp-newsletter-builder' ),
					$author_names[0],
					$author_names[1]
				);
			} else {
				$last_author = array_pop( $author_names );
				$byline      = sprintf(
					/* translators: %1$s is a list of author names, %2$s is the last author name. */
					__( 'By %1$s, and %2$s', 'wp-newsletter-builder' ),
					implode( ', ', $author_names ),
					$last_author
				);
			}
		}
	} else {
		$author = get_user_by( 'ID', $post->post_author );
		if ( ! is_wp_error( $author ) ) {
			$byline = sprintf(
				/* translators: %s is the author name. */
				__( 'By %s', 'wp-newsletter-builder' ),
				$author->display_name
			);
		}
	}
	/**
	 * Filters the byline.
	 */
	return apply_filters( 'wp_newsletter_builder_byline', $byline, $post );
}

/**
 * Gets the post type on the post edit screen.
 *
 * @return string
 */
function get_edit_post_type() {
	global $post, $typenow, $current_screen;

	if ( $post && $post->post_type ) {
		return $post->post_type;
	}
	if ( $typenow ) {
		return $typenow;
	}

	if ( $current_screen && $current_screen->post_type ) {
		return $current_screen->post_type;
	}

	if ( isset( $_REQUEST['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return sanitize_key( $_REQUEST['post_type'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	return null;
}
