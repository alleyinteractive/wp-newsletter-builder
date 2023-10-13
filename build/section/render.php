<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 *
 * @package newsletter-builder
 */

$newsletter_builder_heading = $attributes['heading'];

// Check if the content is empty.
if ( empty( trim( wp_strip_all_tags( $content ) ) ) ) {
	return;
}
if ( ! empty( $newsletter_builder_heading ) ) {
	printf( '<h2 class="newsletter-builder-section__heading">%s</h2>', esc_html( $newsletter_builder_heading ) );
}

echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
