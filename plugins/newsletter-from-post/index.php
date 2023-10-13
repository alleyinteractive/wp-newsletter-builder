<?php
/**
 * Adds the sidebar script to the Post edit screen.
 *
 * This file will be copied to the assets build directory.
 *
 * @package newsletter-builder-demo-plugin
 */

namespace Newsletter_Builder;

add_action(
	'enqueue_block_editor_assets',
	__NAMESPACE__ . '\action_enqueue_post_sidebar_assets'
);

/**
 * Registers all slotfill assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_post_plugin_scripts() {
	wp_register_script(
		'plugin-newsletter-from-post',
		get_entry_asset_url( 'newsletter-builder-from-post' ),
		get_asset_dependency_array( 'newsletter-builder-from-post' ),
		get_asset_version( 'newsletter-builder-from-post' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-from-post' );
}
add_action( 'init', __NAMESPACE__ . '\register_post_plugin_scripts' );

/**
 * Enqueue block editor assets for this slotfill.
 */
function action_enqueue_post_sidebar_assets() {
	$post_type = get_edit_post_type();
	if ( 'post' !== $post_type ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-from-post' );
	wp_localize_script(
		'plugin-newsletter-from-post',
		'newsletterBuilder',
		[
			'fromNames'     => Campaign_Monitor_Client::instance()->get_from_names(),
			'breakingLists' => ( new Breaking_Recipients() )->get_breaking_recipients(),
		]
	);
}
