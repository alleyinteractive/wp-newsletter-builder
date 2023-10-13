<?php
/**
 * Adds a separator to a block.
 *
 * @package newsletter-builder
 */

namespace Newsletter_Builder;

/**
 * Registers assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function register_separator_scripts() {
	wp_register_script(
		'plugin-newsletter-separator',
		get_entry_asset_url( 'newsletter-builder-separator' ),
		get_asset_dependency_array( 'newsletter-builder-separator' ),
		get_asset_version( 'newsletter-builder-separator' ),
		true
	);
	wp_set_script_translations( 'plugin-newsletter-separator' );
}
add_action( 'init', __NAMESPACE__ . '\register_separator_scripts' );

/**
 * Enqueue block editor assets for separator.
 */
function action_enqueue_separator_assets() {
	$post_type = get_edit_post_type();
	if ( 'nb_newsletter' !== $post_type ) {
		return;
	}
	wp_enqueue_script( 'plugin-newsletter-separator' );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\action_enqueue_separator_assets' );


/**
 * Add a block separator if needed.
 *
 * @param string    $block_content The current block content.
 * @param \WP_Block $block The parse block object.
 * @return string   $block_content The new block content.
 */
function add_separator( $block_content, $block ) {
	if ( empty( $block['attrs']['hasSeparator'] ) ) {
		return $block_content;
	}

	if ( empty( wp_strip_all_tags( $block_content ) ) ) {
		return $block_content;
	}

	$nb_wide_separator  = ! empty( $block['attrs']['separatorIsWide'] );
	$nb_separator_class = $nb_wide_separator ? ' is-style-wide' : '';

	return sprintf(
		'<hr class="wp-block-separator has-alpha-channel-opacity%s"/>%s',
		esc_attr( $nb_separator_class ),
		$block_content
	);
}

add_filter( 'render_block', __NAMESPACE__ . '\add_separator', 10, 2 );
