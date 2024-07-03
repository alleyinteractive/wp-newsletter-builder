<?php
/**
 * Block Name: Post.
 *
 * @package wp-newsletter-builder
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function wp_newsletter_builder_post_content_block_init(): void {
	/**
	 * Filters whether to register the block.
	 *
	 * @param bool   $register Whether to register the block. Default true.
	 * @param string $name     Block name.
	 */
	if ( ! apply_filters( 'wp_newsletter_builder_register_block', true, 'post-content' ) ) {
		return;
	}
	// Register the block by passing the location of block.json.
	register_block_type(
		__DIR__
	);
}
add_action( 'init', 'wp_newsletter_builder_post_content_block_init' );
