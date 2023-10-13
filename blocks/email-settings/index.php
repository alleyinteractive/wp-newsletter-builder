<?php
/**
 * Block Name: Email Settings.
 *
 * @package newsletter-builder
 */

use Newsletter_Builder\Campaign_Monitor_Client;

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function newsletter_builder_email_settings_block_init() {
	// Register the block by passing the location of block.json.
	register_block_type(
		__DIR__
	);
}
add_action( 'init', 'newsletter_builder_email_settings_block_init' );
