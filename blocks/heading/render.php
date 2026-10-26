<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @var array{elColor?: string} $attributes The array of attributes for this block.
 * @var string $content Rendered block output. ie. <InnerBlocks.Content />.
 * @var WP_Block $block The instance of the WP_Block class that represents the block being rendered.
 *
 * @package wp-newsletter-builder
 */

$wp_newsletter_builder_heading_color = $attributes['elColor'] ?? '';
?>
<div style="color: <?php echo esc_attr( $wp_newsletter_builder_heading_color ); ?>;" ?>
	<?php echo wp_kses_post( $content ); ?>
</div>
