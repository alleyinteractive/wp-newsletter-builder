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
	 * @param string $block_content The block content.
	 * @param array  $block {
	 *  @type string $blockName The block name.
	 *  @type array<mixed> $attrs The block attributes.
	 *  @type array<mixed> $innerBlocks The inner blocks.
	 *  @type string $innerHTML The inner HTML.
	 *  @type array<mixed> $innerContent The inner content.
	 * } The parsed block.
	 * @phpstan-param array{blockName: string, attrs: array<mixed>, innerBlocks: array<mixed>, innerHTML: string, innerContent: array<mixed>} $block
	 * @return string The block content.
	 */
	public function pre_render_post_block( $block_content, $block ) {
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
