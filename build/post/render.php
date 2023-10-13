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

use function Newsletter_Builder\get_byline;

$newsletter_builder_block_post = get_post( $attributes['postId'] );
if ( empty( $attributes['postId'] ) || ! $newsletter_builder_block_post ) {
	return;
}
$newsletter_builder_post_title = ! empty( $attributes['overrideTitle'] ) ? $attributes['overrideTitle'] : $newsletter_builder_block_post->post_title;
$newsletter_builder_post_image = ! empty( $attributes['overrideImage'] ) ? $attributes['overrideImage'] : get_post_thumbnail_id( $newsletter_builder_block_post->ID );
$newsletter_builder_excerpt    = ! empty( $attributes['overrideExcerpt'] ) ? $attributes['overrideExcerpt'] : $newsletter_builder_block_post->post_excerpt;
$newsletter_builder_content    = $newsletter_builder_block_post->post_content;
preg_match_all( '/<p.*?<\/p>/iU', $newsletter_builder_content, $matches );
$newsletter_builder_paragraphs   = $matches[0];
$newsletter_builder_content      = implode( '', array_slice( $newsletter_builder_paragraphs, 0, 2 ) );
$newsletter_builder_content      = preg_replace( '/<a[^>]*?>(.*?)<\/a>/i', '$1', $newsletter_builder_content );
$newsletter_builder_content      = ! empty( $attributes['overrideContent'] ) ? $attributes['overrideContent'] : $newsletter_builder_content;
$newsletter_builder_byline       = ! empty( $attributes['overrideByline'] ) ? $attributes['overrideByline'] : get_byline( $newsletter_builder_block_post );
$newsletter_builder_order        = ! empty( $attributes['order'] ) ? $attributes['order'] : [
	'title',
	'byline',
	'image',
	'excerpt',
	'content',
	'cta',
];
$newsletter_builder_showimage    = $attributes['showImage'] ?? true;
$newsletter_builder_showexcerpt  = $attributes['showExcerpt'] ?? true;
$newsletter_builder_showcontent  = $attributes['showContent'] ?? true;
$newsletter_builder_showbyline   = $attributes['showByline'] ?? true;
$newsletter_builder_showcta      = $attributes['showCta'] ?? true;
$newsletter_builder_showprobadge = $attributes['showProBadge'] ?? true;
$newsletter_builder_number       = $attributes['number'] ?? null;
$newsletter_builder_smaller_font = $attributes['smallerFont'] ?? false;
$newsletter_builder_title_class  = $newsletter_builder_smaller_font ? 'post__title--small' : '';
$newsletter_builder_img_sizes    = $attributes['imgSizes'] ?? '';
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php foreach ( $newsletter_builder_order as $newsletter_builder_item ) : ?>
		<?php
		switch ( $newsletter_builder_item ) {
			case 'title':
				?>
					<a class="post__title-link" href="<?php echo esc_url( get_permalink( $newsletter_builder_block_post->ID ) ); ?>">
						<h2 class="<?php echo esc_attr( $newsletter_builder_title_class ); ?>">
							<?php if ( ! empty( $newsletter_builder_number ) ) : ?>
								<span class="newsletter-post__number"><?php echo esc_html( $newsletter_builder_number ); ?>.</span>
							<?php endif; ?>
							<?php echo esc_html( $newsletter_builder_post_title ); ?>
						</h2>
					</a>
				<?php
				break;
			case 'byline':
				?>
				<?php
				if ( $newsletter_builder_showbyline && ! empty( $newsletter_builder_byline ) ) :
					?>
					<p class="post__byline">
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %s: byline */
								__( 'By %s', 'newsletter-builder' ),
								$newsletter_builder_byline
							)
						);
						?>
					</p>
					<?php
				endif;
				break;
			case 'image':
				?>
				<?php if ( $newsletter_builder_showimage && ! empty( $newsletter_builder_post_image ) ) : ?>
					<a class="post__image-link" href="<?php echo esc_url( get_permalink( $newsletter_builder_block_post->ID ) ); ?>">
						<?php echo wp_get_attachment_image( $newsletter_builder_post_image, 'full', false, [ 'sizes' => $newsletter_builder_img_sizes ] ); ?>
					</a>
					<?php
				endif;
				break;
			case 'excerpt':
				?>
				<?php if ( $newsletter_builder_showexcerpt && ! empty( $newsletter_builder_excerpt ) ) : ?>
					<div class="post__dek">
						<p><?php echo esc_html( $newsletter_builder_excerpt ); ?></p>
					</div>
					<?php
				endif;
				break;
			case 'content':
				?>
				<?php if ( $newsletter_builder_showcontent && ! empty( $newsletter_builder_content ) ) : ?>
					<?php echo wp_kses_post( $newsletter_builder_content ); ?>
					<?php
				endif;
				break;
			case 'cta':
				?>
				<?php if ( $newsletter_builder_showcta ) : ?>
					<div class="wp-block-button has-text-align-center">
						<!--[if mso]>
						<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo esc_url( get_permalink( $newsletter_builder_block_post->ID ) ); ?>" style="height:48px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="#D62827">
							<w:anchorlock/>
							<center>
						<![endif]-->
						<a class="wp-element-button" href="<?php echo esc_url( get_permalink( $newsletter_builder_block_post->ID ) ); ?>">
							<?php esc_html_e( 'Read More', 'newsletter-builder' ); ?>
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
