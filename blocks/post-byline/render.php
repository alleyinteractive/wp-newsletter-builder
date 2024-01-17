<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes The array of attributes for this block.
 * @param string   $content    Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block      The instance of the WP_Block class that represents the block being rendered.
 *
 * @package wp-newsletter-builder
 */

use function WP_Newsletter_Builder\get_byline;

$wp_newsletter_builder_block_post = $block->context['postId'] ?? null;

$wp_newsletter_builder_block_post = get_post( $wp_newsletter_builder_block_post );
if ( empty( $wp_newsletter_builder_block_post ) || ! $wp_newsletter_builder_block_post ) {
	return;
}
$wp_newsletter_builder_byline = ! empty( $attributes['overrideByline'] ) ? $attributes['overrideByline'] : get_byline( $wp_newsletter_builder_block_post );

?>
<p class="post__byline">
	<?php
	echo wp_kses_post(
		$wp_newsletter_builder_byline
	);
	?>
</p>
