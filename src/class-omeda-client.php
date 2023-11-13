<?php
/**
 * Class file for WP Omeda API calls.
 *
 * @package nr
 */

namespace WP_Newsletter_Builder;

use WP_Newsletter_Builder\Instance;

use WP_Error;
use WP_REST_Request;

/**
 * Class for WP Omeda API calls.
 *
 * @see https://training.omeda.com/knowledge-base/api-overview/
 */
class Omeda_Client {
	use Instance;

	/**
	 * The Omeda API user.
	 *
	 * @var string
	 */
	private string $api_user = '';

	/**
	 * The Omeda app ID.
	 *
	 * @var string
	 */
	private string $app_id = '';

	/**
	 * Whether debug mode is enabled or not.
	 *
	 * @var boolean
	 */
	private bool $debug = false;

	/**
	 * The base production URL for the API.
	 *
	 * @var string
	 */
	private string $base_url = 'https://ows.omeda.com';

	/**
	 * The staging URL for the API.
	 *
	 * @var string
	 */
	private string $staging_url = 'https://ows.omedastaging.com';

	/**
	 * The Omeda brand ID.
	 *
	 * @var string
	 */
	private string $brand = '';

	/**
	 * The Omeda input ID.
	 *
	 * @var string
	 */
	private string $input_id = '';

	/**
	 * The license key for accessing Omeda APIs.
	 *
	 * @var string
	 */
	private string $license_key = '';

	/**
	 * The WP Omeda namespace.
	 *
	 * Used in customer object as ExternalCustomerIdNamespace.
	 *
	 * @var string
	 */
	private string $namespace = '';

	/**
	 * The Omeda API mailbox.
	 *
	 * @var string
	 */
	private string $mailbox = '';

	/**
	 * Reply to email address.
	 *
	 * @var string
	 */
	private string $reply_to = '';

	/**
	 * From name.
	 *
	 * @var string
	 */
	private string $from_name = '';

	/**
	 * The abbreviation of the client name.
	 *
	 * @var string
	 */
	private string $client_abbr = '';

	/**
	 * Initialize and set values for properties.
	 */
	public function __construct( $config ) {

		// Set options.
		$this->set_license_key( $config[ 'license_key' ] ?? $this->license_key );

		// Set properties.
		$this->set_api_user( $config[ 'user' ] );
		$this->set_app_id( $config[ 'app_id' ] );
		$this->set_brand( $config[ 'brand' ] );
		$this->set_client_abbr( $config[ 'client_abbr' ] );
		$this->set_from_name( $config[ 'from_name' ] );
		$this->set_input_id( $config[ 'input_id' ] );
		$this->set_mailbox( $config[ 'mailbox' ] );
		$this->set_namespace( $config[ 'namespace' ] );
		$this->set_reply_to( $config[ 'reply_to' ] );
	}

	/**
	 * Returns the API user.
	 *
	 * @return string The API user.
	 */
	public function get_api_user(): string {
		return $this->api_user;
	}

	/**
	 * Set the API user.
	 *
	 * @param string $api_user The API user.
	 *
	 * @return Omeda_Client Returns this API object for method chaining.
	 */
	public function set_api_user( string $api_user ): Omeda_Client {
		$this->api_user = $api_user;

		return $this;
	}

	/**
	 * Get the app ID.
	 *
	 * This method retrieves the app ID associated with the API instance.
	 *
	 * @return string The app ID.
	 */
	public function get_app_id(): string {
		return $this->app_id;
	}

	/**
	 * Set the app ID for the API.
	 *
	 * @param string $app_id The app ID to be set.
	 *
	 * @return Omeda_Client Returns an instance of the API class.
	 */
	public function set_app_id( string $app_id ): Omeda_Client {
		$this->app_id = $app_id;

		return $this;
	}

	/**
	 * Get the brand name of the API.
	 *
	 * @return string The brand name of the API.
	 */
	public function get_brand(): string {
		return $this->brand;
	}

	/**
	 * Set the brand for the API.
	 *
	 * @param string $brand The brand name to be set.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_brand( string $brand ): Omeda_Client {
		$this->brand = $brand;

		return $this;
	}

	/**
	 * Get the license key associated with the API instance.
	 *
	 * @return string Returns the license key as a string.
	 */
	public function get_license_key(): string {
		return $this->license_key;
	}

	/**
	 * Set the license key for the API.
	 *
	 * @param string $license_key The license key to set.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_license_key( string $license_key ): Omeda_Client {
		$this->license_key = $license_key;

		return $this;
	}

	/**
	 * Check if debugging mode is enabled.
	 *
	 * @return bool True if debugging mode is enabled, false otherwise.
	 */
	public function is_debug(): bool {
		return $this->debug;
	}

	/**
	 * Sets the debug mode for the API.
	 *
	 * @param bool $debug The debug mode flag.
	 *
	 * @return Omeda_Client The API instance.
	 */
	public function set_debug( bool $debug ): Omeda_Client {
		$this->debug = $debug;

		return $this;
	}

