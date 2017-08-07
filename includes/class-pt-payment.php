<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Payment class.
 *
 * Payment class is a API for a single payment in Paytium.
 *
 * @class          PT_Payment
 * @version        1.0.0
 * @author         Jeroen Sormani
 */
class PT_Payment {

	/**
	 * @since 1.0.0
	 * @var int Payment amount in cents.
	 */
	public $payment_amount;

	/**
	 * @since 1.0.0
	 * @var string  Payment status slug.
	 */
	public $status;

	/**
	 * @since 1.0.0
	 * @var string Order status.
	 */
	public $order_status;

	/**
	 * @since 1.0.0
	 * @var string  Payment date.
	 */
	public $payment_date;

	/**
	 * @since 1.0.3
	 * @var string  Mollie transaction ID.
	 */
	public $transaction_id;

	/**
	 * @since 1.0.0
	 * @var string Payment method.
	 */
	public $payment_method;

	/**
	 * @since 1.0.0
	 * @var string  Payment description
	 */
	public $description;

	/**
	 * @since 1.3.0
	 * @var string  Subscription
	 */
	public $subscription;

	/**
	 * @since 1.3.0
	 * @var string  Subscription
	 */
	public $subscription_id;

	/**
	 * @since 1.3.0
	 * @var string  Subscription interval
	 */
	public $subscription_interval;

	/**
	 * @since 1.3.0
	 * @var string  Subscription times
	 */
	public $subscription_times;

	/**
	 * @since 1.4.0
	 * @var string  Subscription start date
	 */
	public $subscription_start_date;

	/**
	 * @since 1.4.0
	 * @var string  Subscription payment status
	 */
	public $subscription_payment_status;

	/**
	 * @since 1.4.0
	 * @var string  Subscription webhook
	 */
	public $subscription_webhook;

	/**
	 * @since 1.4.0
	 * @var string  Subscription error
	 */
	public $subscription_error;

	/**
	 * @since 1.3.0
	 * @var string  Customer ID
	 */
	public $customer_id;

	/**
	 * @since 1.3.0
	 * @var string  mode
	 */
	public $mode;

	/**
	 * @since 1.5.0
	 * @var string  no_payment
	 */
	public $no_payment;

	/**
	 * @since 1.1.0
	 * @var string  Field data
	 */
	public $field_data = array();


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id pt_payment Post ID.
	 */
	public function __construct( $post_id ) {

		$this->id = absint( $post_id );

		if ( 'pt_payment' != get_post_type( $this->id ) ) {
			return false;
		}

		$this->populate();
		return null;

	}


	/**
	 * Populate payment.
	 *
	 * Populate the payment class with the related data.
	 *
	 * @since 1.0.0
	 */
	public function populate() {

		$meta = get_post_meta( $this->id, null, true );

		$this->payment_amount = isset( $meta['_amount'] ) ? reset( $meta['_amount'] ) : '';
		$this->status         = isset( $meta['_status'] ) ? reset( $meta['_status'] ) : '';
		$this->order_status   = isset( $meta['_order_status'] ) ? reset( $meta['_order_status'] ) : '';
		// Mollie transaction ID is called "payment_id" in DB, that's not correct, its the transaction ID
		$this->transaction_id = isset( $meta['_payment_id'] ) ? reset( $meta['_payment_id'] ) : '';
		$this->payment_date   = get_post_field( 'post_date', $this->id );
		$this->payment_method = isset( $meta['_method'] ) ? reset( $meta['_method'] ) : '';
		$this->description    = isset( $meta['_description'] ) ? reset( $meta['_description'] ) : '';

		$this->subscription            = isset( $meta['_subscription'] ) ? reset( $meta['_subscription'] ) : '';
		$this->subscription_id         = isset( $meta['_subscription_id'] ) ? reset( $meta['_subscription_id'] ) : '';
		$this->subscription_interval   = isset( $meta['_subscription_interval'] ) ? reset( $meta['_subscription_interval'] ) : '';
		$this->subscription_times      = isset( $meta['_subscription_times'] ) ? reset( $meta['_subscription_times'] ) : '';
		$this->subscription_start_date = isset( $meta['_subscription_start_date'] ) ? reset( $meta['_subscription_start_date'] ) : '';
		$this->subscription_payment_status     = isset( $meta['_subscription_payment_status'] ) ? reset( $meta['_subscription_payment_status'] ) : '';

		$this->subscription_webhook = isset( $meta['_subscription_webhook'] ) ? reset( $meta['_subscription_webhook'] ) : '';
		$this->subscription_error   = isset( $meta['_subscription_error'] ) ? reset( $meta['_subscription_error'] ) : '';

		$this->customer_id = isset( $meta['_pt-customer-id'] ) ? reset( $meta['_pt-customer-id'] ) : '';
		$this->mode        = isset( $meta['_mode'] ) ? reset( $meta['_mode'] ) : '';

		$this->no_payment = isset( $meta['_pt_no_payment'] ) ? reset( $meta['_pt_no_payment'] ) : '';

		$this->field_data = $meta;

	}


