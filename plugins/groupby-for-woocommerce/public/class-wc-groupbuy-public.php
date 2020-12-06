<?php

/**
 * The public-facing functionality of the Woocommerce Group Buy extension.
 *
 * @link       http://wpgenie.org
 * @since      1.0.0-rc7
 *
 * @package    wc_groupbuy
 * @subpackage wc_groupbuy/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wc_groupbuy
 * @subpackage wc_groupbuy/public
 * @author     wpgenie <info@wpgenie.org>
 */
class wc_groupbuy_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wc_groupbuy    The ID of this plugin.
	 */
	private $wc_groupbuy;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

		/**
	 * The current path of the plugin.
	 *
	 * @since    1.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $path;

	/**
	 * The current url of the plugin public.
	 *
	 * @since    1.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $dir_public_url;



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wc_groupbuy       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      string    $path    The path of this plugin.
	 */
	public function __construct( $wc_groupbuy, $version, $path ) {

			$this->wc_groupbuy = $wc_groupbuy;
			$this->version = $version;
			$this->path = $path;
			$this->dir_public_url = plugin_dir_url( __FILE__ );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->wc_groupbuy, $this->dir_public_url . 'css/wc-groupbuy-public.css', array(), null, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		
		wp_register_script( $this->wc_groupbuy, $this->dir_public_url . 'js/wc-groupbuy-public.js', array( 'jquery' , 'wc-groupbuy-countdown'), $this->version, false );

		wp_register_script( 'wc-groupbuy-jquery-plugin', $this->dir_public_url .'js/jquery.plugin.min.js', array('jquery'), $this->version, false );

		wp_register_script( 'wc-groupbuy-countdown', $this->dir_public_url .'js/jquery.countdown.min.js', array('jquery','wc-groupbuy-jquery-plugin'), $this->version, false );

		wp_register_script('wc-groupbuy-countdown-language', $this->dir_public_url .'js/jquery.countdown.language.js', array('jquery','wc-groupbuy-countdown'), $this->version, false );

		$language_data = array(
			'labels' =>array(
							'Years' => __('Years', 'wc_groupbuy'),
							'Months' => __('Months', 'wc_groupbuy'),
							'Weeks' => __('Weeks', 'wc_groupbuy'),
							'Days' => __('Days', 'wc_groupbuy'),
							'Hours' => __('Hours', 'wc_groupbuy'),
							'Minutes' => __('Minutes', 'wc_groupbuy'),
							'Seconds' => __('Seconds', 'wc_groupbuy'),
							),
			'labels1' => array(
							'Year' => __('Year', 'wc_groupbuy'),
							'Month' => __('Month', 'wc_groupbuy'),
							'Week' => __('Week', 'wc_groupbuy'),
							'Day' => __('Day', 'wc_groupbuy'),
							'Hour' => __('Hour', 'wc_groupbuy'),
							'Minute' => __('Minute', 'wc_groupbuy'),
							'Second' => __('Second', 'wc_groupbuy'),
							),
			'compactLabels' =>	array(
							'y' => __('y', 'wc_groupbuy'),
							'm' => __('m', 'wc_groupbuy'),
							'w' => __('w', 'wc_groupbuy'),
							'd' => __('d', 'wc_groupbuy'),
							)
		);

		wp_localize_script( 'wc-groupbuy-countdown-language', 'wc_groupbuy_language_data', $language_data);

		$custom_data = array( 'finished' => __('Group Buy deal has finished! Please refresh page to see details.', 'wc_groupbuy'), 'gtm_offset' => get_option( 'gmt_offset' ), 'started' => __('Group Buy deal has started! Please refresh page.', 'wc_groupbuy'), 'compact_counter' => get_option('simple_groupbuy_compact_countdown', 'no') );

		$wc_groupbuy_live_check = get_option( 'wc_groupbuy_live_check' );

		$wc_groupbuy_check_interval = get_option( 'wc_groupbuy_live_check_interval' );

		wp_localize_script(  $this->wc_groupbuy , 'wc_groupbuy_data', $custom_data);

		wp_enqueue_script(  'wc-groupbuy-countdown-language');

		wp_enqueue_script(  $this->wc_groupbuy );
	}


	/**
	 * register_widgets function
	 *
	 * @access public
	 * @return void
	 *
	 */
	function register_widgets() {

		// Include - no need to use autoload as WP loads them anyway
		include_once( 'widgets/class-wc-groupbuy-widget-featured-groupbuy.php' );
		include_once( 'widgets/class-wc-groupbuy-widget-random-groupbuy.php' );
		include_once( 'widgets/class-wc-groupbuy-widget-recent-groupbuy.php' );
		include_once( 'widgets/class-wc-groupbuy-widget-recently-groupbuy.php' );
		include_once( 'widgets/class-wc-groupbuy-widget-ending-soon-groupbuy.php' );
		include_once( 'widgets/class-wc-widget-groupbuy-search.php' );
		include_once( 'widgets/class-wc-groupbuy-widget-future-groupbuy.php' );

		// Register widgets
		register_widget( 'WC_groupbuy_Widget_Ending_Soon_Groupbuy' );
		register_widget( 'WC_groupbuy_Widget_Featured_Groupbuy' );
		register_widget( 'WC_groupbuy_Widget_Future_Groupbuy' );
		register_widget( 'WC_groupbuy_Widget_Random_Groupbuy' );
		register_widget( 'WC_groupbuy_Widget_Recent_Groupbuy' );
		register_widget( 'WC_groupbuy_Widget_Recently_Viewed_Groupbuy' );
		register_widget( 'WC_Widget_Groupbuy_Search' );
	}

	/**
	 * Templating with plugin folder
	 *
	 * @param int $post_id the post (product) identifier
	 * @param stdClass $post the post (product)
	 *
	 */
	function woocommerce_locate_template( $template, $template_name, $template_path ) {


		if (!$template_path) {
			$template_path = WC()->template_url;
		}

		$plugin_path = $this->path . 'templates/';
		$template_locate = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
				)
			);


		if (!$template_locate && file_exists($plugin_path . $template_name)) {

			return $plugin_path . $template_name;

		} else {

			return $template;

		}
	}
	/**
	 *  Filter groupbuy based on settings
	 *
	 * @access public
	 * @param  bolean, string
	 * @return bolean
	 *
	 */
	function filter_groupbuy( $visible, $product_id ){

		global $product;

		if (!$product)
			return $visible;


		if (!(method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy'))
				return $visible;

		if ((method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy') && $product->is_user_participating() == true)
			   return true;    
		return $visible;
	}
	/**
	 *  Shortcode for my groupbuy
	 *
	 * @access public
	 * @param  array
	 * @return
	 *
	 */
	function shortcode_my_groupbuy($atts) {

		return WC_Shortcodes::shortcode_wrapper( array( 'WC_Shortcode_Simple_groupbuy_My_Groupbuys', 'output' ), $atts );
	}
	/**
	 *  Add groupbuy badge for groupbuy product
	 *
	 * @access public
	 *
	 */
	function add_groupbuy_bage(){

		if(get_option( 'simple_groupbuy_bage', 'yes' ) == 'yes'){
			wc_get_template( 'loop/groupbuy-badge.php' );
		}
	}

	 /**
	 *  Add progres in loop
	 *
	 * @access public
	 *
	 */
	function add_progressbar_in_loop(){

		if(get_option( 'simple_groupbuy_loopprogress', 'yes' ) == 'yes'){
			wc_get_template( 'loop/groupbuy-progress.php' );
		}
	}

	/**
	 *  Add progres in loop
	 *
	 * @access public
	 *
	 */
	function add_countdown_in_loop(){

		if(get_option( 'simple_groupbuy_loopcountdown', 'yes' ) == 'yes'){
			wc_get_template( 'loop/groupbuy-countdown.php' );
		}
	}
	/**
	 * Get template for groupbuy archive page
	 *
	 * @access public
	 * @param string
	 * @return string
	 *
	 */
	function groupbuy_page_template( $template ) {

		if (get_query_var('is_groupbuy_archive', false)) {

			$template  = locate_template(WC()->template_path().'archive-product-groupbuy.php');
			if($template) {
				wc_get_template('archive-product-groupbuy.php');
			} else {
				wc_get_template('archive-product.php');
			}
			return FALSE;
		}
		return $template;
	}
	/**
	 * Output body classes for groupbuy archive page
	 *
	 * @access public
	 * @param array
	 * @return array
	 *
	 */
	function output_body_class( $classes ){

		if ( is_page( wc_get_page_id('groupbuy') )  ) {
				$classes [] = 'woocommerce groupbuy-page';
		}
		return $classes;
	}
	/**
	 * Remove groupbuy products from woocommerce product query
	 *
	 * @access public
	 * @param object
	 * @return void
	 *
	 */
	function remove_groupbuy_from_woocommerce_product_query( $q ){



		// We only want to affect the main query
		if ( ! $q->is_main_query() OR get_query_var('is_groupbuy_archive', false) ) return;

		if ( ! $q->is_post_type_archive( 'product' ) && ! $q->is_tax( get_object_taxonomies( 'product' ) ) ) return;


		$simple_groupbuy_dont_mix_shop   = get_option( 'simple_groupbuy_dont_mix_shop' );
		$simple_groupbuy_dont_mix_cat    = get_option( 'simple_groupbuy_dont_mix_cat' );
		$simple_groupbuy_dont_mix_tag    = get_option( 'simple_groupbuy_dont_mix_tag' );

		if ( $simple_groupbuy_dont_mix_cat != 'yes' && is_product_category() ) return;
		if ( $simple_groupbuy_dont_mix_tag != 'yes' && is_product_tag() ) return;

		$simple_groupbuy_dont_mix_search = get_option('simple_groupbuy_dont_mix_search');

		

		if (!is_admin() && $q->is_main_query() && $q->is_search()) {


					if (isset($q->query['search_groupbuys']) && $q->query['search_groupbuys'] == TRUE) {
						$taxquery = $q->get('tax_query');
						if (!is_array($taxquery)) {
							$taxquery = array();
						}
						$taxquery[] =
						array(
							'taxonomy' => 'product_type',
							'field' => 'slug',
							'terms' => 'groupbuy',

						);

						$q->set('tax_query', $taxquery);
						$q->query['is_groupbuy_archive'] = TRUE;


					} elseif ($simple_groupbuy_dont_mix_search == 'yes') {
						
						$taxquery = $q->get('tax_query');
						if (!is_array($taxquery)) {
							$taxquery = array();
						}
						$taxquery[] =
						array(
							'taxonomy' => 'product_type',
							'field' => 'slug',
							'terms' => 'groupbuy',
							'operator' => 'NOT IN',
						);

						$q->set('tax_query', $taxquery);

						

					} else {
						$q->query['is_groupbuy_archive'] = TRUE;
					}

					return;

			}

		if ( $simple_groupbuy_dont_mix_shop == 'yes' ){
			$taxquery = $q->get( 'tax_query' );
			if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
			}
			$taxquery []=
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'groupbuy',
				'operator'=> 'NOT IN'
			);
			$q->set( 'tax_query', $taxquery );
		}
	}
	/**
	 * Define query modification based on settings
	 *
	 * @access public
	 * @param object
	 * @return void
	 *
	 */
	function pre_get_posts( $q ){

		
		 if (is_admin()){
			return;
		}


		$groupbuy = array();


		$simple_groupbuy_finished_enabled    = get_option( 'simple_groupbuy_finished_enabled' );
		$simple_groupbuy_future_enabled      = get_option( 'simple_groupbuy_future_enabled' );
		$simple_groupbuy_dont_mix_shop       = get_option( 'simple_groupbuy_dont_mix_shop' );
		$simple_groupbuy_dont_mix_cat        = get_option( 'simple_groupbuy_dont_mix_cat' );
		$simple_groupbuy_dont_mix_tag        = get_option( 'simple_groupbuy_dont_mix_tag' );

		if (isset($q->query_vars['is_groupbuy_archive']) && $q->query_vars['is_groupbuy_archive'] == 'true') {

			$taxquery = $q->get('tax_query');
			if (!is_array($taxquery)) {
					$taxquery = array();
			}
			$taxquery[] =
			array(
					'taxonomy'  => 'product_type',
					'field'     => 'slug',
					'terms'     => 'groupbuy',
			);

			$q->set('tax_query', $taxquery);
			add_filter( 'woocommerce_is_filtered' , array($this, 'add_is_filtered'), 99); // hack for displaying when Shop Page Display is set to show categories
		}

		if ( ($simple_groupbuy_future_enabled != 'yes' && (!isset($q->query['show_future_groupbuy']) or !$q->query['show_future_groupbuy'] ) )
				OR (isset($q->query['show_future_groupbuy']) && $q->query['show_future_groupbuy'] == FALSE ) ){

			$metaquery = $q->get('meta_query');

			   if ( ! is_array( $metaquery ) ) {
					$metaquery = array();
			}

			$metaquery [] = 
							array(
								'key'     => '_groupbuy_started',
								'compare' => 'NOT EXISTS',
								);
			$q->set( 'meta_query', $metaquery );
		}

		if ( ($simple_groupbuy_finished_enabled != 'yes' && (!isset($q->query['show_past_groupbuy']) or !$q->query['show_past_groupbuy'] )
				OR (isset($q->query['show_past_groupbuy']) && $q->query['show_past_groupbuy'] == FALSE ) ) ){

			$metaquery = $q->get('meta_query');
				if ( ! is_array( $metaquery ) ) {
					$metaquery = array();
			}
			$metaquery [] = array(
								'key'     => '_groupbuy_closed',
								'compare' => 'NOT EXISTS',
						);
			$q->set( 'meta_query', $metaquery );
		}

		if ( $simple_groupbuy_dont_mix_cat != 'yes' && is_product_category() ) return;

		if ( $simple_groupbuy_dont_mix_tag != 'yes' && is_product_tag() ) return;

			
		if( !isset($q->query['is_groupbuy_archive']) && get_query_var('is_groupbuy_archive', false) == FALSE ){

			if($simple_groupbuy_dont_mix_shop == 'yes'){
				$taxquery = $q->get( 'tax_query' );
				if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
				}
				$taxquery []=
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'groupbuy',
					'operator'=> 'NOT IN'
				);
				$q->set( 'tax_query', $taxquery );
				return;
			}
		}
	}
	/**
	 * Run query modification based on settings
	 *
	 * @access public
	 * @param object
	 * @return void
	 *
	 */
	function is_groupbuy_archive_pre_get_posts( $q ){
		if ( isset ( $q->query['is_groupbuy_archive']) OR (!isset ( $q->query['is_groupbuy_archive']) && (isset ( $q->query['post_type']) && $q->query['post_type'] =='product' && ! $q->is_main_query())) ) {
			$this->pre_get_posts($q);
		}
	}


	function query_is_groupbuy_archive( $q ) {

		if (!$q->is_main_query()) {
			return;
		}

		if (isset($q->queried_object->ID) && $q->queried_object->ID === wc_get_page_id('groupbuy')) {

			$q->set('post_type', 'product');
			$q->set('page', '');
			$q->set('pagename', '');
			$q->set('groupbuy_arhive', 'true');
			$q->set('is_groupbuy_archive', 'true');

			// Fix conditional Functions
			$q->is_archive = true;
			$q->is_post_type_archive = true;
			$q->is_singular = false;
			$q->is_page = false;

		}

		if (($q->is_page() && 'page' === get_option('show_on_front') && absint($q->get('page_id')) === wc_get_page_id('groupbuy')) OR ($q->is_home() && absint(get_option('page_on_front')) === wc_get_page_id('groupbuy'))) {

			$q->set('post_type', 'product');

			// This is a front-page shop
			$q->set('post_type', 'product');
			$q->set('page_id', '');
			$q->set('groupbuy_arhive', 'true');
			$q->set('is_groupbuy_archive', 'true');

			if (isset($q->query['paged'])) {
				$q->set('paged', $q->query['paged']);
			}

			// Define a variable so we know this is the front page shop later on
			define('groupbuyS_IS_ON_FRONT', true);

			// Get the actual WP page to avoid errors and let us use is_front_page()
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$groupbuy_page = get_post( wc_get_page_id('groupbuy') );

			$wp_post_types['product']->ID = $groupbuy_page->ID;
			$wp_post_types['product']->post_title = $groupbuy_page->post_title;
			$wp_post_types['product']->post_name = $groupbuy_page->post_name;
			$wp_post_types['product']->post_type = $groupbuy_page->post_type;
			$wp_post_types['product']->ancestors = get_ancestors($groupbuy_page->ID, $groupbuy_page->post_type);

			// Fix conditional Functions like is_front_page
			$q->is_singular = false;
			$q->is_post_type_archive = true;
			$q->is_archive = true;
			$q->is_page = true;

			// Remove post type archive name from front page title tag
			add_filter('post_type_archive_title', '__return_empty_string', 5);

			// Fix WP SEO
			if (class_exists('WPSEO_Meta')) {
				add_filter('wpseo_metadesc', WPSEO_Meta::get_value('metadesc', wc_get_page_id('groupbuy')));
				add_filter('wpseo_metakey', WPSEO_Meta::get_value('metakey', wc_get_page_id('groupbuy')));
			}

		}

	}
	/**
	 * Cron action
	 *
	 * Checks for a valid request, check groupbuy and closes groupbuy if is finished
	 *
	 * @access public
	 * @param bool $url (default: false)
	 * @return void
	 *
	 */
	function simple_groupbuy_cron( $url = false ) {

		if ( empty( $_REQUEST['groupbuy-cron'] ) ) return;

		if ($_REQUEST['groupbuy-cron'] == 'check'){

			do_action('before_groupbuy_cron');

			update_option('Wc_groupbuy_cron_check','yes');

			set_time_limit(0);

			ignore_user_abort(1);

			$args = array(
				'post_type'            => 'product',
				'posts_per_page'       => '-1',
				'meta_query'           => array(
				'relation'             => 'AND', // Optional, defaults to "AND"
				
				array(
				'key'                  => '_groupbuy_closed',
				'compare'              => 'NOT EXISTS',
				),
				array(
				'key'                  => '_groupbuy_dates_to',
				'compare'              => 'EXISTS'
				)
				),
				'meta_key'             => '_groupbuy_dates_to',
				'orderby'              => 'meta_value',
				'order'                => 'ASC',
				'tax_query'            => array( array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'groupbuy') ),
				'is_groupbuy_archive'  => TRUE,
				'show_past_groupbuy'   => TRUE,
				'show_future_groupbuy' => TRUE
			);

			for( $i=0; $i<3; $i++ ) {

				$the_query  = new WP_Query( $args );

				$time = microtime(1);

				if ( $the_query->have_posts() ) {

					while ( $the_query->have_posts() ): $the_query->the_post();
						$product_data = wc_get_product( $the_query->post->ID );
						$product_data_type = method_exists( $product_data, 'get_type' ) ? $product_data->get_type() : $product_data->product_type;    
						if ($product_data_type == 'groupbuy'){

								$product_data->is_closed();
						}

					endwhile;
				}
				$time = microtime(1)-$time;
				$i<3 and sleep(2-$time);
			}

			do_action('after_groupbuy_cron');
		}
		exit;
	}
	/**
	 * Load participate template part
	 *
	 */
	function woocommerce_groupbuy_participate_template(){
		global $product;
		if ( method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy' ) wc_get_template( 'single-product/groupbuy-participate.php' );
	}
	/**
	 * Load winners template part
	 *
	 */
	function woocommerce_groupbuy_winners(){
		global $product;
		if ( method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy' && $product->is_closed() ) wc_get_template( 'single-product/groupbuy-ended.php' );
	}
	/**
	 * Load groupbuy product add to cart template part.
	 *
	 */
	function woocommerce_groupbuy_add_to_cart() {
		wc_get_template( 'single-product/add-to-cart/groupbuy.php' );
	}
	
	/**
	 * Add to cart validation
	 *
	 */
	public function add_to_cart_validation( $pass, $product_id, $quantity, $variation_id = 0 ) {

		if ( function_exists( 'wc_get_product' ) ) {

			$product = wc_get_product( $product_id );

		} else {

			$product = new WC_Product( $product_id );
		}



		if (method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy' ) {

			$max_deals_per_user = $product->get_groupbuy_max_deals_per_user() ? $product->get_groupbuy_max_deals_per_user() : false;          

			if ( $max_deals_per_user == false ) {

				return $pass;
			}

			 if ( !is_user_logged_in() ) {

				wc_add_notice(sprintf(__('Sorry, you must be logged in to participate in Group Buy deal. <a href="%s" class="button">Login &rarr;</a>', 'wc_groupbuy'), get_permalink(wc_get_page_id('myaccount'))), 'error');
				return false;
			}

			$user_ID = get_current_user_id();


				$users_qty = array_count_values( get_post_meta($product_id, '_groupbuy_participant_id') );

				$current_user_qty = isset($users_qty[$user_ID]) ? intval($users_qty[$user_ID]) : 0;

				$qty =  $current_user_qty + intval($quantity);

				if ( $qty > $max_deals_per_user ) {

					wc_add_notice( sprintf( __( 'The maximum allowed quantity for %s is %d . You already have %d, so you can not add %d more.', 'wc_groupbuy' ), $product->get_title(), $max_deals_per_user, $users_qty, $quantity ), 'error' );
					$pass = false;
				}

		}
		return $pass;
	}

	/**
	 * Validate cart items against some rules
	 *
	 * @access public
	 * @return void
	 *
	 */
	public function check_cart_items() {

		$checked_ids = $product_quantities =  array();

		foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {

			if ( ! isset( $product_quantities[ $values['product_id'] ] ) ) {

				$product_quantities[ $values['product_id'] ] = 0;
			}

			$product_quantities[ $values['product_id'] ] += $values['quantity'];

		}

		foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {

			$product = wc_get_product( $values['product_id']  );

			if (method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy'){

				 $max_deals_per_user = $product->get_groupbuy_max_deals_per_user() ? $product->get_groupbuy_max_deals_per_user() : false;
				 
				if ( $max_deals_per_user == false ) {

							return true;
				 }


				if (!is_user_logged_in()) {

					wc_add_notice(sprintf(__('Sorry, you must be logged in to participate in Group Buy deals. <a href="%s" class="button">Login &rarr;</a>', 'wc_groupbuy'), get_permalink(wc_get_page_id('myaccount'))), 'error');

					return false;
				}

				$user_ID = get_current_user_id();


				if($max_deals_per_user !== FALSE){

					$users_qty = array_count_values(get_post_meta($values['product_id'] , '_groupbuy_participant_id'));

					$current_user_qty = isset($users_qty[$user_ID]) ? intval($users_qty[$user_ID]) : 0;

					$qty = $current_user_qty+ intval($product_quantities[ $values['product_id'] ]);

					if( $qty > $max_deals_per_user ) {

						wc_add_notice(  sprintf( __( 'The maximum allowed quantity for %s is %d . You already have %d, so you can not add %d more.', 'wc_groupbuy' ), $product->get_title(),$max_deals_per_user, $users_qty, intval($product_quantities[ $values['product_id'] ]) ) ,'error');

					}
				}
			}
		}
	}
	/**
	 * Make product not purchasable if Group Buy deal is sold out
	 *
	 * @access public
	 * @return bolean
	 */
	public function is_purchasable ( $purchasable, $product ){

		if( method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy' && ($purchasable === true) ){

			return !$product->is_groupbuy_max_deals_met();
		}

		return $purchasable;
	}
	/**
	 * Add some classes to post_class()
	 *
	 * @access public
	 * @return array
	 */
	public function add_post_class ($classes ){

		global $post,$product;

		if(method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy'){

			if($product->is_groupbuy_max_deals_met()){
				$classes[] = 'groupbuy-full';
			}
		}

		return $classes;
	}

	/**
	 * Add save percent next to sale item prices.
	 *
	 * @access public
	 * @return string
	 */
	function woocommerce_custom_sales_price( $price, $product ) {

		if( is_admin() ) {

			return $price;
		}

		if( method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy' ){
			$regular_price = method_exists( $product, 'get_regular_price' ) ? $product->get_regular_price(): $product->regular_price; 
			$sale_price = method_exists( $product, 'get_sale_price' ) ? $product->get_sale_price() : $product->sale_price;
			$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
			return $price . sprintf( __(' Save %s', 'woocommerce' ), $percentage . '%' );
		}

		return $price;
	}

	/**
	 * Add save percent next to sale item prices.
	 *
	 * @access public
	 * @return string
	 */
	function woocommerce_custom_sales_bage( $saletext ,$post, $product ) {

		if(method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy'){
			return apply_filters( 'wc_groupbuy_sale_flash', '', $post, $product );
		}

		return $saletext;
	}

	/**
	 * Set is filtered to true to skip displaying categories only on page
	 *
	 * @access public
	 * @return bolean
	 *
	 */
	function add_is_filtered( $id ){

		return true;
	}

	 /**
	  * Translate Group Buy page url
	  *
	  * @access public
	  * @return array
	  *
	  */
	function translate_ls_groupbuy_url($languages, $debug_mode = false) {

		global $sitepress;
		global $wp_query;

		$groupbuy_page = (int) wc_get_page_id( 'groupbuy' );

		foreach ($languages as $language) {
			// shop page
			// obsolete?
			if (get_query_var('is_groupbuy_archive', false) || $debug_mode ) {

				$sitepress->switch_lang($language['language_code']);
				$url = get_permalink( apply_filters( 'translate_object_id', $groupbuy_page, 'page', true, $language['language_code']) );
				$sitepress->switch_lang();
				$languages[$language['language_code']]['url'] = $url;
			}
		}

		return $languages;
	}

	/**
	 *
	 * Add wpml support for Group Buy base page
	 *
	 * @access public
	 * @param int
	 * @return int
	 *
	 */
	function groupbuy_page_wpml( $page_id ){

	  global $sitepress;

	  if (function_exists('icl_object_id') ) {

		  $id = icl_object_id($page_id,'page',false);

	  }  else {

		  $id = $page_id;
	  }

	  return $id;
	}

	/**
	*
	* Fix active class in nav for Group Buy page.
	*
	* @access public
	* @param array $menu_items
	* @return array
	*
	*/
	function groupbuy_nav_menu_item_classes($menu_items) {

		if (!get_query_var('is_groupbuy_archive', false)) {
			return $menu_items;
		}

		$bgoupbuy_page = (int) wc_get_page_id('groupbuy');

		foreach ((array) $menu_items as $key => $menu_item) {

			$classes = (array) $menu_item->classes;

			// Unset active class

			$menu_items[$key]->current = false;

			if (in_array('current_page_parent', $classes)) {
				unset($classes[array_search('current_page_parent', $classes)]);
			}

			if (in_array('current-menu-item', $classes)) {
				unset($classes[array_search('current-menu-item', $classes)]);
			}

			if (in_array('current_page_item', $classes)) {
				unset($classes[array_search('current_page_item', $classes)]);
			}

			// Set active state if this is the shop page link
			if ($bgoupbuy_page == $menu_item->object_id && 'page' === $menu_item->object) {
				$menu_items[$key]->current = true;
				$classes[] = 'current-menu-item';
				$classes[] = 'current_page_item';

			}

			$menu_items[$key]->classes = array_unique($classes);

		}

		return $menu_items;
	}
	/**
	 *
	 * Fix for Group Buy base page breadcrumbs
	 *
	 * @access public
	 * @param string
	 * @return string
	 *
	 */
	public function groupbuy_get_breadcrumb( $crumbs, $WC_Breadcrumb ) {

		if (get_query_var('is_groupbuy_archive', false) == 'true') {

			$auction_page_id = wc_get_page_id('groupbuy');
			$crumbs[1] = array(get_the_title($auction_page_id), get_permalink($auction_page_id));
		}

		return $crumbs;
	}

	function groupbuy_filter_wp_title( $title ) {

	  global $paged, $page;

	  if (!get_query_var('is_groupbuy_archive', false)) {
		return $title;
	  }

	  $auction_page_id = wc_get_page_id('groupbuy');
	  $title = get_the_title($auction_page_id);

	  return $title;
	}
	/**
	*
	* Fix for Group Buy base page title
	*
	* @access public
	* @param string
	* @return string
	*
	*/
	function groupbuy_page_title($title) {

	  if (get_query_var('is_groupbuy_archive', false) == 'true') {

		  $auction_page_id = wc_get_page_id('groupbuy');

		  $title = get_the_title($auction_page_id);

	  }

	  return $title;

	}

	/**
	 *
	 * Track Group Buy views for Recently Viewed Group Buys widget
	 *
	 * @access public
	 * @param void
	 * @return int
	 *
	 */
	function track_groupbuy_view() {

		if (!is_singular('product') || !is_active_widget(false, false, 'recently_viewed_groupbuy', true)) {
			return;
		}

		global $post;

		if (empty($_COOKIE['woocommerce_recently_viewed_groupbuy'])) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode('|', $_COOKIE['woocommerce_recently_viewed_groupbuy']);
		}

		if (!in_array($post->ID, $viewed_products)) {
			$viewed_products[] = $post->ID;
		}

		if (sizeof($viewed_products) > 15) {
			array_shift($viewed_products);
		}

		// Store for session only
		wc_setcookie('woocommerce_recently_viewed_groupbuy', implode('|', $viewed_products));
	}

	function add_redirect_previous_page(){
		if(isset($_SERVER["HTTP_REFERER"])){
			echo '<input type="hidden" name="redirect" value="' . esc_url( $_SERVER["HTTP_REFERER"] ) . ' " >';
		}
	}
	public function check_woocommerce_customer_available_downloads( $downloads) {
		foreach ($downloads as $key => $download) {
			$product = wc_get_product( $download['product_id'] );
			if( $product->is_type('groupbuy') ){
				if ( intval( $product->get_groupbuy_closed() ) !== 2 ) {
					unset( $downloads[ $key ] );
				}
			}
		}
		return $downloads;
	}

	public function check_groupbuy_download( $user_email, $order_key, $product_id, $user_id , $download_id, $order_id){
		$product = wc_get_product( $product_id );
		if( $product->is_type('groupbuy') ){
			if ( intval( $product->get_groupbuy_closed() ) !== 2 ) {
				$data_store  = WC_Data_Store::load( 'customer-download' );
				
				$message = esc_html__( 'You must wait for group buy deal to be closed before downloading.', 'wc_groupbuy' );
				$message .= ' <a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="wc-forward">' . esc_html__( 'Go to shop', 'woocommerce' ) . '</a>';
				wp_die( $message, '', array( 'response' => 403 ) ); // WPCS: XSS ok.
			}
		}
	}


}
