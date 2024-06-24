<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The template for displaying Newsletters.
 *
 * @package wp-newsletter-builder
 */

if ( function_exists( 'newrelic_disable_autorum' ) ) {
	newrelic_disable_autorum();
}

$wp_newsletter_builder_preview = get_post_meta( get_queried_object_id(), 'nb_newsletter_preview', true );
?>
<!doctype html>

<html  <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="x-apple-disable-message-reformatting">

	<title><?php the_title(); ?></title>
	<?php
	/**
	 * Fires in the head of single_nb_newsletter.php
	 * Used to include inline styles.
	 */
	do_action( 'wp_newsletter_builder_enqueue_styles' );
	?>
</head>

<!--[if mso]>
<body class="mso">
<![endif]-->
<!--[if !mso]><!-->
<body class="main">
<!--<![endif]-->
	<?php
	if ( ! empty( $wp_newsletter_builder_preview ) ) {
		/**
		 * Allow preview markup and text to be added to the newsletter.
		 *
		 * @param string $wp_newsletter_builder_preview The preview text.
		 */
		do_action( 'wp_newsletter_builder_preview_text', $wp_newsletter_builder_preview );
	}
	?>
	<table class="wrapper" cellpadding="0" cellspacing="0" role="presentation">
		<tbody>
			<tr>
				<td>
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<div class="wp-newsletter-builder-container">
							<!--[if mso]><table class="wrapper" align="center" cellpadding="0" cellspacing="0" role="presentation"><tr><td style="width: 600px"><![endif]-->
							<?php the_content(); ?>
							<!--[if mso]></td></tr></table><![endif]-->
						</div>
						<?php
					endwhile; // end of the loop.
					?>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