	/**
	 * Get the payment amount.
	 *
	 * Get the payment amount in a nice format with decimals, without currency symbol.
	 *
	 * @since 1.0.0
	 *
	 * @return float Payment amount.
	 */
	public function get_amount() {

		return $this->payment_amount;

	}


	/**
	 * Get payment status.
	 *
	 * Get the pretty payment status name.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_status() {

		$statuses = pt_get_payment_statuses();
		$status   = isset( $statuses[ $this->status ] ) ? $statuses[ $this->status ] : $this->status;

		return apply_filters( 'pt_payment_get_status', $status, $this->id );

	}


	/**
	 * Set payment status.
	 *
	 * Set the payment status and update the DB value.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $status New payment status slug.
	 *
	 * @return bool|string         False when the new status is invalid, the new status otherwise.
	 */
	public function set_status( $status ) {

		$statuses = pt_get_payment_statuses();

		if ( isset( $statuses[ $status ] ) ) {
			$this->status = $status;
		} else {
			return false;
		}

		update_post_meta( $this->id, '_status', $this->status );

		// Add a filter here to allow developers to process payment status update as well
		do_action( 'paytium_update_payment_status_from_admin', $this->id );

		return $this->get_status();

	}

	/**
	 * Hook to be called when payment is updated from WordPress admin
	 *
	 * @since 1.4.0
	 */
	public function update_status_from_admin( $payment_id ) {

		// Add a filter here to allow developers to process payment changes from admin as well
		do_action( 'paytium_after_update_payment_from_admin', $payment_id );

		return;

	}


	/**
	 * Get order status.
	 *
	 * Get the order status.
	 *
	 * @since 1.0.0
	 *
	 * @return string Order status.
	 */
	public function get_order_status() {

		$statuses = pt_get_order_statuses();
		$status   = isset( $statuses[ $this->order_status ] ) ? $statuses[ $this->order_status ] : $this->order_status;

		return apply_filters( 'pt_payment_get_order_status', $status, $this->id );

	}


	/**
	 * Set order status.
	 *
	 * Set the order status.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $status New order status.
	 *
	 * @return string         New order status
	 */
	public function set_order_status( $status ) {

		$statuses = pt_get_order_statuses();

		if ( isset( $statuses[ $status ] ) ) {
			$this->order_status = $status;
		} else {
			return false;
		}

		update_post_meta( $this->id, '_order_status', $this->order_status );

		return $this->get_order_status();

	}


	/**
	 * Get payment date.
	 *
	 * Get the formatted payment date.
	 *
	 * @since 1.0.0
	 *
	 * @return string Formatted payment date.
	 */
	public function get_payment_date() {

		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$date_format = apply_filters( 'pt_payment_date_format', $date_format, $this->id );

		return date_i18n( $date_format, strtotime( $this->payment_date ) );

	}

	/**
	 * Get payment id (Mollie transaction ID).
	 *
	 * Get the Mollie transaction ID.
	 *
	 * @since 1.0.3
	 *
	 * @return string Mollie transaction ID.
	 */
	public function get_transaction_id() {


		$this->transaction_id = ! empty( $this->transaction_id ) ? $this->transaction_id : '-';

		return $this->transaction_id;

	}


	/**
	 * Get payment method.
	 *
	 * Get the used payment method for this payment.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_payment_method() {

		return $this->payment_method;

	}


	/**
	 * Set payment method.
	 *
	 * Set the used method for the payment
	 *
	 * @since 1.0.0
	 *
	 * @param  string $payment_method Used payment method.
	 *
	 * @return string                 Used payment method.
	 */
	public function set_payment_method( $payment_method ) {

		$payment_methods = pt_get_payment_methods();
		if ( isset( $payment_methods[ $payment_method ] ) ) {
			$this->payment_method = $payment_method;
		} else {
			return false;
		}

		update_post_meta( $this->id, '_method', $payment_method );

		return $this->get_payment_method();

	}


	/**
	 * Get description.
	 *
	 * Get the description of the payment. This should be something related to the product title for example.
	 *
	 * @since 1.0.0
	 *
	 * @return string Payment description.
	 */
	public function get_description() {

		return apply_filters( 'pt_payment_get_description', $this->description, $this->id );

	}

	/**
	 * Get all field data from fields in html format
	 *
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */

