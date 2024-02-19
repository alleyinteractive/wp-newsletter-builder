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
$wp_newsletter_builder_read_more_text = $attributes['readMoreText'] ?? __( 'Read More', 'wp-newsletter-builder' );

$wp_newsletter_builder_post_url = get_the_permalink();
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => 'wp-block-button has-text-align-center' ] ) ); ?>>
	<!--[if mso]>
	<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo esc_url( ! empty( $wp_newsletter_builder_post_permalink ) ? $wp_newsletter_builder_post_permalink : '' ); ?>" style="height:48px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="#D62827">
		<w:anchorlock/>
		<center>
	<![endif]-->
	<a class="wp-element-button" href="<?php echo esc_url( ! empty( $wp_newsletter_builder_post_permalink ) ? $wp_newsletter_builder_post_permalink : '' ); ?>">
		<?php echo esc_html( $wp_newsletter_builder_read_more_text ); ?>
	</a>
	<!--[if mso]>
		</center>
	</v:roundrect>
	<![endif]-->
</div>
