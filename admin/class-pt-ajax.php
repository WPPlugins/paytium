<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class PT_Admin_AJAX.
 *
 * AJAX class has holds all the Admin ajax calls.
 *
 * @class          PT_Admin_AJAX
 * @version        1.0.0
 * @author         Jeroen Sormani
 */
class PT_Admin_AJAX {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Login Mollie account
		add_action( 'wp_ajax_paytium_mollie_login_data', array ( $this, 'save_mollie_login' ) );

		// Create Mollie account
		add_action( 'wp_ajax_paytium_mollie_create_account', array ( $this, 'create_mollie_account' ) );

		// Create Mollie profile
		add_action( 'wp_ajax_paytium_mollie_create_profile', array ( $this, 'create_mollie_profile' ) );

		// Check profile
		add_action( 'wp_ajax_paytium_mollie_check_profile_status', array ( $this, 'check_profile_verified' ) );

		// Activate live orders
		add_action( 'wp_ajax_paytium_mollie_activate_live_orders', array ( $this, 'activate_live_orders' ) );

		// Check if a payment exists
		add_action( 'wp_ajax_paytium_check_payment_exists', array ( $this, 'check_if_payment_exists' ) );

	}


	/**
	 * Save Mollie.
	 *
	 * Save the Mollie login/password for usage later.
	 *
	 * @since 1.0.0
	 */
	public function save_mollie_login() {

		check_ajax_referer( 'paytium-ajax-nonce', 'nonce' );

		// Bail if required value is not set.
		if ( ! isset( $_POST['form'] ) ) {
			die();
		}

		$post_data = wp_parse_args( $_POST['form'] );

		$args     = array (
			'username' => sanitize_text_field( $post_data['username'] ),
			'password' => sanitize_text_field( $post_data['password'] ),
		);
		$response = Paytium()->api->claim_mollie_account( $args );


		// An error occurred in the initial API call
		if ( strpos( $response['body'], 'error' ) !== false ) {
			die( json_encode( array (
				'status'  => 'error',
				'message' => '<div class="pt-alert pt-alert-danger">' . $response['body'] . '</div>',
			) ) );
		}

		// Passed initial API call and got response from Mollie
		if ( is_object( json_decode( $response['body'] ) ) ) {
			$response_data = json_decode( $response['body'] );

			set_transient( 'paytium_mollie_username', $post_data['username'], WEEK_IN_SECONDS * 2 ); // 2 week expiration
			set_transient( 'paytium_mollie_password', $post_data['password'], WEEK_IN_SECONDS * 2 ); // 2 week expiration

			die( json_encode( array (
				'status'  => 'success',
				'message' => '<div class="pt-alert pt-alert-success">' . esc_html( $response_data->data->resultmessage ) . '</div>',
			) ) );
		}

	}


	/**
	 * Create Mollie account.
	 *
	 * Create a Mollie account. Initialized via the setup wizard.
	 *
	 * @since 1.0.0
	 */
	public function create_mollie_account() {

		check_ajax_referer( 'paytium-ajax-nonce', 'nonce' );

		// Bail if required value is not set.
		if ( ! isset( $_POST['form'] ) ) {
			die();
		}

		$post_data = wp_parse_args( $_POST['form'] );

		$args     = array (
			'username'     => sanitize_text_field( $post_data['username'] ),
			'name'         => sanitize_text_field( $post_data['name'] ),
			'company_name' => sanitize_text_field( $post_data['company_name'] ),
			'email'        => sanitize_text_field( $post_data['email'] ),
			'address'      => sanitize_text_field( $post_data['address'] ),
			'zipcode'      => sanitize_text_field( $post_data['zipcode'] ),
			'city'         => sanitize_text_field( $post_data['city'] ),
			'country'      => sanitize_text_field( $post_data['country'] ),
		);
		$response = Paytium()->api->create_mollie_account( $args );

		// An error occurred in the initial API call
		if ( strpos( $response['body'], 'error' ) !== false ) {
			die( json_encode( array (
				'status'  => 'error',
				'message' => '<div class="pt-alert pt-alert-danger">' . $response['body'] . '</div>',
			) ) );
		}

		// Passed initial API call and got response from Mollie
		if ( is_object( json_decode( $response['body'] ) ) ) {
			$response_data = json_decode( $response['body'] );

			set_transient( 'paytium_mollie_username', $response_data->data->username, WEEK_IN_SECONDS * 2 ); // 2 week expiration
			set_transient( 'paytium_mollie_password', $response_data->data->password, WEEK_IN_SECONDS * 2 ); // 2 week expiration
			update_option( 'paytium_mollie_partner_id', $response_data->data->partner_id );

			die( json_encode( array (
				'status'  => 'success',
				'message' => '<div class="pt-alert pt-alert-success">' . esc_html( $response_data->data->resultmessage ) . '</div>',
			) ) );
		}

	}


	/**
	 * Create Mollie profile.
	 *
	 * Create a Mollie website profile. Initialized via the setup wizard.
	 *
	 * @since 1.0.0
	 */
	public function create_mollie_profile() {

		check_ajax_referer( 'paytium-ajax-nonce', 'nonce' );

		// Bail if required value is not set.
		if ( ! isset( $_POST['form'] ) ) {
			die();
		}

		$post_data = wp_parse_args( $_POST['form'] );

		$args     = array (
			'username' => get_transient( 'paytium_mollie_username' ),
			'password' => get_transient( 'paytium_mollie_password' ),
			'name'     => sanitize_text_field( $post_data['name'] ),
			'website'  => sanitize_text_field( $post_data['website'] ),
			'email'    => sanitize_text_field( $post_data['email'] ),
			'phone'    => sanitize_text_field( $post_data['phone'] ),
			'category' => sanitize_text_field( $post_data['category'] ),
		);
		$response = Paytium()->api->create_mollie_profile( $args );

		// An error occurred in the initial API call
		if ( strpos( $response['body'], 'error' ) !== false ) {
			die( json_encode( array (
				'status'  => 'error',
				'message' => '<div class="pt-alert pt-alert-danger">' . $response['body'] . '</div>',
			) ) );
		}

		// Passed initial API call and got response from Mollie
		if ( is_object( json_decode( $response['body'] ) ) ) {
			$response_data = json_decode( $response['body'] );

			// Save profile data
			update_option( 'paytium_mollie_website_profile', $response_data->data->profile->hash );
			update_option( 'paytium_test_api_key', $response_data->data->profile->api_keys->test );
			update_option( 'paytium_live_api_key', $response_data->data->profile->api_keys->live );

			die( json_encode( array (
				'status'  => 'success',
				'message' => '<div class="pt-alert pt-alert-success">' . esc_html( $response_data->data->resultmessage ) . '</div>',
			) ) );

		}

	}


	/**
	 * Check profile status.
	 *
	 * Check the profile status that has been created before, and see if the
	 * status is set to 'verified'.
	 *
	 * @since 1.0.0
	 */
	public function check_profile_verified() {

		check_ajax_referer( 'paytium-ajax-nonce', 'nonce' );

		$website_profile = get_option( 'paytium_mollie_website_profile', array ( 'hash' => '' ) );
		$hash            = $website_profile['hash'];
		$args            = array (
			'username' => get_transient( 'paytium_mollie_username' ),
			'password' => get_transient( 'paytium_mollie_password' ),
			'hash'     => $hash,
		);
		$response        = Paytium()->api->verify_profile( $args );

		// Passed initial API call and got response from Mollie
		if ( is_array( $response ) ) {

			$response_data = json_decode( $response['body'] );

			if ( $response_data->data ) {
				$this->switch_to_live();
				die( 'verified' );
			} else {
				die( 'not-verified' );
			}
		}

		die();

	}


	/**
	 * Switch shop to live.
	 *
	 * Set the shop to receive live orders.
	 *
	 * @since 1.0.0
	 */
	public function switch_to_live() {

		update_option( 'paytium_enable_live_key', 1 );

	}


	/**
	 * Test payment.
	 *
	 * Check if a test payment exists.
	 *
	 * @since 1.0.0
	 */
	public function check_if_payment_exists() {

		$payments = pt_get_payments( array ( 'posts_per_page' ) );

		if ( ! empty( $payments ) ) :
			die( json_encode( array (
				'status'  => 'success',
				'message' => '<div class="pt-alert pt-alert-success">' . __( 'A test payment has been found', 'paytium' ) . '</div>',
			) ) );
		else :
			die( json_encode( array (
				'status'  => 'success',
				'message' => '<div class="pt-alert pt-alert-danger">' . __( 'No test payments were found', 'paytium' ) . '</div>',
			) ) );
		endif;

	}


}
