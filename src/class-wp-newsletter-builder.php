<?php
/**
 * WP_Newsletter_Builder class file
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

/**
 * Example Plugin
 */
class WP_Newsletter_Builder {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_filter( 'template_include', [ $this, 'include_template' ] );
		add_action( 'wp_after_insert_post', [ $this, 'on_newsletter_after_insert_post' ], 10, 4 );
		add_action( 'wp_after_insert_post', [ $this, 'on_after_insert_post' ], 10, 1 );
		add_filter( 'wp_newsletter_builder_html_url', [ $this, 'modify_html_url' ], 10, 2 );
		add_filter( 'pre_get_posts', [ $this, 'modify_query' ], 10, 1 );
		add_action( 'init', [ $this, 'check_for_fieldmanager' ] );
	}

	/**
	 * Register post types
	 */
	public function register_post_types() {
		register_post_type(
			'nb_newsletter',
			[
				'labels'              => [
					'name'          => __( 'Newsletters', 'wp-newsletter-builder' ),
					'singular_name' => __( 'Newsletter', 'wp-newsletter-builder' ),
				],
				'public'              => true,
				'has_archive'         => true,
				'rewrite'             => [ 'slug' => 'nb-newsletters' ],
				'supports'            => [ 'title', 'editor', 'custom-fields' ],
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'template'            => [
					[
						'wp-newsletter-builder/email-settings',
						[
							'lock' => [
								'move'   => true,
								'remove' => true,
							],
						],
					],
				],
				'menu_icon'           => 'dashicons-email-alt2',
			],
		);

		register_post_type(
			'nb_template',
			[
				'labels'              => [
					'name'          => __( 'Templates', 'wp-newsletter-builder' ),
					'singular_name' => __( 'Template', 'wp-newsletter-builder' ),
				],
				'public'              => true,
				'has_archive'         => true,
				'rewrite'             => [ 'slug' => 'nb-templates' ],
				'supports'            => [ 'title', 'editor', 'custom-fields' ],
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'template'            => [
					[
						'wp-newsletter-builder/email-settings',
						[
							'lock' => [
								'move'   => true,
								'remove' => true,
							],
						],
					],
				],
				'menu_icon'           => 'dashicons-admin-customizer',
			],
		);
	}

	/**
	 * Adds the local template file to the template hierarchy.
	 *
	 * @param string $template The existing template.
	 * @return string
	 */
	public function include_template( $template ) {
		global $post;

		$local_path = WP_NEWSLETTER_BUILDER_DIR . '/single-nb_newsletter.php';

		if (
			$post instanceof \WP_Post
			&& is_singular( 'nb_newsletter' )
			&& file_exists( $local_path )
			&& 0 === validate_file( $local_path )
		) {
			$template = $local_path;
		}

		return $template;
	}

	/**
	 * Sends the newsletter when the newsletter post is published.
	 *
	 * @param int      $post_id The post id.
	 * @param \WP_Post $post The post.
	 * @param bool     $update Whether this is an update.
	 * @param \WP_Post $post_before The post before the update.
	 */
	public function on_newsletter_after_insert_post( $post_id, $post, $update, $post_before ): void {
		if ( 'nb_newsletter' !== $post->post_type ) {
			return;
		}
		$new_status = $post->post_status;
		$old_status = $post_before->post_status;
		if ( $new_status === $old_status || 'publish' !== $new_status ) {
			return;
		}
		$this->do_send( $post->ID );
	}

	/**
	 * Sends the breaking newsletter when the post is published.
	 *
	 * @param int $post_id The post id.
	 */
	public function on_after_insert_post( $post_id ): void {
		$post = get_post( $post_id );
		if ( 'post' !== $post->post_type ) {
			return;
		}
		if ( 'publish' !== $post->post_status ) {
			return;
		}
		$should_send = get_post_meta( $post->ID, 'nb_breaking_should_send', true );
		if ( ! $should_send ) {
			return;
		}

		$nb_newsletter_subject = get_post_meta( $post->ID, 'nb_breaking_subject', true );
		if ( empty( $nb_newsletter_subject ) ) {
			$nb_newsletter_subject = $post->post_title;
		}

		$nb_newsletter_preview = get_post_meta( $post->ID, 'nb_breaking_preview', true );
		if ( empty( $nb_newsletter_preview ) ) {
			$nb_newsletter_preview = $post->post_excerpt;
		}

		// Publish the post, which should kick off the other transition listener to send the email.
		$breaking_post_id = wp_insert_post(
			[
				'post_title'   => "Breaking News {$post->ID}",
				'post_content' => get_post_meta( $post->ID, 'nb_breaking_content', true ),
				'post_status'  => 'publish',
				'post_type'    => 'nb_newsletter',
				'meta_input'   => [
					'nb_newsletter_email_type' => get_post_meta( $post->ID, 'nb_breaking_email_type', true ),
					'nb_newsletter_template'   => get_post_meta( $post->ID, 'nb_breaking_template', true ),
					'nb_newsletter_from_name'  => get_post_meta( $post->ID, 'nb_breaking_from_name', true ),
					'nb_newsletter_subject'    => $nb_newsletter_subject,
					'nb_newsletter_preview'    => $nb_newsletter_preview,
					'nb_newsletter_list'       => get_post_meta( $post->ID, 'nb_breaking_list', true ),
					'nb_newsletter_header_img' => get_post_meta( $post->ID, 'nb_breaking_header_img', true ),
				],
			]
		);
		if ( is_wp_error( $breaking_post_id ) ) {
			return;
		}

		$sent_emails = get_post_meta( $post->ID, 'nb_newsletter_sent_breaking_post_id', true ) ?? [];
		if ( ! is_array( $sent_emails ) ) {
			$sent_emails = [ $sent_emails ];
		}
		$sent_emails[] = $breaking_post_id;
		update_post_meta( $post->ID, 'nb_newsletter_sent_breaking_post_id', $sent_emails );
		delete_post_meta( $post->ID, 'nb_breaking_subject' );
		delete_post_meta( $post->ID, 'nb_breaking_preview' );
		delete_post_meta( $post->ID, 'nb_breaking_list' );
		delete_post_meta( $post->ID, 'nb_breaking_should_send' );

		\wp_update_post(
			[
				'ID'          => $breaking_post_id,
				'post_status' => 'publish',
			]
		);
	}

	/**
	 * Override the HTML URL for the newsletter - return a public url if local, add auth if staging.
	 *
	 * @param string $url The existing url.
	 * @return string
	 */
	public function modify_html_url( $url ): string {
		$url      = str_replace(
			[
				'https://www.',
				'http://www.',
			],
			[
				'https://',
				'http://',
			],
			$url
		);
		$settings = get_option( 'nb_campaign_monitor_settings' );
		if ( ! empty( $settings['dev_settings']['static_preview_url'] ) ) {
			return $settings['dev_settings']['static_preview_url'];
		}
		if (
			! empty( $settings['dev_settings']['basic_auth_username'] ) && ! empty( $settings['dev_settings']['basic_auth_password'] )
		) {
			return str_replace(
				'://',
				sprintf( '://%s:%s@', $settings['dev_settings']['basic_auth_username'], $settings['dev_settings']['basic_auth_password'] ),
				$url
			);
		}
		return $url;
	}

	/**
	 * Sends the newsletter.
	 *
	 * @param int $post_id The post id.
	 * @return void
	 */
	public function do_send( $post_id ) {
		$lists = get_post_meta( $post_id, 'nb_newsletter_list', true );
		if ( ! is_array( $lists ) ) {
			$lists = [ $lists ];
		}
		if ( empty( $lists ) ) {
			return;
		}
		$campaign_id = get_post_meta( $post_id, 'nb_newsletter_campaign_id', true );
		global $newsletter_builder_email_provider;
		$result = $newsletter_builder_email_provider->create_campaign( $post_id, $lists );
		if ( 201 === $result['http_status_code'] ) {
			update_post_meta( $post_id, 'nb_newsletter_campaign_id', $result['response'] );
			$send_result = $newsletter_builder_email_provider->send_campaign( $result['response'] );
			update_post_meta( $post_id, 'nb_newsletter_send_result', $send_result );
		}
		update_post_meta( $post_id, 'nb_newsletter_campaign_result', $result );
	}

	/**
	 * Modifies the query so we can view draft newsletters as well as published ones.
	 *
	 * @param \WP_Query $query The query.
	 * @return \WP_Query
	 */
	public function modify_query( $query ) {
		if (
			$query->is_main_query()
			&& isset( $query->query_vars['post_type'] )
			&& 'nb_newsletter' === $query->query_vars['post_type']
		) {
			$query->query['post_status']      = [ 'publish', 'draft' ];
			$query->query_vars['post_status'] = [ 'publish', 'draft' ];
		}
		return $query;
	}

	/**
	 * Displays an error message if Fieldmanager is not installed.
	 *
	 * @return void
	 */
	public function fieldmanager_not_found_error() {
		?>
		<div class="error notice">
			<p>
			<?php
			printf(
				/* translators: %1$s: Opening a tag to Fieldmanager plugin, %2$s closing a tag. */
				esc_html__( 'The Newsletter Builder plugin requires the %1$sWordPress Fieldmanager plugin%2$s', 'wp-newsletter-builder' ),
				'<a href="https://github.com/alleyinteractive/wordpress-fieldmanager">',
				'</a>',
			);
			?>
			</p>
		</div>
		<?php
	}

	/**
	 * Check if WordPress Fieldmanager plugin is installed.
	 *
	 * @return void
	 */
	public function check_for_fieldmanager() {
		if ( ! defined( 'FM_VERSION' ) ) {
			add_action( 'admin_notices', [ $this, 'fieldmanager_not_found_error' ] );
			return;
		}
	}
}
