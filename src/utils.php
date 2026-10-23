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
 * @param \WP_Post|int $post The post or post ID.
 * @return string
 *
 * @TODO: try to switch this to use get_the_author() instead.
 * @TODO: If we don't switch, look into wp_sprintf().
 */
function get_byline( \WP_Post|int $post ): string {
	$post   = get_post( $post );
	$byline = '';

	// Bail early if we don't have a post.
	if ( ! $post instanceof \WP_Post ) {
		return $byline;
	}

	if ( function_exists( '\Byline_Manager\get_the_byline' ) ) {
		$byline_manager_byline = \Byline_Manager\get_the_byline( $post->ID );
		$byline                = is_string( $byline_manager_byline ) ? $byline_manager_byline : '';
	} elseif ( function_exists( 'get_coauthors' ) ) {
		$authors      = get_coauthors( $post->ID );
		$author_names = is_array( $authors ) ? array_values( array_filter( wp_list_pluck( $authors, 'display_name' ), 'is_string' ) ) : [];
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
		if ( ! empty( $author->display_name ) ) {
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
	$filtered_byline = apply_filters( 'wp_newsletter_builder_byline', $byline, $post );

	return is_string( $filtered_byline ) ? $filtered_byline : $byline;
}

/**
 * Gets the post type on the post edit screen.
 *
 * @return string
 */
function get_edit_post_type(): string {
	global $post, $typenow, $current_screen;

	if ( $post instanceof \WP_Post && $post->post_type ) {
		return $post->post_type;
	}
	if ( is_string( $typenow ) && $typenow ) {
		return $typenow;
	}

	if ( $current_screen instanceof \WP_Screen && $current_screen->post_type ) {
		return $current_screen->post_type;
	}

	if ( isset( $_REQUEST['post_type'] ) && is_string( $_REQUEST['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return sanitize_key( $_REQUEST['post_type'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	return '';
}

/**
 * Get the ID of the template assigned to a newsletter.
 *
 * @param int $newsletter_id The newsletter post ID.
 * @return int The template post ID, or 0 if none is set.
 */
function get_newsletter_template_id( int $newsletter_id ): int {
	$template_id = get_post_meta( $newsletter_id, 'nb_newsletter_template', true );

	return is_numeric( $template_id ) ? (int) $template_id : 0;
}

/**
 * Get a string meta value from a newsletter template.
 *
 * @param int    $template_id The template post ID.
 * @param string $meta_key    The meta key to retrieve.
 * @return string The meta value, or an empty string if it is not set or not a string.
 */
function get_template_meta_string( int $template_id, string $meta_key ): string {
	$value = get_post_meta( $template_id, $meta_key, true );

	return is_string( $value ) ? $value : '';
}
