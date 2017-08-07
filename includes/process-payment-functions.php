<?php

/**
 * Process payment functions
 *
 * @since   1.5.0
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * Function that will process the payment
 *
 * @since 1.0.0
 */
function pt_process_payment() {

	// Create an array with all details of the payment in Paytium (not Mollie, that comes later)
	$paytium_payment = array();

	// Get the details submitted by the form
	$paytium_payment['amount']       = pt_user_amount_to_float( $_POST['pt-amount'] );
	$paytium_payment['description'] = wp_kses_post( $_POST['pt-description'] );
	$paytium_payment['store_name']  = sanitize_text_field( $_POST['pt-name'] );
	$paytium_payment['test_mode']   = ( isset( $_POST['pt_test_mode'] ) ? 'true' : 'false' );

	// Check if pt-subscription-interval is set and not empty, then set subscription to 1
	$paytium_payment['subscription']          = ( isset( $_POST['pt-subscription-interval'] ) && ! empty( $_POST['pt-subscription-interval'] ) ? '1' : '0' );
	$paytium_payment['subscription_interval'] = ( isset( $_POST['pt-subscription-interval'] ) ? $_POST['pt-subscription-interval'] : '' );
	$paytium_payment['subscription_times']    = ( isset( $_POST['pt-subscription-times'] ) ? $_POST['pt-subscription-times'] : '' );

	// Create a subscription_start_date for Paytium, as first_amount for Paytium is now the full subscription amount
	// Might need to make this more flexible in future versions if users get option to set their own first_amount
	$paytium_payment['subscription_start_date'] = ( isset( $_POST['pt-subscription-start-date'] ) ? $_POST['pt-subscription-start-date'] : date( 'Y-m-d', strtotime( $paytium_payment['subscription_interval'] ) ) );

	// Check if pt-paytium-no-payment is set, if it is, this form doesn't require a payment
	$paytium_payment['no_payment'] = ( isset( $_POST['pt-paytium-no-payment'] ) ? true : false );

	// Get current post/page URL and set as URL where customers will be redirected to after payment
	$paytium_payment['pt_redirect_url'] = sanitize_text_field( isset( $_POST['pt_redirect_url'] ) ? $_POST['pt_redirect_url'] : home_url() );

	// One exception, change redirect URL to admin when going through the setup wizard in admin
	if ( is_admin() ) :
		$paytium_payment['pt_redirect_url'] = add_query_arg( 'step', 'payment-test', admin_url( 'admin.php?page=pt-setup-wizard' ) );
	endif;

	// Get the active Mollie API key
	pt_set_paytium_key( $paytium_payment['test_mode'] );

	$meta = apply_filters( 'pt_meta_values', array () );

	// We allow a spot to hook in, but the hook in is responsible for all of the code.
	// If the action is non-existent, then we run a default for the button.
	if ( has_action( 'pt_process_payment' ) ) {
		do_action( 'pt_process_payment' );
	} else {

		try {

			// Save payment to WP posts database
			$paytium_payment['post_id'] = pt_create_payment( array (
				'subscription'            => $paytium_payment['subscription'],
				'subscription_interval'   => $paytium_payment['subscription_interval'],
				'subscription_times'      => $paytium_payment['subscription_times'],
				'subscription_start_date' => $paytium_payment['subscription_start_date'],
				'order_status'            => 'new',
				'method'                  => '',
				'meta'                    => $meta,
			) );

			// Generate a secure payment key to use in the redirectURL
			$paytium_payment['payment_key'] = substr( sha1( sha1( $paytium_payment['post_id'] ) . $paytium_payment['post_id'] ), 0, 13 );

			// Generate payment URLs
			$paytium_payment['redirect_url'] = add_query_arg( 'pt-payment', $paytium_payment['payment_key'], ! empty ( $paytium_payment['pt_redirect_url'] ) ? $paytium_payment['pt_redirect_url'] : home_url() );
			$paytium_payment['webhook_url']  = add_query_arg( 'pt-webhook', 'paytium', home_url( '/' ) );

			// Convert internal metadata to user readable (move labels to keys etc)
			$paytium_payment['mollie_metadata'] = pt_convert_to_mollie_metadata( $meta );

			// Set customer to none at first
			$paytium_payment['customer_id'] = '';

			// Get customer details (Name, Email) from meta to use Mollie Customer API
			$mollie_customer = pt_get_mollie_customer_data_from_meta( $meta );

			// If there are no name & email set as field, a Mollie customer can not be created
			if ( ! empty( $mollie_customer['name'] ) && ! empty( $mollie_customer['email'] ) ) {

				// Decided to always create a Mollie customer, even when payment is not a subscription
				// See Paytium issue #74 for discussion: https://github.com/davdebcom/paytium/issues/74
				$paytium_payment['customer_id'] = paytium_create_new_mollie_customer( $mollie_customer );

				// Save Mollie customer details to Payment post meta
				$new_payment_details = ( array (
					'pt-customer-name'  => $mollie_customer['name'],
					'pt-customer-email' => $mollie_customer['email'],
					'pt-customer-id'    => $paytium_payment['customer_id'],
				) );
				pt_update_payment_meta( $paytium_payment['post_id'], $new_payment_details );
			} else {
				error_log ('No Mollie Customer created, no fields with type name & email found');
			}

			if ( $paytium_payment['no_payment'] == false ) {
				$redirect = pt_paytium_create_mollie_payment_and_redirect( $paytium_payment );
			} else {
				$redirect = pt_paytium_update_form_submission_and_redirect( $paytium_payment );
			}

			// Add a filter here to allow developers to process payment as well
			do_action( 'paytium_after_full_payment_saved', $paytium_payment['post_id'] );

			// Redirect user to Mollie for payment or message for form submissions
			wp_redirect( $redirect );
			die;

		} catch ( Mollie_API_Exception $e ) {
			error_log( 'Creating payment failed: ' . htmlspecialchars( $e->getMessage() ) );
			echo( 'Creating payment failed: ' . htmlspecialchars( $e->getMessage() ) );
		}

		exit;
	}

	return;

}

