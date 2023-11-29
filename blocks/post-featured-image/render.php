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

$wp_newsletter_builder_block_post = $block->context['postId'] ?? null;

$wp_newsletter_builder_block_post = get_post( $wp_newsletter_builder_block_post );
if ( empty( $wp_newsletter_builder_block_post ) || ! $wp_newsletter_builder_block_post ) {
	return;
}
$wp_newsletter_builder_post_image = ! empty( $attributes['overrideImage'] ) ? $attributes['overrideImage'] : get_post_thumbnail_id( $wp_newsletter_builder_block_post->ID );

?>
<a <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => 'post__image-link' ] ) ); ?> href="<?php echo esc_url( get_the_permalink() ); ?>">
	<?php echo wp_get_attachment_image( $wp_newsletter_builder_post_image, 'full', false, [ 'sizes' => $wp_newsletter_builder_img_sizes ] ); ?>
</a>
