<h3><?php _e( 'Do a test payment', 'paytium' ); ?></h3>
<p><?php _e( 'You can use this payment button to make a test payment', 'paytium' ); ?></p>

<div class="ajax-response"></div>

<div class="spinner-wrap" style="display: none;">
	<div class="spinner is-active" style="float: none;"></div>
	<span class="loading-text"><?php _e( 'Checking if payment exists', 'paytium' ); ?></span>
</div>

<div class="boxed"><?php
	echo do_shortcode( '[paytium name="Webwinkel XYZ" description="Test betaling" amount="49,95"][paytium_total][/paytium]' );
	?></div>

<div style="text-align: center; margin-top: 10px;">
	<a href="javascript:void(0);" class="button button-primary"
	   id="check-payment"><?php _e( 'Validate test payment', 'paytium' ); ?></a>&nbsp;
	<a href="javascript:void(0);" class="button button-secondary tab-button"
	   data-target="activate-account"><?php _e( 'Continue to next step', 'paytium' ); ?> &rarr;</a>
</div>
