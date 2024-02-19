<?php // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 *
 * @package wp-newsletter-builder
 */
$post_id = get_the_ID();
if ( empty( $post_id ) ) {
	return;
}

$image_id = get_post_meta( $post_id, 'nb_newsletter_header_img', true );
if ( empty( $image_id ) || ! is_int( $image_id ) ) {
	return;
}
// TODO: Add a check to see if the image exists.
// TODO: Get proper alt text.
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> role="banner" align="center">
	<?php echo wp_kses_post( wp_get_attachment_image( $image_id, 'full', false ) ); ?>
</div>
