<?php

/**
 * Misc plugin functions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common method to set Mollie API key from options.
 *
 * @since 1.0.0
 */
function pt_set_paytium_key( $test_mode = 'false' ) {

	global $pt_mollie;

	// Check whether existing payment mode is sent and convert to true or false
	if ( $test_mode == 'live' ) {
		$test_mode = 'false';
	}

	if ( $test_mode == 'test' ) {
		$test_mode = 'true';
	}

	// Check first if in live or test mode.
	if ( get_option( 'paytium_enable_live_key', false ) == 1 && $test_mode != 'true' ) {
		$key = get_option( 'paytium_live_api_key', '' );
	} else {
		$key = get_option( 'paytium_test_api_key', '' );
	}

	try {
		$pt_mollie->setApiKey( $key );
	}
	catch ( Mollie_API_Exception $e ) {
		echo 'Your API key is incorrect or missing! Please check the Paytium settings! Error message(s): ' . htmlspecialchars( $e->getMessage() ) . '<br />';
	}

}

/**
 * Log debug messages for Paytium
 *
 * @since   1.0.0
 */
function pt_log_me( $message ) {

	if ( WP_DEBUG === true ) {
		if ( is_array( $message ) || is_object( $message ) ) {
			error_log( print_r( $message, true ) );
		} else {
			error_log( $message );
		}
	}

}

function pt_user_amount_to_float( $amount ) {

	$amount = floatval( str_replace( ',', '.', $amount ) );

	return $amount;

}


function pt_float_amount_to_currency( $amount ) {

	$amount = str_replace( '.', ',', $amount );

	return $amount;

}

/**
 * Google Analytics campaign URL.
 *
 * @since   1.0.0
 *
 * @param  string $base_url Plain URL to navigate to
 * @param  string $source   GA "source" tracking value
 * @param  string $medium   GA "medium" tracking value
 * @param  string $campaign GA "campaign" tracking value
 *
 * @return string $url      Full Google Analytics campaign URL
 */
function pt_ga_campaign_url( $base_url, $source, $medium, $campaign ) {

	// $medium examples: 'sidebar_link', 'banner_image'

	$url = esc_url( add_query_arg( array (
		'utm_source'   => $source,
		'utm_medium'   => $medium,
		'utm_campaign' => $campaign
	), $base_url ) );

	return $url;

}

/**
 * Filters the content to remove any extra paragraph or break tags
 * caused by shortcodes.
 *
 * @since 1.0.0
 *
 * @param  string $content String of HTML content.
 *
 * @return string $content Amended string of HTML content.
 *
 * REF: https://thomasgriffin.io/remove-empty-paragraph-tags-shortcodes-wordpress/
 */
function pt_shortcode_fix( $content ) {

	$array = array (
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']'
	);

	return strtr( $content, $array );

}


add_filter( 'the_content', 'pt_shortcode_fix' );

/**
 * Is WordPress currently on localhost?
 *
 * @since   1.0.0
 * @author  David de Boer
 */
function pt_is_localhost() {

	$whitelist = array ( '127.0.0.1', '::1' );
	if ( in_array( $_SERVER['REMOTE_ADDR'], $whitelist ) ) {
		return true;
	}

}

/**
 * Prefill email field if user is logged in
 *
 * @since   1.1.0
 * @author  David de Boer
 */

function pt_prefill_email() {

	if ( is_user_logged_in() ) {
		$prefill = get_userdata( get_current_user_id() )->user_email;
	} else {
		$prefill = '';
	}

	return $prefill;
}

/**
 * Prefill name field if user is logged in
 *
 * @since   1.5.0
 * @author  David de Boer
 */

function pt_prefill_name() {

	if ( is_user_logged_in() ) {
		$prefill = get_userdata( get_current_user_id() )->user_firstname . ' ' . get_userdata( get_current_user_id() )->user_lastname;
	} else {
		$prefill = '';
	}

	return $prefill;
}

/**
 * Prefill first name field if user is logged in
 *
 * @since   1.5.0
 * @author  David de Boer
 */

function pt_prefill_first_name() {

	if ( is_user_logged_in() ) {
		$prefill = get_userdata( get_current_user_id() )->user_firstname;
	} else {
		$prefill = '';
	}

	return $prefill;
}

/**
 * Prefill last name field if user is logged in
 *
 * @since   1.5.0
 * @author  David de Boer
 */

function pt_prefill_last_name() {

	if ( is_user_logged_in() ) {
		$prefill = get_userdata( get_current_user_id() )->user_lastname;
	} else {
		$prefill = '';
	}

	return $prefill;
}

/**
 * Get a list of payments.
 *
 * Get a list with payments from the database.
 *
 * @since 1.0.0
 *
 * @param  array $args List of WP_Query arguments.
 *
 * @return array       WP_Query result.
 */
function pt_get_payments( $args = array () ) {

	$payment_args = wp_parse_args( $args, array (
		'post_type'     => 'pt_payment',
		'post_status'   => 'publish',
		'post_per_page' => 10,
		'fields'        => 'ids',
	) );

	$posts         = new WP_Query( $payment_args );
	$payment_posts = $posts->get_posts();

	return $payment_posts;

}

/**
 * Check if site has live payments.
 *
 * @since 1.5.0
 *
 * @param  array $args List of WP_Query arguments.
 *
 * @return bool       true or false
 */
function pt_has_live_payments( $args = array () ) {

	$payment_args = wp_parse_args( $args, array (
		'post_type'   => 'pt_payment',
		'post_status' => 'publish',
		'orderby' => 'id',
		'order'   => 'DESC',

		'meta_query' => array (
			array (
				'key'     => '_mode',
				'value'   => 'live',
				'compare' => '='
			)
		),

		'fields'        => 'ids',
		'post_per_page' => 1,
	) );

	$posts = new WP_Query( $payment_args );

	return $posts->have_posts();

}