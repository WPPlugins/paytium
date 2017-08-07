<p><b><?php _e( 'Who or what is Mollie?', 'paytium' ); ?></b>
	- <?php _e( 'The easiest way to receive iDEAL payments is with a Mollie account. Mollie is a certified payments specialist, processes your payments and sends them toi your bank account on a daily basis.', 'paytium' ); ?>
</p>


<p><b><?php _e( 'What should I do?', 'paytium' ); ?></b>
	- <?php _e( 'Create a new Mollie account or connect an existing one to this website.', 'paytium' ); ?></p>

<div style="text-align: center; margin-bottom: 10px;">
	<a href="javascript:void(0);" class="button button-secondary target-button"
	   data-target="have-mollie-account"><?php _e( 'I have a Mollie account', 'paytium' ); ?></a>&nbsp;
	<a href="javascript:void(0);" class="button button-secondary target-button"
	   data-target="no-mollie-account"><?php _e( 'I don\'t have a Mollie account', 'paytium' ); ?></a>
</div>

<div id="have-mollie-account" class="boxed target-area" style="display: none;">

	<h3><?php _e( 'Login with Mollie', 'paytium' ); ?></h3>

	<p><?php _e( 'Your details will be sent to Mollie over a secure and encrypted connection!', 'paytium' ); ?></p>

	<div class="ajax-response"></div>

	<form method="">

		<div class="form-group">
			<label><?php _e( 'Username', 'paytium' );
				?><input type="text" name="username" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Password', 'paytium' );
				?><input type="password" name="password" class="">
			</label>
		</div>
		<a href="javascript:void(0);" id="login-mollie" class="button button-primary"
		   style="margin-top: 10px;"><?php _e( 'Continue', 'paytium' ); ?></a>

		<div class="spinner" style="margin-top: 14px; float: none;"></div>

	</form>

	<a href="javascript:void(0);" class="button button-primary continue-button tab-button" data-target="create-profile"
	   style="display: none;"><?php _e( 'Go to the next step', 'paytium' ); ?> &rarr;</a>

</div>

<div id="no-mollie-account" class="boxed target-area" style="display: none;">

	<h3><?php _e( 'Register with Mollie', 'paytium' ); ?></h3>

	<p><?php _e( 'Your details will be sent to Mollie over a secure and encrypted connection!', 'paytium' ); ?></p>

	<div class="ajax-response"></div>

	<form method="">

		<div class="form-group">
			<label><?php _e( 'Username', 'paytium' );
				?>*<input type="text" name="username" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Name', 'paytium' );
				?>*<input type="text" name="name" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Company name', 'paytium' );
				?>*<input type="text" name="company_name" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Email', 'paytium' );
				?>*<input type="text" name="email" class="" value="<?php echo get_option( 'admin_email' ); ?>">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Address', 'paytium' );
				?>*<input type="text" name="address" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Zip code', 'paytium' );
				?>*<input type="text" name="zipcode" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'City', 'paytium' );
				?>*<input type="text" name="city" class="">
			</label>
		</div>
		<div class="form-group">
			<label><?php _e( 'Country', 'paytium' );
				?>*<select name="country">
					<option value="NL"><?php _e( 'Netherlands', 'paytium' ); ?></option>
					<option value="BE"><?php _e( 'Belgium', 'paytium' ); ?></option>
				</select>
			</label>
		</div>
		<a href="javascript:void(0);" id="create-mollie-account" class="button button-primary"
		   style="margin-top: 10px;"><?php _e( 'Continue', 'paytium' ); ?></a>

		<div class="spinner" style="margin-top: 14px; float: none;"></div>

	</form>

	<a href="javascript:void(0);" class="button button-primary continue-button tab-button" data-target="create-profile"
	   style="display: none;"><?php _e( 'Go to the next step', 'paytium' ); ?> &rarr;</a>

</div>