// We only want to process the payment if form submitted
if ( isset( $_POST['pt-amount'] ) ) {
	// Changed from init to wp_loaded to solve WooCommerce conflict - http://wordpress.stackexchange.com/a/67635
	add_action( 'wp_loaded', 'pt_process_payment' );
}

/**
 * With Paytium payment, create a Mollie payment and redirect user to Mollie
 *
 * @since   1.5.0
 */
function pt_paytium_create_mollie_payment_and_redirect( $paytium_payment ) {

	global $pt_mollie;

	// Note: (first) payments are always required for Paytium, because we use a workaround with first amount & start date.

	// Is this a subscription? Then set Mollie $recurringType to first
	$paytium_payment['recurringType'] = ( $paytium_payment['subscription'] == '1' ? 'first' : null );

	// Create payment at Mollie
	$payment = $pt_mollie->payments->create(
		array (
			'amount'        => $paytium_payment['amount'],
			'customerId'    => $paytium_payment['customer_id'],
			'recurringType' => $paytium_payment['recurringType'],
			'description'   => wp_unslash( $paytium_payment['description'] . ' ' . $paytium_payment['post_id'] ),
			'redirectUrl'   => $paytium_payment['redirect_url'],
			'webhookUrl'    => $paytium_payment['webhook_url'],
			'metadata'      => array (
				'Store'    => wp_unslash( $paytium_payment['store_name'] ),
				'Order ID' => wp_unslash( $paytium_payment['description'] ),
				'Details'  => $paytium_payment['mollie_metadata'],
				// Paytium 1.4: Removed storing custom field data in Mollie metadata,
				// Because it's limited to 1024 KB, and that would limit the
				// amount of custom fields users of Paytium could add
			)
		)
	);

	// Save new data from Mollie to the Payment post meta
	$new_payment_details = ( array (
		'payment_id'  => $payment->id,
		'mode'        => $payment->mode,
		'amount'      => $payment->amount,
		'description' => $payment->description,
		'status'      => $payment->status,
		'payment_key' => $paytium_payment['payment_key'],
	) );
	pt_update_payment_meta( $paytium_payment['post_id'], $new_payment_details );

	// Get payment URL (URL at Mollie) where user should be redirect to
	return $payment->getPaymentUrl();

}

