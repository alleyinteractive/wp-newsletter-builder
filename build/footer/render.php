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

$nb_settings         = get_option( 'nb_settings' );
$nb_footer_settings  = is_array( $nb_settings ) ? $nb_settings['footer_settings'] : [];
$nb_facebook_url     = $nb_footer_settings['facebook_url'] ?? '';
$nb_twitter_url      = $nb_footer_settings['twitter_url'] ?? '';
$nb_instagram_url    = $nb_footer_settings['instagram_url'] ?? '';
$nb_youtube_url      = $nb_footer_settings['youtube_url'] ?? '';
$nb_image_id         = $nb_footer_settings['image'] ?? 0;
$nb_address          = $nb_footer_settings['address'] ?? '';
$nb_has_social_links = ! empty( $nb_facebook_url ) || ! empty( $nb_twitter_url ) || ! empty( $nb_instagram_url ) || ! empty( $nb_youtube_url );

$plugin_url = plugins_url( 'wp-newsletter-builder' );
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> align="center">
	<?php if ( $nb_has_social_links ) : ?>
		<div class="wp-block-wp-newsletter-builder-footer__social-links">
			<?php if ( ! empty( $nb_facebook_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link facebook-icon" href="<?php echo esc_url( $nb_facebook_url ); ?>">
						<img
							src="<?php echo esc_url( $plugin_url . '/images/facebook.png' ); ?>"
							alt="Facebook"
							height="26"
							width="26"
						/>
					</a>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $nb_twitter_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link twitter-icon" href="<?php echo esc_url( $nb_twitter_url ); ?>">
						<img
							src="<?php echo esc_url( $plugin_url . '/images/twitter.png' ); ?>"
							alt="Twitter"
							height="26"
							width="26"
						/>
					</a>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $nb_instagram_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link instagram-icon" href="<?php echo esc_url( $nb_instagram_url ); ?>">
						<img
							src="<?php echo esc_url( $plugin_url . '/images/instagram.png' ); ?>"
							alt="Instagram"
							height="26"
							width="26"
						/>
					</a>
				</span>
			<?php endif; ?>
			<?php if ( ! empty( $nb_youtube_url ) ) : ?>
				<span class="wp-block-wp-newsletter-builder-footer__social-links__item">
					<a class="wp-block-wp-newsletter-builder-footer__social-links__link youtube-icon" href="<?php echo esc_url( $nb_youtube_url ); ?>">
						<img
							src="<?php echo esc_url( $plugin_url . '/images/youtube.png' ); ?>"
							alt="YouTube"
							height="26"
							width="26"
						/>
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
			<span><?php esc_html_e( 'Our mailing address is:', 'wp-newsletter-builder' ); ?></span>
			<?php echo esc_html( $nb_address ); ?>
		</div>
	<?php endif; ?>

	<div class="wp-block-wp-newsletter-builder-footer__links">
		<a href="#unsubscribe_preferences"><?php esc_html_e( 'Manage Subscription Preferences', 'wp-newsletter-builder' ); ?></a>
	</div>
</div>
