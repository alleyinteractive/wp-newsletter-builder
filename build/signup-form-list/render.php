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

$newsletter_builder_title       = $attributes['title'];
$newsletter_builder_frequency   = $attributes['frequency'] ?? '';
$newsletter_builder_description = $attributes['description'] ?? '';
$newsletter_builder_list_id     = $attributes['listId'];
$newsletter_builder_logo_id     = $attributes['logo'] ?? null;
$newsletter_builder_checked     = $attributes['initialChecked'] ?? false;
$newsletter_builder_checked_str = $newsletter_builder_checked ? 'checked="checked"' : '';

if ( empty( $newsletter_builder_title ) || empty( $newsletter_builder_list_id ) ) {
	return;
}
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<label>
		<div class="wp-block-newsletter-builder-signup-form-list__content">
			<?php if ( ! empty( $newsletter_builder_logo_id ) ) : ?>
				<?php echo wp_get_attachment_image( $newsletter_builder_logo_id, 'medium' ); ?>
			<?php endif; ?>
			<h3><?php echo esc_html( $newsletter_builder_title ); ?></h3>
			<?php if ( ! empty( $newsletter_builder_frequency ) ) : ?>
				<div class="wp-block-newsletter-builder-signup-form-list__frequency"><?php echo esc_html( $newsletter_builder_frequency ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $newsletter_builder_description ) ) : ?>
				<div class="wp-block-newsletter-builder-signup-form-list__description"><?php echo esc_html( $newsletter_builder_description ); ?></div>
			<?php endif; ?>
			<input type="checkbox" name="newsletter-builder-checkbox" value="<?php echo esc_attr( $newsletter_builder_list_id ); ?>" <?php echo esc_html( $newsletter_builder_checked_str ); ?> />
		</div>
	</label>
</div>
