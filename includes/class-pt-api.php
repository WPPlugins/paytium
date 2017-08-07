<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class PT_API.
 *
 * API Class makes general API calls to the underlying API.
 *
 * @class          PT_API
 * @version        1.0.0
 * @author         Jeroen Sormani
 */
class PT_API {


	protected $api_url = 'https://api.davdeb.com/';


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

	}


	/**
	 * Create Mollie account.
	 *
	 * Create a new Mollie account via the API.
	 *
	 * @since 1.0.0
	 *
	 * @param  $args
	 *
	 * @param  array $args List of API arguments.
	 *
	 * @return array|WP_Error       WP_Error when the API call failed. Array with the result otherwise.
	 */
	public function create_mollie_account( $args ) {

		$args = wp_parse_args( $args, array (
			'action'           => 'account-create',
			'username'         => isset( $args['username'] ) ? sanitize_text_field( $args['username'] ) : '',
			'customer_details' => array (
				'testmode'     => '0',
				'name'         => isset( $args['name'] ) ? sanitize_text_field( $args['name'] ) : '',
				'company_name' => isset( $args['company_name'] ) ? sanitize_text_field( $args['company_name'] ) : '',
				'email'        => isset( $args['email'] ) ? sanitize_text_field( $args['email'] ) : '',
				'address'      => isset( $args['address'] ) ? sanitize_text_field( $args['address'] ) : '',
				'zipcode'      => isset( $args['zipcode'] ) ? sanitize_text_field( $args['zipcode'] ) : '',
				'city'         => isset( $args['city'] ) ? sanitize_text_field( $args['city'] ) : '',
				'country'      => isset( $args['country'] ) ? sanitize_text_field( $args['country'] ) : '',
				// Optional: company details
				//				'registration_number' => $registration_number,
				//				'legal_form' => 'eenmanszaak',
				//				'representative' => 'Jack de Boer',
				// Optional: when billing address is different
				//				'billing_address' => '',
				//				'billing_zipcode' => '',
				//				'billing_city' => '',
				//				'billing_country' => '',
				// Bank account fields can not be edited with account-edit, use bankaccount-edit
				//				// Optional: bank account details
				//				'bankaccount_iban' => '',
				//				'bankaccount_bic' => 'INGBNL2A',
				//				'bankaccount_bankname' => 'ING',
				//				'bankaccount_location' => 'Amsterdam',
				// Optional: VAT number, must validate
				//				'vat_number' => 'NL123456789B01'
				//				'country' => 'NL',
			),
		) );

		$response = $this->post( $args );

		return $response;

	}


	/**
	 * Claim Mollie account.
	 *
	 * Claim a Mollie account.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args List of API arguments.
	 *
	 * @return array|WP_Error       WP_Error when the API call failed. Array with the result otherwise.
	 */
	public function claim_mollie_account( $args ) {

		// Encode password so it's save to transmit
		$args['password'] = base64_encode( $args['password'] );

		$args     = wp_parse_args( $args, array (
			'action'   => 'account-claim',
			'username' => isset( $args['username'] ) ? sanitize_text_field( $args['username'] ) : '',
			'password' => isset( $args['password'] ) ? sanitize_text_field( $args['password'] ) : '',
		) );
		$response = $this->post( $args );

		return $response;

	}


	/**
	 * Claim Mollie profile.
	 *
	 * Claim a Mollie profile.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args List of API arguments.
	 *
	 * @return array|WP_Error       WP_Error when the API call failed. Array with the result otherwise.
	 */
	public function create_mollie_profile( $args ) {

		// Encode password so it's save to transmit
		$args['password'] = base64_encode( $args['password'] );

		$args     = wp_parse_args( $args, array (
			'action'   => 'profile-create',
			'username' => isset( $args['username'] ) ? sanitize_text_field( $args['username'] ) : '',
			'password' => isset( $args['password'] ) ? sanitize_text_field( $args['password'] ) : '',
		) );
		$response = $this->post( $args );

		return $response;

	}


	/**
	 * Verify profile.
	 *
	 * Verify the user's website profile (see if its 'verified').
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args List of API arguments.
	 *
	 * @return array|WP_Error       WP_Error when the API call failed. Array with the result otherwise.
	 */
	public function verify_profile( $args ) {

		// Encode password so it's save to transmit
		$args['password'] = base64_encode( $args['password'] );

		$args     = wp_parse_args( $args, array (
			'action'   => 'profile-verified',
			'username' => isset( $args['username'] ) ? sanitize_text_field( $args['username'] ) : '',
			'password' => isset( $args['password'] ) ? sanitize_text_field( $args['password'] ) : '',
			'hash'     => isset( $args['hash'] ) ? sanitize_text_field( $args['hash'] ) : '',
		) );
		$response = $this->post( $args );

		return $response;

	}


	/**
	 * POST API call.
	 *
	 * Make a POST API call to the url.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args List of API arguments.
	 *
	 * @return array|WP_Error       WP_Error when the API call failed. Array with the result otherwise.
	 */
	public function post( $args ) {

		$args = wp_parse_args( $args, array (
			'version'        => 'v1',
			'client'         => 'wordpress',
			'server_details' => array (
				'HTTP_HOST'       => isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '',
				'HTTP_CONNECTION' => isset( $_SERVER['HTTP_CONNECTION'] ) ? $_SERVER['HTTP_CONNECTION'] : '',
				'SERVER_NAME'     => isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '',
				'REQUEST_METHOD'  => isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '',
				'QUERY_STRING'    => isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : '',
				'REMOTE_ADDR'     => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '',
				'SERVER_SOFTWARE' => isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '',
			),
		) );

		$response = wp_remote_post( $this->api_url, array (
			'timeout'     => 5,
			'redirection' => 5,
			'blocking'    => true,
			'headers'     => array (),
			'body'        => $args,
			'cookies'     => array ()
		) );

		return $response;

	}


}
