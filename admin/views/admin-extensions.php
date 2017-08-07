<?php

/**
 * Paytium Extensions
 *
 * @package    PT
 * @subpackage Views
 * @author     David de Boer <david@davdeb.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$extensions = array (
	array (
		'title'          => __( 'MailChimp', 'paytium' ),
		'image'          => '1',
		'description'    => __( 'Automatically add emails from your customers/users to your MailChimp lists.', 'paytium' ),
		'form-action-id' => 'q6m3r0',
		'form-id'        => '3155591'
	),
	array (
		'title'          => __( 'MoneyBird', 'paytium' ),
		'image'          => '1',
		'description'    => __( 'Automatically send payments to MoneyBird and create invoices.', 'paytium' ),
		'form-action-id' => 'e2h8t9',
		'form-id'        => '3155585'
	),
	array (
		'title'          => __( 'Exact Online', 'paytium' ),
		'image'          => '1',
		'description'    => __( 'Automatically send payments to your Exact Online administration/invoices.', 'paytium' ),
		'form-action-id' => 'q1m2n6',
		'form-id'        => '3155571'
	),
	array (
		'title'          => __( 'Google Analytics', 'paytium' ),
		'image'          => '1',
		'description'    => __( 'Add Google Analytics eCommerce tracking to Paytium, track goals and more.', 'paytium' ),
		'form-action-id' => 'p3k3r7',
		'form-id'        => '3155565'
	),
	array (
		'title'          => __( 'Invoices', 'paytium' ),
		'image'          => '',
		'description'    => __( 'Automatically create invoices after payments, without other software!', 'paytium' ),
		'form-action-id' => 'g7n7s5',
		'form-id'        => '3973792'
	),
	array (
		'title'          => __( 'Custom text after payment', 'paytium' ),
		'image'          => '',
		'description'    => __( 'Show your own custom messages/posts/pages after payments.', 'paytium' ),
		'form-action-id' => 'g5x4s5',
		'form-id'        => '3155553'
	),
	array (
		'title'          => __( 'Export to PDF or CSV', 'paytium' ),
		'image'          => '',
		'description'    => __( 'Export payments to a PDF or CSV file with just a few clicks.', 'paytium' ),
		'form-action-id' => 'k7n1f3',
		'form-id'        => '3155547'
	),
	array (
		'title'          => __( 'Emails', 'paytium' ),
		'image'          => '',
		'description'    => __( 'Automatically send emails to customers and admins.', 'paytium' ),
		'form-action-id' => 'q9k5r9',
		'form-id'        => '3155541'
	),
	array (
		'title'          => __( 'Statistics and reports', 'paytium' ),
		'image'          => '',
		'description'    => __( 'Get an overview of payments per period, payment methods used, and more!', 'paytium' ),
		'form-action-id' => 'b3z8n2',
		'form-id'        => '3155533'
	),
	array (
		'title'          => __( 'MailPoet Newsletters', 'paytium' ),
		'image'          => '1',
		'description'    => __( 'Automatically add emails from users to MailPoet newsletters.', 'paytium' ),
		'form-action-id' => 'r8q1a9',
		'form-id'        => '3155559'
	),
)
?>

<div class="wrap">
	<div id="pt-extensions">
		<div id="pt-extensions-content">

			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="pt_extensions_wrap">
				<p class="pt_extensions_wrap_intro">
					<?php _e( 'Use this page to show interest in new integrations and features! Other suggestions? Send an email to <a href="mailto:david@paytium.nl">david@paytium.nl</a>.', 'paytium' ); ?>
				</p>

				<ul class="products">

					<?php
					shuffle( $extensions );
					foreach ( $extensions as $extension ) : ?>

						<li class="product">

							<?php if ( ! empty( $extension['image'] ) ) { ?>
								<img
									src=" <?php echo PT_PLUGIN_URL . 'admin/extension_logos/' . str_replace( ' ', '', strtolower( $extension['title'] ) ) . '.png'; ?>"/>
							<?php } else { ?>
								<h2><?php echo $extension['title'] ?></h2>
							<?php } ?>

							<p><?php echo $extension['description'] ?></p>

							<?php include( PT_PATH . 'admin/views/admin-extensions-interest-form.php' ); ?>

						</li>

					<?php endforeach; ?>

				</ul>
			</div>

		</div>
		<!-- .pt-extensions-content -->
	</div>
	<!-- .pt-extensions -->
</div><!-- .wrap -->

<script type="text/javascript"
        src="//static.mailerlite.com/js/w/webforms.min.js?vb01ce49eaf30b563212cfd1f3d202142"></script>
