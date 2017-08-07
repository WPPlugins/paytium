<div class="pt-alert pt-alert-danger">
	<span class="dashicons dashicons-warning paytium-create-profile-warning"></span>
	<?php _e( 'If you have created a new Mollie account in the previous step, you will have received an email from Mollie. You should click the activation URL in that email, otherwise you can not continue!', 'paytium' ); ?>
</div>

<div id="create-profile" class="boxed">

	<h3><?php _e( 'Create a website profile', 'paytium' ); ?></h3>

	<p><?php _e( 'For every website you want to receive payments on, you will need to create a website profile.', 'paytium' ); ?></p>

	<p><?php _e( 'Edit or complete the below details and create your website profile!', 'paytium' ); ?></p>

	<div class="ajax-response"></div>

	<form method="">
		<div class="form-group">
			<label><?php _e( 'Name', 'paytium' );
				?>*<input type="text" name="name" class="" value="<?php echo get_bloginfo( 'name' ); ?>">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Website', 'paytium' );
				?>*<input type="text" name="website" class="" value="<?php echo site_url(); ?>">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Email', 'paytium' );
				?>*<input type="text" name="email" class="" value="<?php echo get_option( 'admin_email' ); ?>">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Phone', 'paytium' );
				?>*<input type="text" name="phone" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Category', 'paytium' ); ?>*
				<select name="category">
					<option value=""></option>
					<option value="5399"><?php _e( 'Physical products', 'paytium' ); ?></option>
					<option value="5732"><?php _e( 'Electronics, computers and software', 'paytium' ); ?></option>
					<option value="4121"><?php _e( 'Travel, rent, transport', 'paytium' ); ?></option>
					<option value="6012"><?php _e( 'Financial services', 'paytium' ); ?></option>
					<option value="5499"><?php _e( 'Food & drinks', 'paytium' ); ?></option>
					<option value="7999"><?php _e( 'Events, festivals and recreation', 'paytium' ); ?></option>
					<option value="5192"><?php _e( 'Books, magazines and papers', 'paytium' ); ?></option>
					<option value="7299"><?php _e( 'Physical labor', 'paytium' ); ?></option>
					<option value="8398"><?php _e( 'Non profits and donations', 'paytium' ); ?></option>
					<option value="0"><?php _e( 'Others', 'paytium' ); ?></option>
				</select>
			</label>
		</div>

		<a href="javascript:void(0);" id="create-mollie-profile" class="button button-primary"
		   style="margin-top: 10px;"><?php _e( 'Create website profile', 'paytium' ); ?></a>

		<div class="spinner" style="margin-top: 14px; float: none;"></div>

	</form>

	<a href="javascript:void(0);" class="button button-primary continue-button tab-button" data-target="payment-test"
	   style="display: none;"><?php _e( 'Go to the next step', 'paytium' ); ?> &rarr;</a>

</div>