/**
 * With Paytium payment without a payment (regular form), update form submission and redirect to Thank You page
 *
 * @since   1.5.0
 */
function pt_paytium_update_form_submission_and_redirect( $paytium_payment ) {

	// Save new data from Mollie to the Payment post meta
	$new_payment_details = ( array (
		'payment_id'    => null,
		'mode'          => null,
		'amount'        => '-',
		'description'   => $paytium_payment['description'],
		'status'        => 'open',
		'payment_key'   => $paytium_payment['payment_key'],
		'pt_no_payment' => '1',
	) );
	pt_update_payment_meta( $paytium_payment['post_id'], $new_payment_details );

	return $paytium_payment['redirect_url'];
}


/**
 * Collect all field data and combine into one meta array
 *
 * @since 1.0.0
 */
function pt_add_all_field_data_to_meta_array( $meta ) {

	// Loop to get all fields and their labels
	foreach ( $_POST as $key => $value ) {

		$is_field = strstr( $key, 'pt-field-' );

		$is_customer_details = strstr( $key, 'pt-customer-details-' );

		if ( $is_field || $is_customer_details ) {

			// TODO: add new name field for Mollie customer name?

			if ( strstr( $key, '-label' ) ) {
				//echo 'User label: ' . $key . ' ' . $value . '<br />';
				sanitize_key( $key );
				sanitize_text_field( $value );
			}

			if ( ( strstr( $key, 'text' ) || strstr( $key, 'pt-customer-details-' ) ) && ( strstr( $key, '-label' ) == false ) ) {
				//echo 'Text & textarea fields: ' . $key . ' ' . $value . '<br />';
				sanitize_key( $key );
				sanitize_text_field( $value );
			}

			if ( ( strstr( $key, 'email' ) ) && ( strstr( $key, '-label' ) == false ) ) {
				//echo 'Email fields: ' . $key . ' ' . $value . '<br />';
				sanitize_key( $key );
				sanitize_email( $value );
			}

			$meta[ $key ] = $value;
		}

	};

	return $meta;

}

add_filter( 'pt_meta_values', 'pt_add_all_field_data_to_meta_array' );

/**
 * Convert field data in $meta to user-readable array for Mollie metadata
 *
 * @since 1.0.0
 */
function pt_convert_to_mollie_metadata( $meta ) {

	$mollie_metadata = array ();

	$count = 0;
	foreach ( $meta as $key => $value ) {

		// Add fields to Mollie metadata
		if ( strstr( $key, '-label' ) ) {
			// Update key/label for fields with user defined label
			$field_key                 = str_replace( '-label', '', $key );
			$mollie_metadata[ $value ] = $meta[ $field_key ];
		}

		// Add customer details fields to Mollie metadata
		if ( strstr( $key, 'pt-customer-details-' ) ) {
			$customer_details_key                     = ucfirst( str_replace( 'house_number', 'House number', str_replace( '-', ' ', str_replace( 'pt-customer-details-', '', $key ) ) ) );
			$mollie_metadata[ $customer_details_key ] = $value;
		}

		$count += 1;
		if ( $count == 40 ) {
			error_log( $count );
			$mollie_metadata['Warning 1/3'] = 'Not all information is shown here.';
			$mollie_metadata['Warning 2/3'] = 'Mollie limits this to 1024KB.';
			$mollie_metadata['Warning 3/3'] = 'View the rest in Paytium in WordPress.';
			break;
		}
	}

	return $mollie_metadata;

}

/**
 * In the field data, find the field name and email to use a customer for Mollie Customers API
 *
 * @since 1.2.0
 *
 * @param   array $meta All fields from the Paytium form
 *
 * @return  array   $mollie_customer    Array that contains name and email for customer
 *
 */
