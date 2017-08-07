<?php

/**
 * Main Paytium class
 *
 * @package PT
 * @author  David de Boer <david@davdeb.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Paytium {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'paytium';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	public $session;

	/**
	 * @since 1.0.0
	 * @var $api PT_API API class.
	 */
	public $api;

	/**
	 * @since 1.0.0
	 * @var $post_types  PT_Post_Types class.
	 */
	public $post_types;


	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'plugins_loaded', array ( $this, 'plugin_textdomain' ) );

		// Include required files.
		$this->setup_constants();

		// Set current version
		$this->version = PT_VERSION;

		add_action( 'init', array ( $this, 'includes' ), 1 );

		// Add the options page and menu item.
		add_action( 'admin_menu', array ( $this, 'add_plugin_admin_menu' ), 10 );

		// Enqueue admin styles.
		add_action( 'admin_enqueue_scripts', array ( $this, 'enqueue_admin_styles' ) );

		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array ( $this, 'enqueue_admin_scripts' ) );

		// Add admin notice after plugin activation, tip to use Setup Wizard. Also check if should be hidden.
		add_action( 'admin_notices', array ( $this, 'admin_notice_setup_wizard' ) );

		// Add admin notice that asks for newsletter sign-up. Also check if should be hidden.
		add_action( 'admin_notices', array ( $this, 'admin_notice_newsletter' ) );

		// Add admin notice that reminds users to view extensions page. Also check if should be hidden.
		add_action( 'admin_notices', array ( $this, 'admin_notice_extensions' ) );

		// Add admin notice when site already received live payments & completing the Setup Wizard is not necessary
		add_action( 'admin_notices', array ( $this, 'admin_notice_has_live_payments' ) );

		// Add plugin listing "Settings" action link.
		add_filter( 'plugin_action_links_' . plugin_basename( PT_PATH . $this->plugin_slug . '.php' ), array (
			$this,
			'paytium_action_links'
		) );

		// Check WP version
		add_action( 'admin_init', array ( $this, 'check_wp_version' ) );

		// Add public JS
		add_action( 'wp_loaded', array ( $this, 'enqueue_public_scripts' ) );

		// Add public CSS
		add_action( 'wp_loaded', array ( $this, 'enqueue_public_styles' ) );

		// Hook into wp_footer so we can localize our script AFTER all the shortcodes have been processed
		add_action( 'wp_footer', array ( $this, 'localize_shortcode_script' ) );

		// Load scripts when posts load so we know if we need to include them or not
		add_filter( 'the_posts', array ( $this, 'load_scripts' ) );

		// Paytium TinyMCE button
		add_action( 'init', array ( $this, 'paytium_add_mce_button' ) );
		add_action( 'admin_enqueue_scripts', array ( $this, 'paytium_mce_css' ) );

	}


	function load_scripts( $posts ) {

		if ( empty( $posts ) ) {
			return $posts;
		}

		foreach ( $posts as $post ) {
			if ( ( strpos( $post->post_content, '[paytium' ) !== false ) || true == get_option( 'paytium_always_enqueue' ) ) {
				// Load CSS
				wp_enqueue_style( $this->plugin_slug . '-public' );

				// Load JS
				wp_enqueue_script( $this->plugin_slug . '-public' );

				// Localize the site script with new language strings
				wp_localize_script( $this->plugin_slug . '-public', 'paytium_localize_script_vars', array (
						'amount_too_low' => __( 'No amount entered or amount is too low!', 'paytium' ),
						'no_valid_email' => __( 'The email address entered in \'%s\' is incorrect!', 'paytium' ),
						'field_is_required' => __( 'Field \'%s\' is required!', 'paytium' ),
						'processing_please_wait' => __( 'Processing, please wait...', 'paytium' ),
					)
				);

				break;
			}
		}

		return $posts;

	}

	/**
	 * Load public facing CSS
	 *
	 * @since 1.0.0
	 */
	function enqueue_public_styles() {

		wp_register_style( 'pikaday', PT_URL . 'public/css/pikaday.css', array (), $this->version );

		wp_register_style( $this->plugin_slug . '-public', PT_URL . 'public/css/public.css', array (), $this->version );

	}

	/**
	 * Find user's browser language
	 *
	 * @since 1.1.0
	 */

	function paytium_browser_language() {

		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {

			$languages = explode( ",", $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
			$language  = strtolower( $languages[0] );
		} else {
			$language = '';
		}

		return $language;
	}

	/**
	 * Load scripts based on environment and language
	 *
	 * @since 1.1.0
	 *
	 * @param $environment
	 * @param $language
	 */

	function paytium_register_scripts( $environment, $language ) {

		if ( ( $environment == 'development' && $language != 'nl' ) ) {

			wp_register_script( $this->plugin_slug . '-public', PT_URL . 'public/js/public.js', array (
				'jquery',
				'parsley',
				'moment',
				'pikaday',
				'pikaday-jquery'
			), time(), true );

			return;

		} elseif ( ( $environment == 'production' && $language != 'nl' ) ) {
			wp_register_script( $this->plugin_slug . '-public', PT_URL . 'public/js/public.js', array (
				'jquery',
				'parsley',
				'moment',
				'pikaday',
				'pikaday-jquery'
			), $this->version, true );

			return;

		} elseif ( ( $environment == 'development' && $language == 'nl' ) ) {
			wp_register_script( $this->plugin_slug . '-public', PT_URL . 'public/js/public.js', array (
				'jquery',
				'parsley',
				'parsley-nl',
				'moment',
				'pikaday',
				'pikaday-jquery'
			), time(), true );

			return;

		} elseif ( ( $environment == 'production' && $language == 'nl' ) ) {
			wp_register_script( $this->plugin_slug . '-public', PT_URL . 'public/js/public.js', array (
				'jquery',
				'parsley',
				'parsley-nl',
				'moment',
				'pikaday',
				'pikaday-jquery'
			), $this->version, true );

			return;
		}
	}

	/**
	 * Load public facing JS
	 *
	 * @since 1.0.0
	 */
	public function enqueue_public_scripts() {

		// Register Parsley JS validation library.
		wp_register_script( 'parsley', PT_URL . 'public/js/parsley.min.js', array ( 'jquery' ), $this->version, true );
		wp_register_script( 'parsley-nl', PT_URL . 'public/js/parsley-nl.js', array ( 'jquery' ), $this->version, true );
		wp_register_script( 'moment', PT_URL . 'public/js/moment.min.js', array (), $this->version, true );
		wp_register_script( 'pikaday', PT_URL . 'public/js/pikaday.js', array ( 'moment' ), $this->version, true );
		wp_register_script( 'pikaday-jquery', PT_URL . 'public/js/pikaday.jquery.js', array (
			'jquery',
			'pikaday'
		), $this->version, true );

		// What's the user's browser language?
		$language = $this->paytium_browser_language();

		// Is this Paytium plugin on a production or development site?
		$environment = get_option( 'pt_environment', 'production' );

		if ( $language == 'nl' || $language == 'nl-nl' || $language == 'nl-be' ) {

			$this->paytium_register_scripts( $environment, 'nl' );

		} else {

			$this->paytium_register_scripts( $environment, '' );

		}

		wp_localize_script( $this->plugin_slug . '-public', 'pt_coup', array ( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	}


	/**
	 * Function to localize the script variables being sent from the shortcodes
	 *
	 * @since 1.0.0
	 */
	function localize_shortcode_script() {

		global $script_vars;

		// Add script debug flag to JS.
		$script_vars['script_debug'] = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );

		wp_localize_script( $this->plugin_slug . '-public', 'pt_script', $script_vars );

		// clear it out after we use it
		$script_vars = array ();

	}


	/**
	 * Load admin scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts() {

		if ( $this->viewing_this_plugin() ) {
			wp_enqueue_script( $this->plugin_slug . '-admin', PT_URL . 'admin/js/admin.js', array ( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'paytium-setup-wizard', PT_URL . 'admin/js/setup-wizard.js', array ( 'jquery' ), $this->version, true );

			wp_localize_script( 'paytium-setup-wizard', 'paytium', array (
				'nonce' => wp_create_nonce( 'paytium-ajax-nonce' ),
			) );
		}

	}


	/**
	 * Enqueue admin-specific style sheets for this plugin's admin pages only.
	 *
	 * @since     1.0.0
	 */
	public function enqueue_admin_styles() {

		if ( $this->viewing_this_plugin() ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', PT_URL . 'admin/css/admin.css', array (), $this->version );
			wp_enqueue_style( $this->plugin_slug . '-toggle-switch', PT_URL . 'admin/css/toggle-switch.css', array (), $this->version );
		}


		if ( get_current_screen()->post_type == 'pt_payment' ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-notice-newsletter-style', PT_URL . 'admin/css/admin-notice-newsletter.css', array (), $this->version );

		}

		if ( get_current_screen()->base == 'paytium_page_pt-extensions' ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-extensions', PT_URL . 'admin/css/admin-extensions.css', array (), $this->version );
		}

	}


	/**
	 * Make sure user has the minimum required version of WordPress installed to use the plugin
	 *
	 * @since 1.0.0
	 */
	public function check_wp_version() {

		global $wp_version;
		$required_wp_version = '3.6.1';

		if ( version_compare( $wp_version, $required_wp_version, '<' ) ) {
			deactivate_plugins( PT_MAIN_FILE );
			wp_die( sprintf( __( $this->get_plugin_title() . ' requires WordPress version <strong>' . $required_wp_version . '</strong> to run properly. ' .
			                     'Please update WordPress before reactivating this plugin. <a href="%s">Return to Plugins</a>.', 'paytium' ), get_admin_url( '', 'plugins.php' ) ) );
		}

	}


	/**
	 * Setup any plugin constants we need
	 *
	 * @since    1.0.0
	 */
	public function setup_constants() {

		// Plugin slug.
		if ( ! defined( 'PT_PLUGIN_SLUG' ) ) {
			define( 'PT_PLUGIN_SLUG', $this->plugin_slug );
		}

		// Plugin folder URL.
		if ( ! defined( 'PT_PLUGIN_URL' ) ) {
			define( 'PT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin directory
		if ( ! defined( 'PT_PLUGIN_DIR' ) ) {
			define( 'PT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin version
		if ( ! defined( 'PT_PLUGIN_VERSION' ) ) {
			define( 'PT_PLUGIN_VERSION', $this->version );
		}

		// Website for this plugin
		if ( ! defined( 'PT_WEBSITE_BASE_URL' ) ) {
			define( 'PT_WEBSITE_BASE_URL', 'http://www.paytium.nl/' );
		}

	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function plugin_textdomain() {

		load_plugin_textdomain(
			'paytium',
			false,
			dirname( plugin_basename( PT_MAIN_FILE ) ) . '/languages/'
		);

	}


	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}


	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Add value to indicate that we should show admin notice for setup wizard.
		update_option( 'pt_show_admin_notice_setup_wizard', 1 );

		// Add value to indicate that we should show admin notice for newsletter.
		update_option( 'pt_show_admin_notice_newsletter', 1 );

		// Add value to indicate that we should show admin notice for extensions.
		update_option( 'pt_show_admin_notice_extensions', 1 );

		if ( ! function_exists( 'curl_version' ) ) {
			wp_die( sprintf( __( 'You must have the cURL extension enabled in order to run %s. Please enable cURL and try again. <a href="%s">Return to Plugins</a>.', 'paytium' ),
				self::get_plugin_title(), get_admin_url( '', 'plugins.php' ) ) );
		}

		// TODO - David: do I want to warn now?
		//		if(pt_is_localhost()) {
		//			echo "Payments via Mollie can't be tested and will not work on local servers (localhost/127.0.0.1)!";
		//		};

	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix[] = add_menu_page(
			$this->get_plugin_title() . ' ' . __( 'Settings', 'paytium' ),
			$this->get_plugin_title(),
			'manage_options',
			$this->plugin_slug,
			array ( $this, 'display_plugin_admin_page' ),
			plugins_url( '/assets/ideal-16x16.png', __FILE__ )
		);

		// Settings page
		add_submenu_page( 'paytium', __( 'Paytium settings', 'paytium' ), __( 'Settings', 'paytium' ), 'manage_options', 'paytium', array (
			$this,
			'display_plugin_admin_page'
		) );

		// Setup wizard
		if ( false == get_option( 'paytium_enable_live_key' ) ) {
			add_submenu_page( 'paytium', 'Setup wizard', __( 'Setup wizard', 'paytium' ), 'manage_options', 'pt-setup-wizard', array (
				$this,
				'setup_wizard_page'
			) );
		}

		// Extensions
		add_submenu_page( 'paytium', 'Extensions', __( 'Extensions', 'paytium' ), 'manage_options', 'pt-extensions', array (
			$this,
			'paytium_extensions_page'
		) );

	}


	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		include_once( PT_PATH . 'admin/views/admin-settings.php' );

	}


	/**
	 * Setup wizard admin page.
	 *
	 * Display the setup wizard on a separate page for the best UX.
	 *
	 * @since 1.0.0
	 */
	public function setup_wizard_page() {

		require_once PT_PATH . 'admin/views/setup-wizard.php';

	}

	/**
	 * Extensions admin page.
	 *
	 * Display extensions for Paytium.
	 *
	 * @since 1.2.0
	 */
	public function paytium_extensions_page() {

		require_once PT_PATH . 'admin/views/admin-extensions.php';

	}


	/**
	 * Include required files (admin and frontend).
	 *
	 * @since     1.0.0
	 */
	public function includes() {

		global $pt_mollie;
		// TODO David Experiment, what does "global" do?

		// TODO Check for curl -- function_exists( 'curl_version' )
		// TODO Check for PHP 5.3.3 (or whatever Mollie API currently requires).

		if ( ! class_exists( 'Mollie_API_Client' ) ) { // TODO David - is Mollie class name correct?
			require_once( PT_PATH . 'libraries/Mollie/API/Autoloader.php' );
		}

		$pt_mollie = new Mollie_API_Client;

		/**
		 * Include functions
		 */
		include_once( PT_PATH . 'includes/misc-functions.php' );

		include_once( PT_PATH . 'includes/process-payment-functions.php' );
		include_once( PT_PATH . 'includes/webhook-url-functions.php' );
		include_once( PT_PATH . 'includes/redirect-url-functions.php' );

		include_once( PT_PATH . 'includes/shortcodes.php' );
		include_once( PT_PATH . 'includes/register-settings.php' );
		include_once( PT_PATH . 'includes/payment-functions.php' );

		// Include classes
		include_once( PT_PATH . 'includes/class-pt-payment.php' );

		/**
		 * Post types class
		 */
		include_once( PT_PATH . 'includes/class-pt-post-types.php' );
		$this->post_types = new PT_Post_Types();

		/**
		 * Include all Plus/Pro/Premium features included in /features
		 * Keep this code in! Also required for development!
		 */
		if ( glob( PT_PATH . 'features/*.php') != FALSE ) {
			foreach ( glob( PT_PATH . 'features/*.php' ) as $filename ) {
				include_once( PT_PATH . 'features/' . basename( $filename ) );
			}
		}

		/**
		 * Admin includes
		 */
		if ( is_admin() ) {
			require_once PT_PATH . 'admin/class-pt-admin.php';
			$this->admin = new PT_Admin();

			require_once PT_PATH . 'includes/class-pt-api.php';
			$this->api = new PT_API();

		}

	}


	/**
	 * Return localized base plugin title.
	 *
	 * @since     1.0.0
	 *
	 * @return string
	 */
	public static function get_plugin_title() {

		return __( 'Paytium', 'paytium' );

	}


	/**
	 * Add Settings action link to left of existing action links on plugin listing page.
	 *
	 * @since   1.0.0
	 *
	 * @param  array $links Default plugin action links
	 *
	 * @return array $links Amended plugin action links
	 */
	public function paytium_action_links( $links ) {

		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=pt-setup-wizard' ) ) . '">' . __( 'Setup wizard', 'paytium' ) . '</a>';
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=paytium' ) ) . '">' . __( 'Settings', 'paytium' ) . '</a>';

		return $links;

	}


	/**
	 * Check if viewing this plugin's admin page.
	 *
	 * @since   1.0.0
	 *
	 * @return bool
	 */
	private function viewing_this_plugin() {

		$screen = get_current_screen();

		if ( ! empty( $this->plugin_screen_hook_suffix ) && in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {
			return true;
		}

		if ( 'paytium_page_pt-setup-wizard' == $screen->id ) {
			return true;
		}

		if ( 'pt_payment' == get_post_type() || ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'pt_payment' ) ) {
			return true;
		}

		return false;

	}


	/**
	 * Show notice after plugin install/activate in admin dashboard.
	 * Hide after first viewing.
	 *
	 * @since   1.0.0
	 */
	public function admin_notice_setup_wizard() {

		// Exit all of this is stored value is false/0 or not set.
		if ( true == get_option( 'paytium_enable_live_key' ) ) {
			return;
		}

		// Exit all of this is stored value is false/0 or not set.
		if ( false == get_option( 'pt_show_admin_notice_setup_wizard' ) ) {
			return;
		}

		// Delete stored value if "hide" button click detected (custom querystring value set to 1).
		if ( ! empty( $_REQUEST['pt-dismiss-install-nag'] ) ) {
			delete_option( 'pt_show_admin_notice_setup_wizard' );

			return;
		}

		// At this point show install notice. Show it only on the plugin screen.
		if ( get_current_screen()->id == 'plugins' ) {
			include_once( PT_PATH . 'admin/views/admin-notice-setup-wizard.php' );
		}

	}

	/**
	 * Use admin notice to ask for newsletter sign-up.
	 * Hide after first viewing.
	 *
	 * @since   1.2.0
	 */
	public function admin_notice_newsletter() {

		// Exit all of this is stored value is false/0 or not set.
		if ( false == get_option( 'pt_show_admin_notice_newsletter' ) ) {
			return;
		}

		// Delete stored value if "hide" button click detected (custom querystring value set to 1).
		if ( ! empty( $_REQUEST['pt-dismiss-newsletter-nag'] ) ) {
			delete_option( 'pt_show_admin_notice_newsletter' );
			set_transient( 'pt_wait_to_show_admin_notice_extensions', '1', '30' );
			return;
		}

		// At this point show newsletter notice.
		if ( get_current_screen()->post_type == 'pt_payment' ) {
			include_once( PT_PATH . 'admin/views/admin-notice-newsletter.php' );
		}
	}

	/**
	 * Use admin notice to remind users to view extensions page.
	 * Hide after first viewing.
	 *
	 * @since   1.2.0
	 */
	public function admin_notice_extensions() {

		// Exit all of this is stored value is false/0 or not set.
		if ( false == get_option( 'pt_show_admin_notice_extensions' ) ) {
			return;
		}

		// Delete stored value if "hide" button click detected (custom querystring value set to 1).
		if ( ! empty( $_REQUEST['pt-dismiss-extensions-nag'] ) ) {
			delete_option( 'pt_show_admin_notice_extensions' );

			return;
		}

		// At this point show extensions notice.
		if ( get_current_screen()->post_type == 'pt_payment' &&
		     false == get_option( 'pt_show_admin_notice_newsletter' ) &&
		     false == get_transient( 'pt_wait_to_show_admin_notice_extensions' )
		) {
			include_once( PT_PATH . 'admin/views/admin-notice-extensions.php' );
		}
	}

	/**
	 * Add admin notice when site already received live payments &
	 * completing the Setup Wizard is not necessary
	 *
	 * @since   1.5.0
	 */
	public function admin_notice_has_live_payments() {

		// Delete stored value if "hide" button click detected (custom querystring value set to 1).
		if ( ! empty( $_REQUEST['pt-dismiss-has-live-payments-nag'] ) ) {
			delete_option( 'pt_show_admin_notice_has_live_payments' );

			return;
		}

		// Check if there are live payments in this site
		if ( false == pt_has_live_payments() ) {
			return;
		}

		// At this point show "has live payments" notice.
		if ( get_current_screen()->id == 'paytium_page_pt-setup-wizard' ) {
			include_once( PT_PATH . 'admin/views/admin-notice-has-live-payments.php' );
		}
	}


	/**
	 * Code for including a TinyMCE button
	 *
	 * @since   1.0.0
	 */
	function paytium_add_mce_button() {

		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_external_plugins', array ( $this, 'paytium_add_buttons' ) );
			add_filter( 'mce_buttons', array ( $this, 'paytium_register_buttons' ) );
		}

	}


	function paytium_add_buttons( $plugin_array ) {

		$plugin_array['paytiumshortcodes'] = plugin_dir_url( __FILE__ ) . '/public/js/paytium-tinymce-button.js';

		return $plugin_array;

	}


	function paytium_register_buttons( $buttons ) {

		array_push( $buttons, 'separator', 'paytiumshortcodes' );

		return $buttons;

	}


	function paytium_mce_css() {

		wp_enqueue_style( 'paytium_shortcodes-tc', plugins_url( '/public/css/paytium_tinymce_style.css', __FILE__ ) );

	}

}

