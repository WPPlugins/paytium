<?php

/**
 * Represents the view for the administration dashboard.
 *
 * @package    PT
 * @subpackage Views
 * @author     David de Boer <david@davdeb.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( 'admin-helper-functions.php' );

$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'keys';
?>

<div class="wrap">
	<?php settings_errors(); ?>
	<div id="pt-settings">
		<div id="pt-settings-content">

			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<h2 class="nav-tab-wrapper">
				<?php

				$pt_tabs = pt_get_admin_tabs();

				foreach ( $pt_tabs as $key => $value ) {
					?>
					<a href="<?php echo esc_url( add_query_arg( 'tab', $key, remove_query_arg( 'settings-updated' ) ) ); ?>"
					   class="nav-tab
							<?php echo $active_tab == $key ? 'nav-tab-active' : ''; ?>"><?php echo $value; ?></a>
					<?php
				}
				?>
			</h2>

			<div id="tab_container">
				<form method="post" action="options.php">
					<?php
					$pt_tabs = pt_get_admin_tabs();

					foreach ( $pt_tabs as $key => $value ) {
						if ( $active_tab == $key ) {
							settings_fields( 'pt_settings_' . $key );
							do_settings_sections( 'pt_settings_' . $key );

							do_action( 'pt_settings_' . $key );

							submit_button();
						}
					}
					?>
				</form>
			</div>
			<!-- #tab_container-->
		</div>
		<!-- #pt-settings-content -->

		<div id="pt-settings-sidebar">
			<?php include( 'admin-sidebar.php' ); ?>
		</div>

	</div>
</div><!-- .wrap -->
