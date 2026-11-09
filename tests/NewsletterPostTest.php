<?php
/**
 * Newsletter post tests.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use WP_Newsletter_Builder\Tests\Test_Case;

/**
 * Tests that newsletters only show posts with an allowed status.
 */
class NewsletterPostTest extends Test_Case {
	/**
	 * Registers the post and post title blocks from their source render files.
	 *
	 * The built blocks may not exist when the tests run, so they aren't registered.
	 */
	protected function setUp(): void {
		parent::setUp();

		$render_file = fn ( string $block_dir ) => function ( array $attributes, string $content, \WP_Block $block ) use ( $block_dir ): string {
			ob_start();
			require dirname( __DIR__ ) . "/blocks/{$block_dir}/render.php";
			return (string) ob_get_clean();
		};

		$registry = \WP_Block_Type_Registry::get_instance();
		foreach ( [ 'wp-newsletter-builder/post', 'wp-newsletter-builder/post-title' ] as $block_name ) {
			if ( $registry->is_registered( $block_name ) ) {
				unregister_block_type( $block_name );
			}
		}

		register_block_type(
			'wp-newsletter-builder/post',
			[
				'attributes'       => [ 'postId' => [ 'type' => 'number' ] ],
				'provides_context' => [ 'postId' => 'postId' ],
				'render_callback'  => $render_file( 'post' ),
			]
		);
		register_block_type(
			'wp-newsletter-builder/post-title',
			[
				'uses_context'    => [ 'postId' ],
				'render_callback' => $render_file( 'post-title' ),
			]
		);
	}

	/**
	 * Renders a post block with a post title block for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return string
	 */
	private function render_post_block( int $post_id ): string {
		return do_blocks(
			sprintf(
				'<!-- wp:wp-newsletter-builder/post {"postId":%d} --><!-- wp:wp-newsletter-builder/post-title /--><!-- /wp:wp-newsletter-builder/post -->',
				$post_id
			)
		);
	}

	public function test_published_posts_are_shown() {
		$post_id = static::factory()->post->create( [ 'post_status' => 'publish' ] );

		$this->assertSame( $post_id, get_newsletter_post( $post_id )?->ID );
		$this->assertSame( $post_id, get_newsletter_post( (string) $post_id )?->ID );
		$this->assertSame( $post_id, get_newsletter_post( get_post( $post_id ) )?->ID );
	}

	public function test_unpublished_posts_are_not_shown() {
		foreach ( [ 'draft', 'pending', 'private', 'future' ] as $status ) {
			$post_id = static::factory()->post->create(
				[
					'post_status' => $status,
					'post_date'   => 'future' === $status ? gmdate( 'Y-m-d H:i:s', time() + DAY_IN_SECONDS ) : '',
				]
			);

			$this->assertNull( get_newsletter_post( $post_id ), "A {$status} post was shown." );
		}
	}

	public function test_missing_posts_are_not_shown() {
		$this->assertNull( get_newsletter_post( PHP_INT_MAX ) );
		$this->assertNull( get_newsletter_post( null ) );
		$this->assertNull( get_newsletter_post( 'not a post' ) );
	}

	public function test_allowed_statuses_can_be_filtered() {
		$post_id = static::factory()->post->create( [ 'post_status' => 'private' ] );

		add_filter( 'wp_newsletter_builder_allowed_post_statuses', fn () => [ 'publish', 'private' ] );

		$this->assertSame( $post_id, get_newsletter_post( $post_id )?->ID );
	}

	public function test_post_blocks_show_published_posts() {
		$post_id = static::factory()->post->create(
			[
				'post_status' => 'publish',
				'post_title'  => 'Published Title',
			]
		);

		$this->assertStringContainsString( 'Published Title', $this->render_post_block( $post_id ) );
	}

	public function test_post_blocks_do_not_show_private_posts() {
		$post_id = static::factory()->post->create(
			[
				'post_status' => 'private',
				'post_title'  => 'Private Title',
			]
		);

		$this->assertStringNotContainsString( 'Private Title', $this->render_post_block( $post_id ) );
	}

	public function test_private_posts_are_not_exported() {
		$post_id       = static::factory()->post->create(
			[
				'post_status' => 'private',
				'post_title'  => 'Private Title',
			]
		);
		$newsletter_id = static::factory()->post->create(
			[
				'post_type'    => 'nb_newsletter',
				'post_status'  => 'draft',
				'post_content' => sprintf(
					'<!-- wp:wp-newsletter-builder/post {"postId":%d} --><!-- wp:wp-newsletter-builder/post-title /--><!-- /wp:wp-newsletter-builder/post -->',
					$post_id
				),
			]
		);
		$this->acting_as( 'contributor' );
		wp_update_post(
			[
				'ID'          => $newsletter_id,
				'post_author' => get_current_user_id(),
			]
		);

		$response = $this->get( rest_url( 'wp-newsletter-builder/v1/export/' . $newsletter_id ) );

		$response->assertOk();
		$this->assertStringNotContainsString( 'Private Title', $response->json( 'html' ) );
	}
}
