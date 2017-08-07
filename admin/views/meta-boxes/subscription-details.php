<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


?>

    <div class="submitbox" id="submitpost">
        <div id="minor-publishing">

			<?php
			if ( $payment->subscription == 1 && $payment->subscription_payment_status == 'pending' ) {

				?>
                <div id="misc-publishing-actions">
                    <div class="inside-options">
						<?php

						echo __( 'Subscription not created yet.', 'paytium' );

						?>
                    </div> <!-- END INSIDE OPTIONS -->
                    <div class="clear"></div>
                </div> <!-- END MISC PUBLISHING ACTIONS -->

				<?php

			} elseif ( $payment->subscription == 1 && $payment->subscription_payment_status == 'failed' ) {

				?>
                <div id="misc-publishing-actions">
                    <div class="inside-options">
						<?php

						echo __( 'Creating subscription failed:', 'paytium' ) . '<br />' . strtolower( $payment->subscription_error );

						?>
                    </div> <!-- END INSIDE OPTIONS -->
                    <div class="clear"></div>
                </div> <!-- END MISC PUBLISHING ACTIONS -->
                <?php

			} elseif ( $payment->subscription == 1 && ( $payment->subscription_payment_status == 'active' || 'pending' ) ) {

				try {
					$subscription = $pt_mollie->customers_subscriptions->withParentId( $payment->customer_id )->get( $payment->subscription_id );
					?>

                    <div id="misc-publishing-actions">
                        <div class="inside-options">

                            <div class="option-group-subscription">
                                <label for="claimer">
                                    <?php echo __( 'Status' ) ?>:
                                </label>
                                <span class="option-value" id="option-value-subscription-status">
                                    <?php echo ucfirst( $subscription->status ) ?>
                                </span>
                            </div>

                            <div class="option-group-subscription">
                                <label for="claimer">
                                    <?php echo __( 'Interval' ) ?>:
                                </label>
                                <span  class="option-value">
                                    <?php echo ucfirst( $subscription->interval ) ?>
                                </span>
                            </div>

                            <div class="option-group-subscription">
                                <label for="claimer">
                                    <?php echo __( 'Times' ) ?>:
                                </label>
                                <span class="option-value">
                                    <?php echo ucfirst( $subscription->times ) ?>
                                </span>
                            </div>

                            <div class="option-group-subscription">
                                <label for="claimer">
                                    <?php echo __( 'Amount' ) ?>:
                                </label>
                                <span class="option-value">
                                    <?php echo esc_html( pt_float_amount_to_currency( $subscription->amount ) ) ?>
                                </span>
                            </div>

                            <div class="option-group-subscription">
                                <label for="claimer">
                                    <?php echo __( 'ID' ) ?>:
                                </label>
                                <span class="option-value">
                                    <?php echo $subscription->id ?>
                                </span>
                            </div>

                            <div class="option-group-subscription">
                                <label for="claimer">
			                        <?php echo __( 'Customer' ) ?>:
                                </label>
                                <span class="option-value">
                                    <?php echo $payment->customer_id ?>
                                </span>
                            </div>

                            <div class="option-group-subscription">
                                <label for="claimer">
                                    <?php echo __( 'Created' ) ?>:
                                </label>
                                <span class="option-value">
                                    <?php echo preg_replace( '/T.*/', '', $subscription->createdDatetime ) ?>
                                </span>
                            </div>

							<?php
							$visibility = ( $subscription->cancelledDatetime == null ) ? 'none' : 'block';
							?>

                            <div class="option-group-subscription option-group-subscription-cancelled" style="display:<?php echo $visibility ?>">
                                <label for="claimer">
                                    <?php echo __( 'Cancelled' ) ?>:
                                </label>
                                <span class="option-value option-value-cancelled">
                                    <?php echo preg_replace( '/T.*/', '', $subscription->cancelledDatetime ) ?>
                                </span>
                            </div>

                        </div> <!-- END INSIDE OPTIONS -->
                        <div class="clear"></div>
                    </div> <!-- END MISC PUBLISHING ACTIONS -->

					<?php
					if ( $subscription->status == 'active' ) {
						?>

                        <div id="major-publishing-actions">

                            <div id="publishing-action">
                                <span class="spinner"></span>

                                <input type="hidden" id="payment_id" name="payment_id"
                                       value="<?php echo $payment->id ?>">
                                <input type="hidden" id="subscription_id" name="subscription_id"
                                       value="<?php echo $payment->subscription_id ?>">
                                <input type="hidden" id="customer_id" name="customer_id"
                                       value="<?php echo $payment->customer_id ?>">
                                <input type="submit"
                                       class="button button-secondary button-large paytium-cancel-subscription"
                                       id="paytium-cancel-subscription-button" value="Cancel subscription">
                            </div>
                            <div class="clear"></div>
                        </div>

						<?php
					}
				}
				catch ( Mollie_API_Exception $e ) {

					var_dump( print_r( $e->getMessage() ) );
				}
			}

			?>
        </div>
    </div>
<?php

