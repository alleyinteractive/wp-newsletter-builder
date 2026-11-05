<?php
/**
 * Removes the theme's global styles from the newsletter and template editors.
 *
 * This file will be copied to the assets build directory and loaded by load_scripts().
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

add_action(
	'enqueue_block_editor_assets',
	__NAMESPACE__ . '\action_enqueue_editor_styles_assets'
);

/**
 * Registers the script so that it can be enqueued in the block editor.
 */
function register_editor_styles_scripts(): void {
	$src = get_entry_asset_url( 'editor-styles' );
	if ( '' === $src ) {
		return;
	}

	wp_register_script(
		'wp-newsletter-builder-editor-styles',
		$src,
		get_asset_dependency_array( 'editor-styles' ),
		get_asset_version( 'editor-styles' ),
		true
	);
}
add_action( 'init', __NAMESPACE__ . '\register_editor_styles_scripts' );

/**
 * Enqueue the script for newsletters and templates, unless the theme's styles are kept.
 */
function action_enqueue_editor_styles_assets(): void {
	$post_type = get_edit_post_type();
	if ( ( 'nb_newsletter' !== $post_type && 'nb_template' !== $post_type ) || ! removes_theme_editor_styles() ) {
		return;
	}
	wp_enqueue_script( 'wp-newsletter-builder-editor-styles' );
}
