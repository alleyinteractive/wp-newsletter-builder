<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @var array{overrideImage?: int, imgSizes?: string} $attributes The array of attributes for this block.
 * @var string $content Rendered block output. ie. <InnerBlocks.Content />.
 * @var WP_Block $block The instance of the WP_Block class that represents the block being rendered.
 *
 * @package wp-newsletter-builder
 */

$wp_newsletter_builder_block_post_id = $block->context['postId'] ?? null;

$wp_newsletter_builder_block_post = get_post( is_numeric( $wp_newsletter_builder_block_post_id ) ? absint( $wp_newsletter_builder_block_post_id ) : null );
if ( empty( $wp_newsletter_builder_block_post ) ) {
	return;
}
$wp_newsletter_builder_img_sizes  = $attributes['imgSizes'] ?? '';
$wp_newsletter_builder_post_image = ! empty( $attributes['overrideImage'] ) ? $attributes['overrideImage'] : ( get_post_thumbnail_id( $wp_newsletter_builder_block_post->ID ) ?: 0 );

$wp_newsletter_builder_post_permalink = (string) get_the_permalink();
?>
<a <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => 'post__image-link' ] ) ); ?> href="<?php echo esc_url( $wp_newsletter_builder_post_permalink ); ?>">
	<?php echo wp_get_attachment_image( $wp_newsletter_builder_post_image, 'full', false, [ 'sizes' => $wp_newsletter_builder_img_sizes ] ); ?>
</a>
