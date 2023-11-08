<?php
/**
 * Adds the sidebar script to the Post edit screen.
 *
 * This file will be copied to the assets build directory.
 *
 * @package wp-newsletter-builder-demo-plugin
 */

namespace WP_Newsletter_Builder;

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
		get_entry_asset_url( 'wp-newsletter-builder-from-post' ),
		get_asset_dependency_array( 'wp-newsletter-builder-from-post' ),
		get_asset_version( 'wp-newsletter-builder-from-post' ),
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
	$settings = new Settings();

	$templates    = get_posts( // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
		[
			'post_type'        => 'nb_template',
			'posts_per_page'   => -1,
			'orderby'          => 'ID',
			'suppress_filters' => false,
		]
	);
	$template_map = [];

	foreach ( $templates as $template ) {
		$template_map[ $template->ID ] = $template->post_title;
	}

	wp_enqueue_script( 'plugin-newsletter-from-post' );
	wp_localize_script(
		'plugin-newsletter-from-post',
		'newsletterBuilder',
		[
			'fromNames'     => $settings->get_from_names(),
			'templates'     => $template_map,
			'breakingLists' => ( new Breaking_Recipients() )->get_breaking_recipients(),
		]
	);
}
