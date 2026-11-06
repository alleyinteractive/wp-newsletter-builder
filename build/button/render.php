<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @var array{textColor?: string, bgColor?: string, radius?: string, btnWidth?: string} $attributes The array of attributes for this block.
 * @var string $content Rendered block output. ie. <InnerBlocks.Content />.
 * @var WP_Block $block The instance of the WP_Block class that represents the block being rendered.
 *
 * @package wp-newsletter-builder
 */

$wp_newsletter_builder_button_color    = $attributes['textColor'] ?? '';
$wp_newsletter_builder_button_bg_color = $attributes['bgColor'] ?? '';
$wp_newsletter_builder_button_radius   = $attributes['radius'] ?? '';
$wp_newsletter_builder_button_width    = $attributes['btnWidth'] ?? '';
?>

<div style="margin: 0 auto; width: <?php echo esc_attr( $wp_newsletter_builder_button_width ); ?>; color: <?php echo esc_attr( $wp_newsletter_builder_button_color ); ?>; background-color: <?php echo esc_attr( $wp_newsletter_builder_button_bg_color ); ?>; border-radius: <?php echo esc_attr( $wp_newsletter_builder_button_radius ); ?>">
	<?php echo wp_kses_post( $content ); ?>
</div>
