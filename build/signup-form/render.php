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

$newsletter_builder_header_text     = $attributes['headerText'];
$newsletter_builder_subheader_text  = $attributes['subheaderText'] ?? '';
$newsletter_builder_disclaimer_text = $attributes['disclaimerText'] ?? '';
$newsletter_builder_button_text     = $attributes['buttonText'] ?? __( 'Subscribe', 'newsletter-builder' );
$newsletter_builder_list_id         = $attributes['listId'] ?? '';
?>
<form <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> data-component="newsletter-builder-signup">
	<div class="wp-block-newsletter-builder-signup-form__header container container--entry-content">
		<div>
			<h2><?php echo wp_kses_post( $newsletter_builder_header_text ); ?></h2>
			<div class="wp-block-newsletter-builder-signup-form__subheader"><?php echo esc_html( $newsletter_builder_subheader_text ); ?></div>
		</div>
		<div>
			<label for="newsletter-builder-email">
				<?php
				printf(
					'%s <span>(%s)</span>',
					esc_html__( 'Email', 'newsletter-builder' ),
					esc_html__( 'required', 'newsletter-builder' )
				);
				?>
				<input type="email" name="newsletter-builder-email" placeholder="Enter your email address" class="wp-block-newsletter-builder-signup-form__email-input" />
			</label>
			<div class="wp-block-newsletter-builder-signup-form__disclaimer">
				<?php echo esc_html( $newsletter_builder_disclaimer_text ); ?>
			</div>

			<div class="wp-block-button is-style-subscribe">
				<button class="wp-block-button__link wp-element-button">
					<?php echo esc_html( $newsletter_builder_button_text ); ?>
				</button>
			</div>

			<div class="wp-block-newsletter-builder-signup-form__response"></div>
		</div>
	</div>
	<?php
	if ( ! empty( $newsletter_builder_list_id ) ) {
		printf( '<input type="hidden" name="newsletter-builder-hidden" value="%s" />', esc_attr( $newsletter_builder_list_id ) );
	} else {
		echo wp_kses_post( $content );
	}
	?>
</form>
