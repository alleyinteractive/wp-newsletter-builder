<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block         The instance of the WP_Block class that represents the block being rendered.
 *
 * @package wp-newsletter-builder
 */

$wp_newsletter_builder_divider_height = $attributes['elHeight'] ?? null;
?>
<div style="height: <?php echo esc_attr( $wp_newsletter_builder_divider_height ); ?>px;background-color: #000;"></div>
