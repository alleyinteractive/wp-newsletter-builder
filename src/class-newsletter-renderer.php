<?php
/**
 * Newsletter_Renderer class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use WP_Post;
use WP_Query;

/**
 * Renders a newsletter to email-ready HTML.
 */
class Newsletter_Renderer {
	/**
	 * Post statuses that can be rendered.
	 *
	 * @var array<string>
	 */
	private const RENDERABLE_STATUSES = [ 'publish', 'draft', 'pending', 'future', 'private' ];

	/**
	 * Renders a newsletter for export.
	 *
	 * The newsletter is rendered through the same template as the front-end view,
	 * then its CSS is inlined and root-relative URLs are made absolute so the HTML
	 * can be pasted into another email tool.
	 *
	 * @param int $post_id The nb_newsletter post ID.
	 * @return string|false The HTML, or false if the newsletter couldn't be rendered.
	 */
	public function render_for_export( int $post_id ): string|false {
		$html = $this->render_template( $post_id );
		if ( false === $html ) {
			return false;
		}

		$html = ( new CssToInlineStyles() )->convert( $html, '' );

		return $this->make_urls_absolute( $html );
	}

	/**
	 * Gets a file name for a newsletter's exported HTML.
	 *
	 * @param int $post_id The nb_newsletter post ID.
	 * @return string
	 */
	public function get_export_filename( int $post_id ): string {
		$post = get_post( $post_id );
		$name = $post instanceof WP_Post ? sanitize_title( $post->post_name ? $post->post_name : $post->post_title ) : '';

		if ( '' === $name ) {
			$name = 'newsletter-' . $post_id;
		}

		return sanitize_file_name( $name . '.html' );
	}

	/**
	 * Renders a newsletter through the front-end view template.
	 *
	 * Renders as a logged-out user, as an email provider would see it.
	 *
	 * @param int $post_id The nb_newsletter post ID.
	 * @return string|false The HTML, or false if the newsletter couldn't be rendered.
	 */
	public function render_template( int $post_id ): string|false {
		global $wp_query, $post;

		$template = WP_NEWSLETTER_BUILDER_DIR . '/single-nb_newsletter.php';
		if ( 'nb_newsletter' !== get_post_type( $post_id ) || ! file_exists( $template ) ) {
			return false;
		}

		// Back up globals.
		$old_wp_query     = $wp_query;
		$old_post         = $post;
		$old_current_user = get_current_user_id();

		wp_set_current_user( 0 );

		$wp_query = new WP_Query( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			[
				'p'           => $post_id,
				'post_type'   => 'nb_newsletter',
				'post_status' => self::RENDERABLE_STATUSES,
			]
		);

		// As on the front end, the template uses the post before the loop starts.
		$post = $wp_query->post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		ob_start();

		try {
			load_template( $template, false );
			$content = (string) ob_get_clean();
		} catch ( \Throwable $e ) {
			ob_end_clean();
			$content = false;
		} finally {
			$wp_query = $old_wp_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$post     = $old_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			if ( $post instanceof WP_Post ) {
				setup_postdata( $post );
			}
			wp_set_current_user( $old_current_user );
		}

		// Sanity check that the newsletter actually rendered.
		if ( false === $content || ! str_contains( $content, 'wp-newsletter-builder-container' ) ) {
			return false;
		}

		return $content;
	}

	/**
	 * Converts root-relative src and href attributes to absolute URLs.
	 *
	 * @param string $html The HTML.
	 * @return string
	 */
	private function make_urls_absolute( string $html ): string {
		$result = preg_replace_callback(
			'#\b(src|href)=(["\'])/(?!/)#i',
			fn( array $matches ) => $matches[1] . '=' . $matches[2] . trailingslashit( home_url() ),
			$html
		);

		return is_string( $result ) ? $result : $html;
	}
}
