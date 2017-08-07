<?php

/**
 * Redirect URL functions
 *
 * @since   1.5.0
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to show a message and payment details after the purchase
 *
 * @param  mixed $content WP the_content.
 *
 * @return mixed $html
 *
 * @since 1.0.0
 */
function pt_show_payment_details( $content ) {

	global $pt_script_options;
	global $pt_mollie;

	$place_above = ( $pt_script_options['other']['payment_details_placement'] == 'above' ? true : false );

	$html = '';

	if ( in_the_loop() && is_main_query() ) {

		// TODO David - Also add unique order number, after orders database is implemented
		if ( isset( $_GET['pt-payment'] ) ) {

			$payment_id = $_GET['pt-payment'];
			$payment    = pt_get_payment_by_payment_key( $payment_id );

			$pretty_status = $payment->get_status();

			error_log( 'Shown after payment message for payment_id: ' . print_r( $payment->id, true ) );

			$payment_status_style = ( $payment->status == 'paid' || $payment->no_payment == true ) ? 'pt-payment-details-wrap' : 'pt-payment-details-wrap pt-payment-details-error';

			$html = '<div class="' . $payment_status_style . '">' . "\n";

			if (( $payment->status == 'open' ) && ( $payment->no_payment == true ) ){
				$html .= __( 'Thank you for your submission! We will get in touch shortly!', 'paytium' );
			} elseif ( $payment->status == 'paid' ) {
				$html .= __( 'Thank you for your order, the status is:', 'paytium' ) . ' <b>' . strtolower($pretty_status) . '</b>.' . "\n";
			} elseif (( $payment->status == 'open' ) && ( $payment->no_payment == false ) ) {
				$html .= sprintf( __( 'The payment is: %s, this status might still change.', 'paytium' ), '<b>' . strtolower($pretty_status) . '</b>' ) . "\n";
			} else {
				$html .= __( 'The payment status is:', 'paytium' ) . ' <b>' . strtolower($pretty_status) . '</b>.' . "\n";
			}

			if ( ! empty( $payment->subscription_id ) ) {
				$html .= '<br />';
				$html .= __( 'Your subscription has been created with id', 'paytium' ) . ' <b>' . $payment->subscription_id . '</b>.' . "\n";
			}

			$html .= '</div>' . "\n";

			// TODO David - Return more payment information after Orders DB is implemented:
			// Payment description, amount, store_name/description, transaction ID, order ID

			// David - Paytium 1.1.0
			// Add custom hook after payment
			do_action( 'paytium_after_pt_show_payment_details', $payment );

			return $html;

		}
	}

	if ( $place_above ) {
		return apply_filters( 'pt_payment_details', $html ) . $content;
	} else {
		return $content . apply_filters( 'pt_payment_details', $html );
	}

}

add_filter( 'the_content', 'pt_show_payment_details', 11 );