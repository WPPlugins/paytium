<div class="activate-account-wrapper">

	<!-- Loading -->
	<div class="spinner-wrap" style="display: none;">
		<div class="spinner is-active" style="float: none;"></div>
		<span class="loading-text"><?php _e( 'Checking profile status', 'paytium' ); ?></span>
	</div>


	<!-- Not verified -->
	<div id="profile-not-verified" style="display: none;">
		<div class="pt-alert pt-alert-danger">
			<span
				class="dashicons dashicons-warning paytium-activate-account-failed-warning"></span>
            <?php _e( 'Looks like your Mollie account and/or website profile isn\'t enabled completely.', 'paytium' ); ?>
            <br />
			<?php _e( 'To see what\'s wrong go to', 'paytium' ); ?>
			<a href='https://www.mollie.com/nl/signup/335035' target='_blank'>mollie.com</a>!
		</div>
		<div style="text-align: center; margin-top: 10px;">
			<a href="javascript:void(0);" id="check-profile-status" class="button button-primary"
			   target="_blank"><?php _e( 'Re-check', 'paytium' ); ?></a>&nbsp;
		</div>
	</div>

	<!-- Verified -->
	<div id="profile-verified" style="display: none;">
		<div class="pt-alert pt-alert-success">
			<span class="dashicons dashicons-yes"></span><?php _e( 'Success!', 'paytium' ); ?>
			<br/><?php _e( 'Your account is active!', 'paytium' ); ?>
		</div>

		<div style="text-align: center; margin-top: 10px;">
			<a href="javascript:void(0);" class="button button-secondary tab-button"
			   data-target="first-product"><?php _e( 'Continue to next step', 'paytium' ); ?> &rarr;</a>
		</div>

	</div>

</div>