	/**
	 * Get the endpoint URL for the given API and endpoint.
	 *
	 * @param string $api The API name.
	 * @param string $endpoint The endpoint name.
	 *
	 * @return string The endpoint URL.
	 */
	public function get_endpoint( string $api, string $endpoint ): string {
		// Set the base URL depending on whether we're using staging or not.
		$url = $this->is_use_staging() ? $this->get_staging_url() : $this->get_base_url();

		// Set type based on whether we are calling client or brand APIs.
		$type = 'client' === $api ? $this->get_client_abbr() : $this->get_brand();

		return "{$url}/webservices/rest/{$api}/{$type}/{$endpoint}/*";
	}

	/**
	 * Get the staging URL of the API.
	 *
	 * @return string Returns the staging URL of the API.
	 */
	public function get_staging_url(): string {
		return $this->staging_url;
	}

	/**
	 * Set the staging URL for the API.
	 *
	 * @param string $staging_url The staging URL of the API.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_staging_url( string $staging_url ): Omeda_Client {
		$this->staging_url = $staging_url;

		return $this;
	}

	/**
	 * Retrieves the base URL used by the API.
	 *
	 * @return string The base URL.
	 */
	public function get_base_url(): string {
		return $this->base_url;
	}

	/**
	 * Set the base URL for the API.
	 *
	 * @param string $base_url The base URL of the API.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_base_url( string $base_url ): Omeda_Client {
		$this->base_url = $base_url;

		return $this;
	}

	/**
	 * Get the client abbreviation.
	 *
	 * @return string The client abbreviation associated with the API.
	 */
	public function get_client_abbr(): string {
		return $this->client_abbr;
	}

	/**
	 * Set the client abbreviation for the API.
	 *
	 * @param string $client_abbr The client abbreviation to set.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_client_abbr( string $client_abbr ): Omeda_Client {
		$this->client_abbr = $client_abbr;

		return $this;
	}

	/**
	 * Get the input ID.
	 *
	 * @return string The input ID.
	 */
	public function get_input_id(): string {
		return $this->input_id;
	}

	/**
	 * Set the input ID for the API.
	 *
	 * @param string $input_id The ID of the input.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_input_id( string $input_id ): Omeda_Client {
		$this->input_id = $input_id;

		return $this;
	}

	/**
	 * Get the namespace of the API.
	 *
	 * @return string The namespace of the API.
	 */
	public function get_namespace(): string {
		return $this->namespace;
	}

	/**
	 * Set the namespace for the API.
	 *
	 * @param string $namespace The namespace of the API.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_namespace( string $namespace ): Omeda_Client {
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * Get the mailbox value.
	 *
	 * @return string The mailbox value.
	 */
	public function get_mailbox(): string {
		return $this->mailbox;
	}

	/**
	 * Set the mailbox for the API.
	 *
	 * @param string $mailbox The mailbox to set.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_mailbox( string $mailbox ): Omeda_Client {
		$this->mailbox = $mailbox;

		return $this;
	}

	/**
	 * Get the reply-to address.
	 *
	 * Retrieves the reply-to address that has been set.
	 *
	 * @return string The reply-to address.
	 */
	public function get_reply_to(): string {
		return $this->reply_to;
	}

	/**
	 * Set the email address to reply to.
	 *
	 * @param string $reply_to The email address to reply to.
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_reply_to( string $reply_to ): Omeda_Client {
		$this->reply_to = $reply_to;

		return $this;
	}

	/**
	 * Get the value of the 'from_name' property.
	 *
	 * @return string The value of the 'from_name' property.
	 */
	public function get_from_name(): string {
		return $this->from_name;
	}

	/**
	 * Set the value of the "from_name" property.
	 *
	 * @param string $from_name The value to set as the "from_name".
	 *
	 * @return Omeda_Client Returns the instance of the API class for method chaining.
	 */
	public function set_from_name( string $from_name ): Omeda_Client {
		$this->from_name = $from_name;

		return $this;
	}

	/**
	 * Checks if all the required properties are set.
	 *
	 * @return bool Returns true if all the properties are set, false otherwise.
	 */
	public function check_requirements(): bool {
		$properties = [
			$this->get_api_user(),
			$this->get_app_id(),
			$this->get_brand(),
			$this->get_license_key(),
		];

		return count( array_filter( $properties ) ) === count( $properties );
	}


