<?php
/**
 * Block_Modifications class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Block Modifications class
 */
class Block_Modifications {
	/**
	 * Sets things up.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'pre_render_block', [ $this, 'pre_render_post_block' ], 10, 2 );
	}

	/**
	 * Sets the global post object and overrides for the Newsletter Single Post block.
	 *
	 * @param mixed $block_content The block content.
	 * @param mixed $block         The parsed block.
	 * @return string|null The block content.
	 */
	public function pre_render_post_block( $block_content, $block ): string|null {
		if ( ! is_string( $block_content ) ) {
			$block_content = null;
		}
		if ( ! is_array( $block ) || ! isset( $block['blockName'] ) || ! is_array( $block['attrs'] ?? null ) ) {
			return $block_content;
		}
		if ( 'wp-newsletter-builder/post' === $block['blockName'] ) {
			$post_id = isset( $block['attrs']['postId'] ) ? $block['attrs']['postId'] : null;
			if ( empty( $post_id ) || ( ! is_int( $post_id ) && ! $post_id instanceof \WP_Post ) ) {
				return $block_content;
			}
			global $post;
			$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

			if ( ! empty( $block['attrs']['overrideUrl'] ) ) {
				add_filter(
					'post_link',
					function () use ( $block ) {
						return $block['attrs']['overrideUrl'];
					},
					10,
					0
				);
			}
		}
		return $block_content;
	}
}
