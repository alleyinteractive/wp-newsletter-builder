<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @var array{overrideContent?: string} $attributes The array of attributes for this block.
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
$wp_newsletter_builder_content = $wp_newsletter_builder_block_post->post_content;
preg_match_all( '/<p.*?<\/p>/iU', $wp_newsletter_builder_content, $matches );
$wp_newsletter_builder_paragraphs = $matches[0];
$wp_newsletter_builder_content    = implode( '', array_slice( $wp_newsletter_builder_paragraphs, 0, 2 ) );
$wp_newsletter_builder_content    = preg_replace( '/<a[^>]*?>(.*?)<\/a>/i', '$1', $wp_newsletter_builder_content ) ?? '';
$wp_newsletter_builder_content    = ! empty( $attributes['overrideContent'] ) ? $attributes['overrideContent'] : $wp_newsletter_builder_content;

?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => 'post__content' ] ) ); ?>>
	<?php echo wp_kses_post( $wp_newsletter_builder_content ); ?>
</div>