	/**
	 * Check if an email address is valid.
	 *
	 * @param mixed $email The email address to validate.
	 *
	 * @return WP_Error|string Returns WP_Error with 'invalid_email' code and error message if the email is invalid, otherwise returns null.
	 */
	public function is_email_valid( mixed $email ): WP_Error|string {
		if ( ! is_string( $email ) ) {
			return new WP_Error( 'empty_email', __( 'Email address is not string.', 'nr' ) );
		}
		$email = sanitize_email( strtolower( $email ) );
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'Invalid email address.', 'nr' ) );
		}
		return $email;
	}

	/**
	 * Logs a message (if debug mode is enabled).
	 *
	 * @param string $message The message to log.
	 *
	 * @return void
	 */
	private function log( string $message ): void {
		// Avoid running inside unit tests.
		if ( defined( 'DOING_UNIT_TEST' ) && DOING_UNIT_TEST ) {
			return;
		}
		if ( $this->is_debug() ) {
			return;
		}
		error_log( $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Sends an API request to the specified endpoint with the given data.
	 *
	 * @param string     $service The endpoint to send the request to.
	 * @param array|null $data The data to send with the request.
	 * @param string     $api The API to use (client or brand).
	 *
	 * @return array|WP_Error The response data as an associative array, or a WP_Error object if there was an error.
	 */
	public function call( string $service, ?array $data, string $api = 'client' ): array|WP_Error {
		if ( ! $this->check_requirements() ) {
			$this->log( 'Missing required properties.' );

			return new WP_Error( 'missing_properties', __( 'Missing required properties.', 'nr' ) );
		}

		$endpoint = $this->get_endpoint( $api, $service );

		$response = wp_remote_post( esc_url_raw( $endpoint ), [
			'headers' => [
				'x-omeda-appid' => $this->get_app_id(),
				'x-omeda-inputid' => $this->get_input_id(),
				'Content-Type'  => 'application/json',
			],
			'body'    => wp_json_encode( $data ),
		] );

		if ( is_wp_error( $response ) ) {
			$this->log( 'Something went wrong: ' . $response->get_error_message() );
			$this->log( $response->get_error_data() );

			return $response;
		}

		// Check the HTTP response code.
		$status = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status ) {
			$this->log( $status . 'HTTP Error: ' . wp_remote_retrieve_body( $response ) );
			$this->log( 'Endpoint: ' . $endpoint );
			$this->log( 'Data: ' . wp_json_encode( $data ) );

			return new WP_Error( 'http_error', wp_remote_retrieve_body( $response ) );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Gets the deployment types (newsletter IDs) for the brand.
	 *
	 * @see https://training.omeda.com/knowledge-base/deployment-type-lookup-by-brand-api/
	 *
	 * @param array|null $data The data to send with the request.
	 *
	 * @return array|WP_Error The response data as an associative array, or a WP_Error object if there was an error.
	 */
	public function get_deployment_types( ?array $data = null ): WP_Error|array {
		return $this->call( 'deploymenttypes', $data, 'brand' );
	}

	/**
	 * Sends an opt-in to the queue.
	 *
	 * @see https://training.omeda.com/knowledge-base/api-email-optin-queue-service/
	 *
	 * @param WP_REST_Request $request The opt-in data as an associative array.
	 *
	 * @return array|WP_Error The response data as an associative array, or WP_Error if there was an error.
	 */
	public function opt_in( WP_REST_Request $request ): array|WP_Error {
		// Check for valid email address.
		$email = $this->is_email_valid( $request->get_param( 'email' ) );
		if ( is_wp_error( $email ) ) {
			return $email;
		}

		// NR-5278: Set the input ID to automatically create customers.
		$this->set_input_id( '7900G2456689A2G' );

		return $this->call( 'optinfilterqueue', [
			'DeploymentTypeOptIn' => [
				[
					'EmailAddress'     => $email,
					'Source'           => $request->get_param( 'source' ),
					'DeploymentTypeId' => $request->get_param( 'newsletters' ),
					'DeleteOptOut'     => 1,
				],
			],
		] );
	}

	/**
	 * Sends an opt-out to the queue.
	 *
	 * @see https://training.omeda.com/knowledge-base/api-email-optout-queue-service/
	 *
	 * @param WP_REST_Request $request The opt-out data as an associative array.
	 *
	 * @return array|WP_Error The response data as an associative array, or WP_Error if there was an error.
	 */
	public function opt_out( WP_REST_Request $request ): array|WP_Error {
		// Check for valid email address.
		$email = $this->is_email_valid( $request->get_param( 'email' ) );
		if ( is_wp_error( $email ) ) {
			return $email;
		}

		return $this->call( 'optoutfilterqueue', [
			'DeploymentTypeOptIn' => [
				[
					'EmailAddress'     => $email,
					'Source'           => $request->get_param( 'source' ),
					'DeploymentTypeId' => $request->get_param( 'newsletters' ),
				],
			],
		] );
	}

	/**
	 * Email opt-in/out lookup.
	 *
	 * @see https://training.omeda.com/knowledge-base/api-email-opt-in-out-lookup-service/
	 *
	 * @param string $email The email address to check.
	 *
	 * @return array|WP_Error The response data as an associative array, or WP_Error if there was an error.
	 */
	public function opt_in_out_lookup( string $email ): array|WP_Error {
		// Check for valid email address.
		$email = $this->is_email_valid( $email );
		if ( is_wp_error( $email ) ) {
			return $email;
		}
		return $this->call( 'filter/email/' . $email, null, 'brand' );
	}
}
