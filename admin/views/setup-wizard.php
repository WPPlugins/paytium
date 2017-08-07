<div class="wrap">

	<h1 style="margin-bottom: 10px;"><?php _e( 'Paytium setup wizard', 'paytium' ); ?></h1>

	<?php $current_step = isset( $_GET['step'] ) ? $_GET['step'] : 'connect-mollie'; ?>
	<div id='pt-setup-wizard'>

		<div class='tabs-panels-wrap'>
			<div class='tabs'>
				<ul>
					<li class="<?php echo $current_step == 'connect-mollie' ? 'active' : ''; ?>">
						<a href='javascript:void(0);'
						   data-target='connect-mollie'><?php _e( '1. Connect your Mollie account', 'paytium' ); ?></a>
					</li>
					<li class="<?php echo $current_step == 'create-profile' ? 'active' : ''; ?>">
						<a href='javascript:void(0);'
						   data-target='create-profile'><?php _e( '2. Create a website profile', 'paytium' ); ?></a>
					</li>

					<li class="<?php echo $current_step == 'payment-test' ? 'active' : ''; ?>">
						<a href='javascript:void(0);'
						   data-target='payment-test'><?php _e( '3. Payment test', 'paytium' ); ?></a>
					</li>
					<li class="<?php echo $current_step == 'activate-account' ? 'active' : ''; ?>">
						<a href='javascript:void(0);'
						   data-target='activate-account'><?php _e( '4. Activate account', 'paytium' ); ?></a>
					</li>
					<li class="<?php echo $current_step == 'first-product' ? 'active' : ''; ?>">
						<a href='javascript:void(0);'
						   data-target='first-product'><?php _e( '5. Create your first product', 'paytium' ); ?></a>
					</li>
				</ul>
			</div>

			<div class='panels'>

				<div id='connect-mollie' class='panel'
				     style='<?php echo $current_step != 'connect-mollie' ? 'display: none;' : ''; ?>'><?php
					require_once PT_PATH . 'admin/views/setup-wizard/connect-mollie.php';
					?></div>
				<div id='create-profile' class='panel'
				     style='<?php echo $current_step != 'create-profile' ? 'display: none;' : ''; ?>'><?php
					require_once PT_PATH . 'admin/views/setup-wizard/create-profile.php';
					?></div>

				<div id='payment-test' class='panel'
				     style='<?php echo $current_step != 'payment-test' ? 'display: none;' : ''; ?>'><?php
					require_once PT_PATH . 'admin/views/setup-wizard/payment-test.php';
					?></div>
				<div id='activate-account' class='panel'
				     style='<?php echo $current_step != 'activate-account' ? 'display: none;' : ''; ?>'><?php
					require_once PT_PATH . 'admin/views/setup-wizard/activate-account.php';
					?></div>
				<div id='first-product' class='panel'
				     style='<?php echo $current_step != 'first-product' ? 'display: none;' : ''; ?>'><?php
					require_once PT_PATH . 'admin/views/setup-wizard/first-product.php';
					?></div>

			</div>

			<div class='clear'></div>
		</div>

	</div>

</div>
