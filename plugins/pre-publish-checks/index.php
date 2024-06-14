<?php
/**
 * Adds the pre publish panel to the newsletter template.
 *
 * This file will be copied to the assets build directory.
 *
 * @package wp-newsletter-builder-demo-plugin
 */

namespace WP_Newsletter_Builder;

add_action(
	'enqueue_block_editor_assets',
	__NAMESPACE__ . '\action_enqueue_pre_publish_assets'
);

/**
 * Registers all slotfill assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_pre_publish_scripts(): void {
	wp_register_script(
		'plugin-pre-publish-checks',
		get_entry_asset_url( 'pre-publish-checks' ),
		get_asset_dependency_array( 'pre-publish-checks' ),
		get_asset_version( 'pre-publish-checks' ),
		true
	);
	wp_set_script_translations( 'pre-publish-checks' );
}
add_action( 'init', __NAMESPACE__ . '\register_pre_publish_scripts' );

/**
 * Enqueue block editor assets for this slotfill.
 */
function action_enqueue_pre_publish_assets(): void {
	$post_type = get_edit_post_type();
	if ( 'nb_newsletter' !== $post_type ) {
		return;
	}
	wp_enqueue_script( 'plugin-pre-publish-checks' );
}
