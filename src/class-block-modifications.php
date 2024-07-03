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
		add_filter( 'wp_newsletter_builder_register_block', [ $this, 'filter_wp_newsletter_builder_register_block' ], 10, 2 );
	}

	/**
	 * Sets the global post object and overrides for the Newsletter Single Post block.
	 *
	 * @param string|null $block_content The block content.
	 * @param array       $block {.
	 *  @type string $blockName The block name.
	 *  @type array<mixed> $attrs The block attributes.
	 *  @type array<mixed> $innerBlocks The inner blocks.
	 *  @type string $innerHTML The inner HTML.
	 *  @type array<mixed> $innerContent The inner content.
	 * } The parsed block.
	 * @phpstan-param array{blockName: string, attrs: array<mixed>, innerBlocks: array<mixed>, innerHTML: string, innerContent: array<mixed>} $block
	 * @return string|null The block content.
	 */
	public function pre_render_post_block( string|null $block_content, array $block ): string|null {
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

	/**
	 * Filters whether to register a block.
	 *
	 * @param boolean $register Current register status.
	 * @param string $block_name The block name.
	 * @return boolean
	 */
	public function filter_wp_newsletter_builder_register_block( bool $register, string $block_name ): bool {
		$post_type = $_GET['post'] ? get_post_type( $_GET['post'] ) : $_GET['post_type'] ?? 'post';
		if ( 'nb_newsletter' !== $post_type && 'nb_template' !== $post_type) {
			return false;
		}
		return $register;
	}

}
