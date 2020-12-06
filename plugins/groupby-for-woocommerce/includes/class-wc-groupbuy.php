<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wpgenie.org
 * @since      1.0.0
 *
 * @package    wc_groupbuy
 * @subpackage wc_groupbuy/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    wc_groupbuy
 * @subpackage wc_groupbuy/includes
 * @author     wpgenie <info@wpgenie.org>
 */
class wc_groupbuy {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      wc_groupbuy_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $wc_groupbuy    The string used to uniquely identify this plugin.
	 */
	protected $wc_groupbuy;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current path of the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the plugin.
	 */
	public $path;

	/**
	 * The current plugin_basename.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the plugin.
	 */
	public $plugin_basename;

	/**
	 * The admin plugin object
	 *
	 * @since    1.1.10
	 * @access   public
	 * @var      object
	 */
	public $wc_groupbuy_admin;

	/**
	 * The public plugin object.
	 *
	 * @since    1.1.10
	 * @access   public
	 * @var      object
	 */
	public $wc_groupbuy_public;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_basename ) {

		$this->wc_groupbuy     = 'wc-groupbuy';
		$this->version         = '1.1.19';
		$this->path            = plugin_dir_path( dirname( __FILE__ ) );
		$this->plugin_basename = $plugin_basename;
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - wc_groupbuy_Loader. Orchestrates the hooks of the plugin.
	 * - wc_groupbuy_i18n. Defines internationalization functionality.
	 * - wc_groupbuy_Admin. Defines all hooks for the admin area.
	 * - wc_groupbuy_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
	   * The class responsible for orchestrating the actions and filters of the
	   * core plugin.
	   */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-groupbuy-loader.php';
		/**
	   * The class responsible for defining all actions that occur in the admin area.
	   */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-groupbuy-admin.php';
		/**
	   * The class responsible for defining all actions that occur in the public-facing
	   * side of the site.
	   */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wc-groupbuy-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wc-groupbuy-shortcodes.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-product-groupbuy.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpgenie-dashboard.php';

		$this->loader = new wc_groupbuy_Loader();

		$this->shortcodes = new WC_Shortcode_groupbuy();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the wc_groupbuy_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new wc_groupbuy_i18n();
		$this->loader->add_action( 'wp_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		global $sitepress;

		$this->wc_groupbuy_admin = new wc_groupbuy_Admin( $this->get_wc_groupbuy(), $this->get_version(), $this->get_path(), $this->get_plugin_basename() );

		$this->loader->add_action( 'init', $this->wc_groupbuy_admin, 'register_groupbuy_order_status' );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->wc_groupbuy_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->wc_groupbuy_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->wc_groupbuy_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $this->wc_groupbuy_admin, 'woocommerce_simple_groupbuy_meta' );
		$this->loader->add_action( 'admin_notices', $this->wc_groupbuy_admin, 'woocommerce_simple_groupbuy_admin_notice' );
		$this->loader->add_action( 'admin_init', $this->wc_groupbuy_admin, 'woocommerce_simple_groupbuy_ignore_notices' );
		$this->loader->add_action( 'restrict_manage_posts', $this->wc_groupbuy_admin, 'admin_posts_filter_restrict_manage_posts' );
		$this->loader->add_action( 'delete_post', $this->wc_groupbuy_admin, 'del_groupbuy_logs' );
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $this->wc_groupbuy_admin, 'product_write_panel_tab', 1 );

		$this->loader->add_action( 'woocommerce_process_product_meta', $this->wc_groupbuy_admin, 'product_save_data', 80, 2 );
		$this->loader->add_action( 'woocommerce_email', $this->wc_groupbuy_admin, 'add_to_mail_class' );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $this->wc_groupbuy_admin, 'groupbuy_order_hold_on', 10 );
		$this->loader->add_action( 'woocommerce_order_status_processing', $this->wc_groupbuy_admin, 'groupbuy_order', 99, 1 );
		$this->loader->add_action( 'woocommerce_order_status_completed', $this->wc_groupbuy_admin, 'groupbuy_order', 99, 1 );
		$this->loader->add_action( 'woocommerce_order_status_cancelled', $this->wc_groupbuy_admin, 'groupbuy_order_canceled', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_cancelled', $this->wc_groupbuy_admin, 'groupbuy_order_failed', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_failed', $this->wc_groupbuy_admin, 'groupbuy_order_failed', 10, 1 );
		$this->loader->add_action( 'wp_ajax_delete_groupbuy_participate_entry', $this->wc_groupbuy_admin, 'wp_ajax_delete_participate_entry' );
		$this->loader->add_action( 'wp_ajax_groupbuy_refund', $this->wc_groupbuy_admin, 'groupbuy_refund' );
		$this->loader->add_action( 'woocommerce_duplicate_product', $this->wc_groupbuy_admin, 'woocommerce_duplicate_product' );
		$this->loader->add_action( 'widgets_init', $this->wc_groupbuy_admin, 'register_widgets' );
		$this->loader->add_action( 'wc_groupbuy_close', $this->wc_groupbuy_admin, 'change_order_status_on_closing_groupbuy' );
		$this->loader->add_action( 'manage_product_posts_custom_column', $this->wc_groupbuy_admin, 'render_product_columns' );
		$this->loader->add_filter( 'woocommerce_get_settings_pages', $this->wc_groupbuy_admin, 'groupbuy_settings_class', 20 );
		$this->loader->add_filter( 'parse_query', $this->wc_groupbuy_admin, 'admin_posts_filter', 20 );
		$this->loader->add_filter( 'plugin_row_meta', $this->wc_groupbuy_admin, 'add_support_link', 10, 2 );
		$this->loader->add_filter( 'product_type_selector', $this->wc_groupbuy_admin, 'add_product_type', 10, 2 );
		$this->loader->add_filter( 'wc_order_statuses', $this->wc_groupbuy_admin, 'add_groupbuy_to_order_statuses', 10, 2 );

		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
				$this->loader->add_action( 'woocommerce_product_write_panels', $this->wc_groupbuy_admin, 'product_write_panel' );
		} else {
			$this->loader->add_action( 'woocommerce_product_data_panels', $this->wc_groupbuy_admin, 'product_write_panel' );
		}
		/* emails hooks */
		$email_actions = array( 'wc_groupbuy_won', 'wc_groupbuy_fail', 'wc_groupbuy_close', 'wc_groupbuy_min_fail' );
		foreach ( $email_actions as $action ) {
			$this->loader->add_action( $action, 'WC_Emails', 'send_transactional_email' );
		}

		/* wpml support */
		if ( function_exists( 'icl_object_id' ) && method_exists( $sitepress, 'get_default_language' ) ) {
			//$this->loader->add_action( 'init', $this->wc_groupbuy_admin, 'die', 1 );
			$this->loader->add_action( 'wc_groupbuy_participate', $this->wc_groupbuy_admin, 'sync_metadata_wpml', 1 );
			$this->loader->add_action( 'wc_groupbuy_close', $this->wc_groupbuy_admin, 'sync_metadata_wpml', 1 );
			$this->loader->add_action( 'woocommerce_process_product_meta', $this->wc_groupbuy_admin, 'sync_metadata_wpml', 85 );
			$this->loader->add_action( 'woocommerce_add_to_cart', $this->wc_groupbuy_admin, 'add_language_wpml_meta', 99 );
			$this->loader->add_action( 'woocommerce_add_to_cart', $this->wc_groupbuy_admin, 'change_email_language', 1 );
			$this->loader->add_action( 'woocommerce_simple_groupbuy_won', $this->wc_groupbuy_admin, 'change_email_language', 1 );
		}

		// wc-vendors email integration
			$this->loader->add_filter( 'woocommerce_email_recipient_groupbuy_fail', $this->wc_groupbuy_admin, 'add_vendor_to_email_recipients', 10, 2 );
			$this->loader->add_filter( 'woocommerce_email_recipient_groupbuy_finished', $this->wc_groupbuy_admin, 'add_vendor_to_email_recipients', 10, 2 );

		//update
		// $this->loader->add_filter('pre_set_site_transient_update_plugins', $this->wc_groupbuy_admin, 'check_for_plugin_update');
		// $this->loader->add_filter('plugins_api_result', $this->wc_groupbuy_admin, 'plugin_api_call', 10, 3);
		//
		//
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->wc_groupbuy_public = new wc_groupbuy_Public( $this->get_wc_groupbuy(), $this->get_version(), $this->get_path() );

		$this->loader->add_action( 'wp_enqueue_scripts', $this->wc_groupbuy_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->wc_groupbuy_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_finish_groupbuy', $this->wc_groupbuy_public, 'ajax_finish_groupbuy' );
		$this->loader->add_action( 'woocommerce_product_is_visible', $this->wc_groupbuy_public, 'filter_groupbuy', 10, 2 );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item_title', $this->wc_groupbuy_public, 'add_groupbuy_bage', 60 );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item_title', $this->wc_groupbuy_public, 'add_progressbar_in_loop', 60 );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item_title', $this->wc_groupbuy_public, 'add_countdown_in_loop', 65 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $this->wc_groupbuy_public, 'woocommerce_groupbuy_participate_template', 25 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $this->wc_groupbuy_public, 'woocommerce_groupbuy_winners', 25 );
		$this->loader->add_action( 'woocommerce_groupbuy_add_to_cart', $this->wc_groupbuy_public, 'woocommerce_groupbuy_add_to_cart' );
		$this->loader->add_action( 'woocommerce_check_cart_items', $this->wc_groupbuy_public, 'check_cart_items' );
		$this->loader->add_action( 'woocommerce_product_query', $this->wc_groupbuy_public, 'remove_groupbuy_from_woocommerce_product_query', 2 );
		$this->loader->add_action( 'widgets_init', $this->wc_groupbuy_public, 'register_widgets' );
		$this->loader->add_action( 'init', $this->wc_groupbuy_public, 'simple_groupbuy_cron' );
		$this->loader->add_action( 'pre_get_posts', $this->wc_groupbuy_public, 'query_is_groupbuy_archive', 1 );
		$this->loader->add_action( 'template_redirect', $this->wc_groupbuy_public, 'track_groupbuy_view' );
		$this->loader->add_filter( 'woocommerce_locate_template', $this->wc_groupbuy_public, 'woocommerce_locate_template', 10, 3 );
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $this->wc_groupbuy_public, 'add_to_cart_validation', 10, 4 );
		$this->loader->add_filter( 'template_include', $this->wc_groupbuy_public, 'groupbuy_page_template', 99 );
		$this->loader->add_filter( 'body_class', $this->wc_groupbuy_public, 'output_body_class' );
		$this->loader->add_filter( 'pre_get_posts', $this->wc_groupbuy_public, 'is_groupbuy_archive_pre_get_posts' );
		$this->loader->add_filter( 'wp_nav_menu_objects', $this->wc_groupbuy_public, 'groupbuy_nav_menu_item_classes' );
		$this->loader->add_filter( 'woocommerce_get_groupbuy_page_id', $this->wc_groupbuy_public, 'groupbuy_page_wpml', 10, 1 );
		$this->loader->add_filter( 'woocommerce_get_breadcrumb', $this->wc_groupbuy_public, 'groupbuy_get_breadcrumb', 1, 2 );
		$this->loader->add_filter( 'pre_get_document_title', $this->wc_groupbuy_public, 'groupbuy_filter_wp_title', 10 );
		$this->loader->add_filter( 'woocommerce_page_title', $this->wc_groupbuy_public, 'groupbuy_page_title', 10 );
		$this->loader->add_filter( 'woocommerce_product_query', $this->wc_groupbuy_public, 'pre_get_posts', 99, 2 );
		$this->loader->add_filter( 'woocommerce_is_purchasable', $this->wc_groupbuy_public, 'is_purchasable', 99, 2 );
		$this->loader->add_filter( 'post_class', $this->wc_groupbuy_public, 'add_post_class' );
		$this->loader->add_filter( 'woocommerce_sale_price_html', $this->wc_groupbuy_public, 'woocommerce_custom_sales_price', 10, 2 );
		$this->loader->add_filter( 'woocommerce_sale_flash', $this->wc_groupbuy_public, 'woocommerce_custom_sales_bage', 10, 3 );
		$this->loader->add_action( 'woocommerce_login_form_end', $this->wc_groupbuy_public, 'add_redirect_previous_page' );
		$this->loader->add_filter( 'woocommerce_customer_available_downloads', $this->wc_groupbuy_public, 'check_woocommerce_customer_available_downloads', 10, 1 );
		$this->loader->add_filter( 'woocommerce_order_get_downloadable_items', $this->wc_groupbuy_public, 'check_woocommerce_customer_available_downloads', 10, 1 );
		$this->loader->add_action( 'woocommerce_download_product', $this->wc_groupbuy_public, 'check_groupbuy_download', 10, 6 );

		  add_shortcode( 'woocommerce_simple_groupbuy_my_groupbuy', array( $this, 'shortcode_my_groupbuy' ) );
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_wc_groupbuy() {
		return $this->wc_groupbuy;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    wc_groupbuy_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the path of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The path  of the plugin.
	 */
	public function get_path() {
		return $this->path;
	}


	/**
	 * Retrieve the plugin_basename of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The plugin_basename  of the plugin.
	 */
	public function get_plugin_basename() {
		return $this->plugin_basename;
	}

}
