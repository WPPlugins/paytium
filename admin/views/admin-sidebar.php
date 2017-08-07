<?php

/**
 * Sidebar portion of the administration dashboard view.
 *
 * @package    PT
 * @subpackage views
 * @author     David de Boer <david@davdeb.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- BASIC -->

<div class="sidebar-container metabox-holder">
	<div class="postbox">
		<h3 class="wp-ui-primary"><span><?php _e( 'iDEAL payments - the easy way', 'paytium' ); ?></span></h3>

		<div class="inside">
			<div class="main">
				<p class="last-blurb centered">
					<?php _e( 'A few things you can easily do with Paytium', 'paytium' ); ?>
				</p>

				<ul>
					<li>
						<div
							class="dashicons dashicons-yes"></div> <?php _e( 'Accept payments via your site', 'paytium' ); ?>
					</li>
					<li>
						<div
							class="dashicons dashicons-yes"></div> <?php _e( 'Sell e-books, or real books', 'paytium' ); ?>
					</li>
					<li>
						<div
							class="dashicons dashicons-yes"></div> <?php _e( 'Collect donations for a charity', 'paytium' ); ?>
					</li>
					<li>
						<div
							class="dashicons dashicons-yes"></div> <?php _e( 'Ask customers to pay their invoice', 'paytium' ); ?>
					</li>
					<li>
						<div
							class="dashicons dashicons-yes"></div> <?php _e( 'Let customers enter a custom amount', 'paytium' ); ?>
					</li>
				</ul>
				<!--
				<div class="centered">
					<a href="<?php echo pt_ga_campaign_url( PT_WEBSITE_BASE_URL, 'paytium', 'sidebar_link', 'docs' ); ?>"
						class="button-primary button-large" target="_blank">
						<?php _e( 'View the documentation', 'paytium' ); ?></a>
				</div>
				-->
			</div>
		</div>
	</div>
</div>

<div class="sidebar-container metabox-holder">
	<div class="postbox">
		<h3 class="wp-ui-primary"><span><?php _e( 'Need help or advice?', 'paytium' ); ?></span></h3>

		<div class="inside">
			<p>
				<?php _e( 'Email me for advice or with questions:', 'paytium' ); ?>
			</p>

			<div class="centered">
				<a href="mailto:david@paytium.nl" class="button-primary" target="_blank">
					<?php _e( 'david@paytium.nl', 'paytium' ); ?></a>
			</div>
		</div>
	</div>
</div>
<!--
<div class="sidebar-container metabox-holder">
	<div class="postbox">
		<div class="inside">
			<p>
				<?php _e( 'Your review helps more folks find our plugin. Thanks so much!', 'paytium' ); ?>
			</p>
			<div class="centered">
				<a href="https://wordpress.org/support/view/plugin-reviews/paytium#postform" class="button-primary" target="_blank">
					<?php _e( 'Review this Plugin Now', 'paytium' ); ?></a>
			</div>
		</div>
	</div>
</div>
-->
<div class="sidebar-container metabox-holder">
	<div class="postbox">
		<div class="inside">
			<ul>
				<!--
				<li>
					<div class="dashicons dashicons-arrow-right-alt2"></div>
					<a href="<?php echo pt_ga_campaign_url( PT_WEBSITE_BASE_URL . 'handleiding/', 'paytium', 'sidebar_link', 'docs' ); ?>" target="_blank">
						<?php _e( 'Documentation', 'paytium' ); ?></a>
				</li>
				-->

				<li>
					<div class="dashicons dashicons-arrow-right-alt2"></div>
					<a href="https://wordpress.org/support/plugin/paytium" target="_blank">
						<?php _e( 'Support forums', 'paytium' ); ?></a>
				</li>

				<li>
					<div class="dashicons dashicons-arrow-right-alt2"></div>
					<a href="https://www.mollie.com/nl/signup/335035" target="_blank">
						<?php _e( 'Mollie', 'paytium' ); ?></a>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="sidebar-container metabox-holder">
	<div class="postbox-nobg">
		<div class="inside centered">
			<a href="https://www.mollie.com/nl/signup/335035" target="_blank">
				<img src="<?php echo PT_PLUGIN_URL; ?>assets/mollie-119x26.png"/>
			</a>
		</div>
	</div>
</div>
