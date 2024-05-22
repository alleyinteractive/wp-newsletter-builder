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

$nb_settings         = get_option( 'nb_campaign_monitor_settings' );
$nb_footer_settings  = is_array( $nb_settings ) ? $nb_settings['footer_settings'] : [];
$nb_facebook_url     = $nb_footer_settings['facebook_url'] ?? '';
$nb_twitter_url      = $nb_footer_settings['twitter_url'] ?? '';
$nb_instagram_url    = $nb_footer_settings['instagram_url'] ?? '';
$nb_youtube_url      = $nb_footer_settings['youtube_url'] ?? '';
$nb_image_id         = $nb_footer_settings['image'] ?? 0;
$nb_address          = $nb_footer_settings['address'] ?? '';
$nb_has_social_links = ! empty( $nb_facebook_url ) || ! empty( $nb_twitter_url ) || ! empty( $nb_instagram_url ) || ! empty( $nb_youtube_url );
$nb_narrow_separator = ! empty( $attributes['narrow_separator'] );
$nb_separator_class  = $nb_narrow_separator ? '' : 'is-style-wide';
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> align="center">
	<hr class="wp-block-separator has-alpha-channel-opacity <?php echo esc_attr( $nb_separator_class ); ?>" />

	<?php if ( $nb_has_social_links ) : ?>
		<div class="wp-block-wp-newsletter-builder-footer__social-links">
			<?php if ( ! empty( $nb_facebook_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link" href="<?php echo esc_url( $nb_facebook_url ); ?>">
						<img src="/wp-content/plugins/wp-newsletter-builder/images/facebook.png" alt="Facebook" height="26" width="26" />
					</a>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $nb_twitter_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link" href="<?php echo esc_url( $nb_twitter_url ); ?>">
						<img src="/wp-content/plugins/wp-newsletter-builder/images/twitter.png" alt="Twitter" height="26" width="26" />
					</a>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $nb_instagram_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link" href="<?php echo esc_url( $nb_instagram_url ); ?>">
						<img src="/wp-content/plugins/wp-newsletter-builder/images/instagram.png" alt="Instagram" height="26" width="26" />
					</a>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $nb_youtube_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link" href="<?php echo esc_url( $nb_youtube_url ); ?>">
						<img src="/wp-content/plugins/wp-newsletter-builder/images/youtube.png" alt="YouTube" height="26" width="26" />
					</a>
				</span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $nb_image_id ) ) : ?>
		<div class="wp-block-wp-newsletter-builder-footer__logo" align="center">
			<?php echo wp_kses_post( wp_get_attachment_image( $nb_image_id, 'full', false ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $nb_address ) ) : ?>
		<div class="wp-block-wp-newsletter-builder-footer__address">
			<?php echo esc_html( $nb_address ); ?>
		</div>
	<?php endif; ?>

	<div class="wp-block-wp-newsletter-builder-footer__links">
		<preferences><u><?php esc_html_e( 'Preferences', 'wp-newsletter-builder' ); ?></u></preferences>
		&nbsp;|&nbsp;
		<unsubscribe><u><?php esc_html_e( 'Unsubscribe', 'wp-newsletter-builder' ); ?></u></unsubscribe>
	</div>
</div>
