<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
 *
 * @package wp-newsletter-builder
 */

use function WP_Newsletter_Builder\get_byline;

$wp_newsletter_builder_block_post = get_post( $attributes['postId'] );
if ( empty( $attributes['postId'] ) || ! $wp_newsletter_builder_block_post ) {
	return;
}
$wp_newsletter_builder_post_title = ! empty( $attributes['overrideTitle'] ) ? $attributes['overrideTitle'] : $wp_newsletter_builder_block_post->post_title;
$wp_newsletter_builder_post_image = ! empty( $attributes['overrideImage'] ) ? $attributes['overrideImage'] : get_post_thumbnail_id( $wp_newsletter_builder_block_post->ID );
$wp_newsletter_builder_excerpt    = ! empty( $attributes['overrideExcerpt'] ) ? $attributes['overrideExcerpt'] : $wp_newsletter_builder_block_post->post_excerpt;
$wp_newsletter_builder_content    = $wp_newsletter_builder_block_post->post_content;
preg_match_all( '/<p.*?<\/p>/iU', $wp_newsletter_builder_content, $matches );
$wp_newsletter_builder_paragraphs   = $matches[0];
$wp_newsletter_builder_content      = implode( '', array_slice( $wp_newsletter_builder_paragraphs, 0, 2 ) );
$wp_newsletter_builder_content      = preg_replace( '/<a[^>]*?>(.*?)<\/a>/i', '$1', $wp_newsletter_builder_content );
$wp_newsletter_builder_content      = ! empty( $attributes['overrideContent'] ) ? $attributes['overrideContent'] : $wp_newsletter_builder_content;
$wp_newsletter_builder_byline       = ! empty( $attributes['overrideByline'] ) ? $attributes['overrideByline'] : get_byline( $wp_newsletter_builder_block_post );
$wp_newsletter_builder_order        = ! empty( $attributes['order'] ) ? $attributes['order'] : [
	'title',
	'byline',
	'image',
	'excerpt',
	'content',
	'cta',
];
$wp_newsletter_builder_showimage    = $attributes['showImage'] ?? true;
$wp_newsletter_builder_showexcerpt  = $attributes['showExcerpt'] ?? true;
$wp_newsletter_builder_showcontent  = $attributes['showContent'] ?? true;
$wp_newsletter_builder_showbyline   = $attributes['showByline'] ?? true;
$wp_newsletter_builder_showcta      = $attributes['showCta'] ?? true;
$wp_newsletter_builder_showprobadge = $attributes['showProBadge'] ?? true;
$wp_newsletter_builder_number       = $attributes['number'] ?? null;
$wp_newsletter_builder_smaller_font = $attributes['smallerFont'] ?? false;
$wp_newsletter_builder_title_class  = $wp_newsletter_builder_smaller_font ? 'post__title--small' : '';
$wp_newsletter_builder_img_sizes    = $attributes['imgSizes'] ?? '';
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php foreach ( $wp_newsletter_builder_order as $wp_newsletter_builder_item ) : ?>
		<?php
		switch ( $wp_newsletter_builder_item ) {
			case 'title':
				?>
					<a class="post__title-link" href="<?php echo esc_url( get_permalink( $wp_newsletter_builder_block_post->ID ) ); ?>">
						<h2 class="<?php echo esc_attr( $wp_newsletter_builder_title_class ); ?>">
							<?php if ( ! empty( $wp_newsletter_builder_number ) ) : ?>
								<span class="newsletter-post__number"><?php echo esc_html( $wp_newsletter_builder_number ); ?>.</span>
							<?php endif; ?>
							<?php echo esc_html( $wp_newsletter_builder_post_title ); ?>
						</h2>
					</a>
				<?php
				break;
			case 'byline':
				?>
				<?php
				if ( $wp_newsletter_builder_showbyline && ! empty( $wp_newsletter_builder_byline ) ) :
					?>
					<p class="post__byline">
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %s: byline */
								__( 'By %s', 'wp-newsletter-builder' ),
								$wp_newsletter_builder_byline
							)
						);
						?>
					</p>
					<?php
				endif;
				break;
			case 'image':
				?>
				<?php if ( $wp_newsletter_builder_showimage && ! empty( $wp_newsletter_builder_post_image ) ) : ?>
					<a class="post__image-link" href="<?php echo esc_url( get_permalink( $wp_newsletter_builder_block_post->ID ) ); ?>">
						<?php echo wp_get_attachment_image( $wp_newsletter_builder_post_image, 'full', false, [ 'sizes' => $wp_newsletter_builder_img_sizes ] ); ?>
					</a>
					<?php
				endif;
				break;
			case 'excerpt':
				?>
				<?php if ( $wp_newsletter_builder_showexcerpt && ! empty( $wp_newsletter_builder_excerpt ) ) : ?>
					<div class="post__dek">
						<p><?php echo esc_html( $wp_newsletter_builder_excerpt ); ?></p>
					</div>
					<?php
				endif;
				break;
			case 'content':
				?>
				<?php if ( $wp_newsletter_builder_showcontent && ! empty( $wp_newsletter_builder_content ) ) : ?>
					<?php echo wp_kses_post( $wp_newsletter_builder_content ); ?>
					<?php
				endif;
				break;
			case 'cta':
				?>
				<?php if ( $wp_newsletter_builder_showcta ) : ?>
					<div class="wp-block-button has-text-align-center">
						<!--[if mso]>
						<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo esc_url( get_permalink( $wp_newsletter_builder_block_post->ID ) ); ?>" style="height:48px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="#D62827">
							<w:anchorlock/>
							<center>
						<![endif]-->
						<a class="wp-element-button" href="<?php echo esc_url( get_permalink( $wp_newsletter_builder_block_post->ID ) ); ?>">
							<?php esc_html_e( 'Read More', 'wp-newsletter-builder' ); ?>
						</a>
						<!--[if mso]>
							</center>
						</v:roundrect>
						<![endif]-->
					</div>
					<?php
				endif;
				break;
		}
		?>
		<?php endforeach; ?>
</div>