function pt_get_mollie_customer_data_from_meta( $meta ) {

	$mollie_customer = array ();

	foreach ( $meta as $key => $value ) {

		if ( strpos( $key, 'pt-field-name-' ) !== false ) {
			$mollie_customer['name'] = $value;
			break;
		}
	}

	foreach ( $meta as $key => $value ) {

		if ( strpos( $key, 'pt-field-email-' ) !== false ) {
			$mollie_customer['email'] = $value;
			break;
		}
	}

	return $mollie_customer;
}


/**
 * Process to validate and get or crate a Mollie customer
 *
 * @since 1.2.0
 *
 */
function pt_get_or_create_mollie_customer( $mollie_customer ) {

	// Not used.
	// See Paytium issue #74 for discussion
	// https://github.com/davdebcom/paytium/issues/74

	global $pt_mollie;

	// Check in Paytium database:
	// is there already a Mollie customer with customer ID stored?
	$customer_id_in_paytium = trim( pt_get_customer_by_email_in_paytium( $mollie_customer['email'] ) );

	if ( $customer_id_in_paytium == false ) {

		error_log( 'No customer with email ' . $mollie_customer['email'] . ' was found in Paytium, create a new one.' );
		$customer_id = paytium_create_new_mollie_customer( $mollie_customer );

	} else {

		error_log( 'A customer with email ' . $mollie_customer['email'] . ' was found in Paytium: ' . $customer_id_in_paytium );

		// A customer with this email and a customer ID found in Paytium
		// Does Mollie also know about this customer based on customer ID?
		$customer_id_at_mollie = paytium_check_customer_id_known_by_mollie( $customer_id_in_paytium );

		if ( $customer_id_at_mollie == false ) {
			// If Mollie does not know about this customer, create a new one
			error_log( 'No customer with ID ' . $customer_id_in_paytium . ' was found at Mollie, create a new one.' );
			$customer_id = paytium_create_new_mollie_customer( $mollie_customer );
		} else {
			// Mollie has this customer ID on file, use customer ID for the new payment
			error_log( 'A customer with ID ' . $customer_id_at_mollie . ' was found at Mollie' );
			$customer_id = $customer_id_in_paytium;
		}
	}

	// Save to post meta
	error_log( "Customer ID used for this payment: " . $customer_id );

	return $customer_id;

}

/**
 * Check customer ID is known by Mollie (for this profile key)
 *
 * @since 1.4.0
 *
 */
function paytium_check_customer_id_known_by_mollie( $customer_id ) {

	// Not used.
	// See Paytium issue #74 for discussion
	// https://github.com/davdebcom/paytium/issues/74

	global $pt_mollie;

	$customers = (array) $pt_mollie->customers->all();

	$customer_id_at_mollie = false;

	foreach ( $customers as $key => $customer ) {

		if ( $customer_id == $customer->id ) {
			$customer_id_at_mollie = $customer->id;
			break;
		} else {
			$customer_id_at_mollie = false;
		}
	}

	return $customer_id_at_mollie;
}

/**
 * Create a new Mollie customer
 *
 * @since 1.4.0
 *
 */
function paytium_create_new_mollie_customer( $mollie_customer ) {

	global $pt_mollie;

	$customer = $pt_mollie->customers->create( array (
		"name"  => wp_unslash( $mollie_customer['name'] ),
		"email" => $mollie_customer['email'],
	) );

	error_log( 'New customer created at Mollie for: ' . implode( ', ', $mollie_customer ) . ', ' . $customer->id );

	return $customer->id;
}

/**
 * Send post meta
 *
 * @since 1.0.0
 */
function pt_cf_checkout_meta( $meta ) {

	if ( isset( $_POST['pt_form_field'] ) ) {
		foreach ( $_POST['pt_form_field'] as $k => $v ) {
			// Drop the default value for paytium_radio and paytium_dropdown
			// I have a superior way to store key and value (see public.js)
			if ( strstr( $k, 'pt_cf_' ) ) {
				continue;
			}

			if ( ! empty( $v ) ) {
				$meta[ $k ] = $v;
			}
		}
	}

	return $meta;

}


add_filter( 'pt_meta_values', 'pt_cf_checkout_meta' );