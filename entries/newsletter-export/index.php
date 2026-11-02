<?php
/**
 * Adds the export panel to the newsletter edit screen.
 *
 * This file will be copied to the assets build directory and loaded by load_scripts().
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

add_action(
	'enqueue_block_editor_assets',
	__NAMESPACE__ . '\action_enqueue_export_sidebar_assets'
);

/**
 * Registers all slotfill assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_export_plugin_scripts(): void {
	$src = get_entry_asset_url( 'newsletter-export' );
	if ( '' === $src ) {
		return;
	}

	wp_register_script(
		'plugin-newsletter-export',
		$src,
		get_asset_dependency_array( 'newsletter-export' ),
		get_asset_version( 'newsletter-export' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-export' );
}
add_action( 'init', __NAMESPACE__ . '\register_export_plugin_scripts' );

/**
 * Enqueue block editor assets for this slotfill.
 */
function action_enqueue_export_sidebar_assets(): void {
	$post_type = get_edit_post_type();
	if ( 'nb_newsletter' !== $post_type || ! current_user_can( 'edit_post', (int) get_the_ID() ) ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-export' );
}
