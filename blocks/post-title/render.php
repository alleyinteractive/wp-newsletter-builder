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
if ( empty( $wp_newsletter_builder_block_post ) ) {
	return;
}
$wp_newsletter_builder_post_title = ! empty( $attributes['overrideTitle'] ) ? $attributes['overrideTitle'] : $wp_newsletter_builder_block_post->post_title;

$wp_newsletter_builder_smaller_font = $attributes['smallerFont'] ?? false;
$wp_newsletter_builder_title_class  = $wp_newsletter_builder_smaller_font ? 'post__title--small' : '';

$wp_newsletter_builder_post_permalink = (string) get_the_permalink();
?>
<a <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => 'post__title-link' ] ) ); ?> href="<?php echo esc_url( $wp_newsletter_builder_post_permalink ); ?>">
	<h2 class="<?php echo esc_attr( $wp_newsletter_builder_title_class ); ?>">
		<?php if ( ! empty( $wp_newsletter_builder_number ) ) : ?>
			<span class="newsletter-post__number"><?php echo esc_html( $wp_newsletter_builder_number ); ?>.</span>
		<?php endif; ?>
		<?php echo esc_html( $wp_newsletter_builder_post_title ); ?>
	</h2>
</a>