	public function get_field_data_html() {

		$html = '';

		$field_data = array ();

		foreach ( (array) $this->field_data as $key => $value ) {

			// Add fields to custom data
			// Note: Every field has only one label, but two postmeta items in DB
			if ( strstr( $key, '-label' ) ) {
				// Update key/label for fields with user defined label
				$field_key               = ucfirst( str_replace( '-label', '', $key ) );
				$field_data[ $value[0] ] = $this->field_data[ $field_key ];
			}

			// Add customer details fields to custom data
			// If I merge customer details with fields, this can be removed (no users have this live?)
			if ( strstr( $key, 'pt-customer-details-' ) ) {
				$customer_details_key                = ucfirst( str_replace( 'housenumber', 'House number', str_replace( 'pt-customer-details-', '', str_replace( '_', '', $key ) ) ) );
				$field_data[ $customer_details_key ] = $value;
			}

			// Add customer details fields to custom data
			// Old format until June 2016
			if ( strstr( $key, '_customer_details_' ) ) {
				$customer_details_key                = ucfirst( str_replace( 'housenumber', 'House number', str_replace( '_customer_details_', '', str_replace( '_customer_details_fields_', '', $key ) ) ) );
				$field_data[ $customer_details_key ] = $value;
			}

		}

		foreach ( (array) $field_data as $key => $value ) {

			ob_start();
			?>

			<div class='option-group'>

				<label for='claimer'><?php _e( $key, 'paytium' ); ?>:</label>
				<span class="option-value"><?php echo $value[0] ?></span>

			</div>

			<?php
			$html .= ob_get_contents();
			ob_end_clean();
		}

		return $html;

	}

	/**
	 * Get all field data from fields in raw format
	 *
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */

	public function get_field_data_raw() {

		$field_data = array ();

		// Add the post/payment ID to the payment data array
		$field_data[ 'payment-id' ] = array($this->id);

		foreach ( (array) $this->field_data as $key => $value ) {

			$clean_key = '';

			//
			// Remove first _ character
			//
			if ( strstr( $key, '_' ) ) {
				$clean_key = substr( $key, 1 );
			}

			//
			// Add custom tags - create a description without the payment ID at the end
			//
			if (  $key === '_description' ) {
				$field_data[ 'description-without-id' ] = str_replace( ' ' . $this->id, '', $value );
			}

			//
			// Rename a few elements so they are more logical or users
			//
			if (  $key === '_payment_id' ) {
				$clean_key = 'transaction-id';
			}

			if (  $key === '_mode' ) {
				$clean_key = 'payment-mode';
			}

			if (  $key === '_amount' ) {
				$clean_key = 'payment-amount';
			}

			if ( $key === '_status' ) {
				$clean_key                = 'payment-status';
				$this->field_data[ $key ] = array ( strtolower( $this->get_status() ) );
			}

			if (  $key === '_method' ) {
				$clean_key = 'payment-method';
			}

			if (  $key === '_pt-field-amount' ) {
				$clean_key = 'payment-options-selected';
			}

			if (  $key === '_pt-field-amount-label' ) {
				$clean_key = 'payment-options-label';
			}

			//
			// Remove these elements, not needed for users/in emails
			//
			if (  $key === '_edit_lock' ) {
				continue;
			}

			if (  $key === '_payment_key' ) {
				continue;
			}

			if (  $key === '_paytium_emails_last_status' ) {
				continue;
			}

			if (  $key === 'mode' ) {
				continue;
			}

			if (  $key === '_subscription_webhook' ) {
				continue;
			}

			if (  $key === '_pt_emails_last_status' ) {
				continue;
			}

			// TODO: Make pretty version of this status/value and show in emails
			if (  $key === '_subscription_payment_status' ) {
				continue;
			}

			// TODO: show this if value is not empty (so actually error found)?
			if (  $key === '_subscription_error' ) {
				continue;
			}

			if ( strstr( $key, 'pt_cf_' ) ) {
				continue;

			}

			if ( strstr( $key, 'pt-field-edd' ) ) {
				continue;

			}

			if ( strstr( $key, '_pt_mailchimp_error' ) ) {
				continue;

			}

			if ( strstr( $key, '_pt-field-mailchimp-' ) ) {
				continue;

			}

			if ( strstr( $key, '_pt_no_payment' ) ) {
				continue;

			}

			// TODO: Add subscription_renewal which should convert to Yes/No
            // TODO: Translate payment_status tag to pretty/translated names?

			//
			// Add all data to the field data array
			//
			$field_data[ str_replace( '_', '-', $clean_key  ) ] = str_replace( '_', '-', $this->field_data[ $key ] );

		}

		return $field_data;

	}

	/**
	 * Get all customer emails
	 *
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */

	public function get_field_data_customer_emails() {

		$field_data = array ();

		//var_dump($this->field_data);


		foreach ( (array) $this->field_data as $key => $value ) {

			$clean_key = '';

			if ( strstr( $key, '-label' ) ) {
				continue;
			}

			if ( strstr( $key, 'pt-field-email' )) {
				$field_data[] = $value[0];
			}

		}

		return $field_data;

	}


}
