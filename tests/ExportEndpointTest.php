<?php
/**
 * Export endpoint tests.
 *
 * @package wp-newsletter-builder
 */

namespace WP_Newsletter_Builder;

use WP_Newsletter_Builder\Tests\Test_Case;

/**
 * Tests for the newsletter HTML export endpoint.
 */
class ExportEndpointTest extends Test_Case {
	/**
	 * Creates a newsletter.
	 *
	 * @param string $status The post status.
	 * @return int The post ID.
	 */
	private function create_newsletter( string $status ): int {
		return static::factory()->post->create(
			[
				'post_type'    => 'nb_newsletter',
				'post_status'  => $status,
				'post_title'   => 'My Newsletter',
				'post_content' => '<p>Hello <a href="/about/">about us</a> <img src="/logo.png" alt=""></p>',
			]
		);
	}

	public function test_editor_can_export_a_draft() {
		$post_id = $this->create_newsletter( 'draft' );
		$this->acting_as( 'editor' );

		$response = $this->get( rest_url( 'wp-newsletter-builder/v1/export/' . $post_id ) );

		$response->assertOk();
		$response->assertJsonPath( 'filename', 'my-newsletter.html' );

		$html = $response->json( 'html' );
		$this->assertStringContainsStringIgnoringCase( '<!doctype html>', $html );
		$this->assertStringContainsString( 'wp-newsletter-builder-container', $html );
		$this->assertStringContainsString( '<title>My Newsletter</title>', $html );
		$this->assertStringContainsString( 'Hello', $html );
		$this->assertStringContainsString( 'href="' . home_url( '/about/' ) . '"', $html );
		$this->assertStringContainsString( 'src="' . home_url( '/logo.png' ) . '"', $html );
	}

	public function test_editor_can_export_a_published_newsletter() {
		$post_id = $this->create_newsletter( 'publish' );
		$this->acting_as( 'editor' );

		$response = $this->get( rest_url( 'wp-newsletter-builder/v1/export/' . $post_id ) );

		$response->assertOk();
		$this->assertStringContainsString( 'Hello', $response->json( 'html' ) );
	}

	public function test_user_who_cannot_edit_cannot_export() {
		$post_id = $this->create_newsletter( 'publish' );

		$this->get( rest_url( 'wp-newsletter-builder/v1/export/' . $post_id ) )->assertUnauthorized();

		$this->acting_as( 'subscriber' );
		$this->get( rest_url( 'wp-newsletter-builder/v1/export/' . $post_id ) )->assertForbidden();
	}

	public function test_only_newsletters_can_be_exported() {
		$post_id = static::factory()->post->create();
		$this->acting_as( 'editor' );

		$this->get( rest_url( 'wp-newsletter-builder/v1/export/' . $post_id ) )->assertNotFound();
	}
}
