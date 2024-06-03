<?php
/**
 * Block Name: Divider.
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
function divider_divider_block_init() {
	// Register the block by passing the location of block.json.
	register_block_type(
		__DIR__
	);

}
add_action( 'init', 'divider_divider_block_init' );
