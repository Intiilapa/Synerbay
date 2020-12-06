<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wpgenie.org
 * @since      1.0.0
 *
 * @package    wc_groupbuy
 * @subpackage wc_groupbuy/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wc_groupbuy
 * @subpackage wc_groupbuy/admin
 * @author     wpgenie <info@wpgenie.org>
 */
class wc_groupbuy_Admin {

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
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $path;

	/**
	 * The current plugin_basename.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the plugin.
	 */
	public $plugin_basename;


	public $api_url = 'https://wpgenie.org/api/index.php';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wc_groupbuy       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wc_groupbuy, $version, $path, $plugin_basename ) {

		$this->wc_groupbuy = $wc_groupbuy;
		$this->version = $version;
		$this->path = $path;
		$this->plugin_slug = 'groupby-for-woocommerce';
		$this->plugin_basename = $plugin_basename;
		



	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {


		wp_enqueue_style( $this->wc_groupbuy, plugin_dir_url( __FILE__ ) . 'css/wc-groupbuy-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

				if( 'product' == get_post_type() ){

						wp_register_script(
								'wc-groupbuy-admin',
								plugin_dir_url( __FILE__ ) . '/js/wc-groupbuy-admin.js',
								array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker','timepicker-addon'),
								$this->version,
								true
						);

						$params = array(
							'i18_max_ticket_less_than_min_ticket_error' => __( 'Please enter in a value greater than the min deal.', 'wc_groupbuy' ),
							'groupbuy_refund_nonce'              				=> wp_create_nonce( 'groupbuy-refund' ),
						);

						wp_localize_script( 'wc-groupbuy-admin', 'woocommerce_groupbuy', $params );

						wp_enqueue_script( 'wc-groupbuy-admin' );

						wp_enqueue_script(
							'timepicker-addon',
							plugin_dir_url( __FILE__ ) . '/js/jquery-ui-timepicker-addon.js',
							array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
							$this->version,
							true
						);

						wp_enqueue_style( 'jquery-ui-datepicker' );
				}

		 }
	}

	/**
	* Add to mail class
	*
	* @access public
	* @return object
	*
	*/
	public function add_to_mail_class($emails){

		include_once( 'emails/class-wc-email-groupbuy-win.php' );
		include_once( 'emails/class-wc-email-groupbuy-failed.php' );
		include_once( 'emails/class-wc-email-groupbuy-failed-user.php' );
		include_once( 'emails/class-wc-email-groupbuy-finished.php' );

		$emails->emails['WC_Email_groupbuy_Win'] = new WC_Email_groupbuy_Win();
		$emails->emails['WC_Email_groupbuy_Failed'] = new WC_Email_groupbuy_Failed();
		$emails->emails['WC_Email_groupbuy_Finished'] = new WC_Email_groupbuy_Finished();
		$emails->emails['WC_Email_groupbuy_failed_user'] = new WC_Email_groupbuy_failed_user();
		
		return $emails;
	}

	/**
	 * register_widgets function
	 *
	 * @access public
	 * @return void
	 *
	 */
	function register_widgets() {

	}

	/**
	 * Add link to plugin page
	 *
	 * @access public
	 * @param  array, string
	 * @return array
	 *
	 */
	public function add_support_link($links, $file){

		if(!current_user_can('install_plugins')){

			return $links;

		}

		if($file == 'woocommerce-groupbuy/wc-groupbuy.php'){
			$links[] = '<a href="http://wpgenie.org/woocommerce-groupbuy/documentation/" target="_blank">'.__('Docs', 'wc_groupbuy').'</a>';
			$links[] = '<a href="http://codecanyon.net/user/wpgenie#contact" target="_blank">'.__('Support', 'wc_groupbuy').'</a>';
			$links[] = '<a href="http://codecanyon.net/user/wpgenie/" target="_blank">'.__('More WooCommerce Extensions', 'wc_groupbuy').'</a>';
		}

		return $links;
	}

	/**
	 * Add admin notice
	 *
	 * @access public
	 * @param  array, string
	 * @return array
	 *
	 */
	public function woocommerce_simple_groupbuy_admin_notice(){

		global $current_user;

		if ( current_user_can( 'manage_options' ) ) {
			$user_id = $current_user->ID;
			if(get_option('Wc_groupbuy_cron_check') != "yes" && ! get_user_meta($user_id, 'groupbuy_cron_check_ignore')){
				echo '<div class="updated">
				<p>'.sprintf (__('Group Buy deals for Woocommerce recommends that you set up a cron job to check for finished Group Buy deals: <b>%s/?groupbuy-cron=check</b>. Set it to every minute | <a href="%s">Hide Notice</a>','wc_groupbuy'),get_bloginfo('url'),add_query_arg( 'groupbuy_cron_check_ignore', '0' )).'</p>
				</div>';
			}

		}

	}

	/**
	 * Add user meta to ignor notice about crons.
	 * @access public
   *
	 */
	public function woocommerce_simple_groupbuy_ignore_notices(){

		global $current_user;
		$user_id = $current_user->ID;

		/* If user clicks to ignore the notice, add that to their user meta */
		if ( isset($_GET['groupbuy_cron_check_ignore']) && '0' == $_GET['groupbuy_cron_check_ignore'] ) {
		  add_user_meta($user_id, 'groupbuy_cron_check_ignore', 'true', true);
		}

	}

	/**
	 * Add product type
	 * @param array
	 * @return array
   *
	 */
	public function add_product_type($types){
		$types[ 'groupbuy' ] = __( 'Groupbuy', 'wc_groupbuy' );
		return $types;
	}

	/**
	 * Adds a new tab to the Product Data postbox in the admin product interface
	 *
	 * @return void
   *
	 */
	public function product_write_panel_tab($product_data_tabs){


		$groupbuy_tab = array(
				'groupbuy_tab' => array(
							'label'  => __('Group Buy', 'wc_groupbuy'),
							'target' => 'groupbuy_tab',
							'class'  => array( 'groupbuy_tab', 'show_if_groupbuy', 'hide_if_grouped', 'hide_if_external','hide_if_variable','hide_if_simple' ),
						),
			);

		return $groupbuy_tab + $product_data_tabs;
	}

	/**
	 * Adds the panel to the Product Data postbox in the product interface
	 *
	 * @return void
	 *
	 */
	public function product_write_panel(){

		global $post;
		$product = wc_get_product($post->ID);

		echo '<div id="groupbuy_tab" class="panel woocommerce_options_panel">';

		woocommerce_wp_text_input(
					array(
					'id' => '_groupbuy_min_deals',
					'class' => 'input_text required',
					'size' => '6',
					'label' => __( 'Min deals', 'wc_groupbuy' ),
					'type' => 'number',
					'custom_attributes' => array('step' => 'any', 'min'	=> '0'),
					'desc_tip' => 'true',
					'description' => __( 'Minimum deals to be sold', 'wc_groupbuy' ),
		) );
	woocommerce_wp_text_input(
				array(
				'id' => '_groupbuy_max_deals',
				'class' => 'input_text required',
		'size' => '6',
				'label' => __( 'Max deals', 'wc_groupbuy' ),
				'type' => 'number',
				'custom_attributes' => array('step' => 'any', 'min'	=> '0'),
				'desc_tip' => 'true',
				'description' => __( 'Maximum deals to be sold', 'wc_groupbuy' ),
		) );
	woocommerce_wp_text_input(
				array(
				'id' => '_groupbuy_max_deals_per_user',
				'class' => 'input_text',
		'size' => '6',
				'label' => __( 'Max deals per user', 'wc_groupbuy' ),
				'type' => 'number',
				'custom_attributes' => array('step' => 'any', 'min'	=> '0'),
				'desc_tip' => 'true',
				'description' => __( 'Max deals sold per user', 'wc_groupbuy' )
	 ) );
   woocommerce_wp_text_input(
				array(
				'id' => '_groupbuy_price',
				'class' => 'input_text',
		'label' => __( 'Group Buy Price', 'wc_groupbuy' ). ' ('.get_woocommerce_currency_symbol().')',
				'type' => 'number',
				'custom_attributes' => array('step' => 'any', 'min'	=> '0'),
				'data_type' => 'price' ,
				'desc_tip' => 'true',
				'description' => __( 'Group Buy deal price', 'wc_groupbuy' )
	 ) );
	 woocommerce_wp_text_input(
				array(
				'id' => '_groupbuy_regular_price',
				'class' => 'input_text',
		'label' => __( 'Regular Price', 'wc_groupbuy' ). ' ('.get_woocommerce_currency_symbol().')',
				'type' => 'number',
				'custom_attributes' => array('step' => 'any', 'min'	=> '0'),
				'data_type' => 'price' ,
				'desc_tip' => 'true',
				'description' => __( 'Regular product price (for comparison)', 'wc_groupbuy' )
	 ) );

		$groupbuy_dates_from 	= ( $date = get_post_meta( $post->ID, '_groupbuy_dates_from', true ) ) ?  $date  : '';
		$groupbuy_dates_to 	= ( $date = get_post_meta( $post->ID, '_groupbuy_dates_to', true ) ) ?  $date  : '';

		echo '	<p class="form-field groupbuy_dates_fields">
					<label for="_groupbuy_dates_from">' . __( 'Group buy available from date', 'wc_groupbuy' ) . '</label>
					<input type="text" class="short datetimepicker required" name="_groupbuy_dates_from" id="_groupbuy_dates_from" value="' . $groupbuy_dates_from . '" placeholder="' . _x( 'From&hellip;', 'placeholder', 'wc_groupbuy' )  . __('YYYY-MM-DD HH:MM').'"maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
				 </p>
				 <p class="form-field groupbuy_dates_fields">
					<label for="_groupbuy_dates_to">' . __( 'Group buy available to date', 'wc_groupbuy' ) . '</label>
					<input type="text" class="short datetimepicker required" name="_groupbuy_dates_to" id="_groupbuy_dates_to" value="' . $groupbuy_dates_to . '" placeholder="' . _x( 'To&hellip;', 'placeholder', 'wc_groupbuy' ) . __('YYYY-MM-DD HH:MM').'" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])"  />
				</p>';

		if ('groupbuy' == $product->get_type() && $product->is_closed()) {
			echo '<p class="form-field relist_dates_fields"><a class="button relist" href="#" id="relistgroupbuy">' . __('Relist', 'wc_simple_groupbuys') . '</a>
				   <p class="form-field relist_groupbuy_dates_fields"> 
						<label for="_relist_groupbuy_dates_from">' . __('Relist Dates', 'wc_simple_groupbuys') . '</label>
						<input type="text" class="short datetimepicker" name="_relist_groupbuy_dates_from" id="_relist_groupbuy_dates_from" value="" placeholder="' . _x('From&hellip; YYYY-MM-DD HH:MM', 'placeholder', 'wc_groupbuy') . '" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
					</p>
					<p class="form-field relist_groupbuy_dates_fields"> 
						<input type="text" class="short datetimepicker" name="_relist_groupbuy_dates_to" id="_relist_groupbuy_dates_to" value="" placeholder="' . _x('To&hellip; YYYY-MM-DD HH:MM', 'placeholder', 'wc_groupbuy') . '" maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />
					</p>
				</p>';

		}		

		do_action( 'woocommerce_product_options_groupbuy' );

		echo "</div>";
	}

	/**
	 * Saves the data inputed into the product boxes, as post meta data
	 *
	 *
	 * @param int $post_id the post (product) identifier
	 * @param stdClass $post the post (product)
	 *
	 */
	public function product_save_data($post_id, $post){

		$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( wc_clean( $_POST['product-type'] ) );

		if ( $product_type == 'groupbuy' ) {

			$product = wc_get_product( $post_id );

			if (isset($_POST['_groupbuy_max_deals'] ) && !empty($_POST['_groupbuy_max_deals']) && !empty($_POST['_groupbuy_price'])) {

					update_post_meta( $post_id, '_manage_stock', 'yes'  );

					if(get_post_meta( $post_id, '_groupbuy_participants_count',TRUE )){
						$number_of_activ_deals = intval(wc_clean( $_POST['_groupbuy_max_deals'])) - intval(get_post_meta( $post_id, '_groupbuy_participants_count',TRUE ));
						update_post_meta( $post_id, '_stock', $number_of_activ_deals  );
						if( $number_of_activ_deals > 0 ){
							update_post_meta( $post_id, '_stock_status', 'instock'  );
						}


					} else {
						update_post_meta( $post_id, '_stock', wc_clean( $_POST['_groupbuy_max_deals'])  );
						update_post_meta( $post_id, '_stock_status', 'instock'  );
					}

					update_post_meta( $post_id, '_backorders', 'no'  );


			}	else {

				update_post_meta( $post_id, '_manage_stock', 'no'  );
				update_post_meta( $post_id, '_backorders', 'no'  );
				update_post_meta( $post_id, '_stock_status', 'instock'  );

			}


	  if (isset($_POST['_groupbuy_price'] ) && !empty($_POST['_groupbuy_price'])){

		$deal_price = wc_format_decimal( wc_clean( $_POST['_groupbuy_price'] ) );
				update_post_meta( $post_id, '_groupbuy_price', $deal_price  );
				update_post_meta( $post_id, '_sale_price', $deal_price );
				update_post_meta( $post_id, '_price', $deal_price );
	  }

	  if (isset($_POST['_groupbuy_regular_price'] ) && !empty($_POST['_groupbuy_regular_price'])){

				$deal_regular_price = wc_format_decimal( wc_clean( $_POST['_groupbuy_regular_price'] ) );
				update_post_meta( $post_id, '_groupbuy_regular_price', $deal_regular_price  );
				update_post_meta( $post_id, '_regular_price', $deal_regular_price );

	  } else{
	  	delete_post_meta( $post_id, '_groupbuy_regular_price' );
	  	delete_post_meta( $post_id, '_regular_price');

	  }

		if (isset($_POST['_groupbuy_max_deals_per_user'] ) && !empty($_POST['_groupbuy_max_deals_per_user']) ){

				update_post_meta( $post_id, '_groupbuy_max_deals_per_user', wc_clean( $_POST['_groupbuy_max_deals_per_user'] ) );

				if ($_POST['_groupbuy_max_deals_per_user'] <= 1){
					update_post_meta( $post_id, '_sold_individually', 'yes'  );
				} else {
					update_post_meta( $post_id, '_sold_individually', 'no'  );
				}

			} else {

				delete_post_meta( $post_id, '_groupbuy_max_deals_per_user');
			update_post_meta( $post_id, '_sold_individually', 'no'  );
		}

			if (isset($_POST['_groupbuy_min_deals'] ))
				update_post_meta( $post_id, '_groupbuy_min_deals', wc_clean( $_POST['_groupbuy_min_deals'] ) );
			if (isset($_POST['_groupbuy_max_deals'] ))
				update_post_meta( $post_id, '_groupbuy_max_deals', wc_clean( $_POST['_groupbuy_max_deals'] ) );
			if (isset($_POST['_groupbuy_dates_from'] ))
				update_post_meta( $post_id, '_groupbuy_dates_from', wc_clean( $_POST['_groupbuy_dates_from'] ) );
			if (isset($_POST['_groupbuy_dates_to'] ))
				update_post_meta( $post_id, '_groupbuy_dates_to', wc_clean( $_POST['_groupbuy_dates_to'] ) );

			if (isset($_POST['_relist_groupbuy_dates_from']) && isset($_POST['_relist_groupbuy_dates_to']) && !empty($_POST['_relist_groupbuy_dates_from']) && !empty($_POST['_relist_groupbuy_dates_to'])) {
				$this->do_relist($post_id, $_POST['_relist_groupbuy_dates_from'], $_POST['_relist_groupbuy_dates_to']);
			}
			$product->groupbuy_update_lookup_table();
		}
	}
	function do_relist($post_id, $relist_from, $relist_to) {
		global $wpdb;

		update_post_meta($post_id, '_groupbuy_dates_from', stripslashes($relist_from));
		update_post_meta($post_id, '_groupbuy_dates_to', stripslashes($relist_to));
		update_post_meta($post_id, '_groupbuy_relisted', current_time('mysql'));
		delete_post_meta($post_id, '_groupbuy_closed');
		delete_post_meta($post_id, '_groupbuy_started');
		delete_post_meta($post_id, '_groupbuy_has_started');
		delete_post_meta($post_id, '_groupbuy_fail_reason');
		delete_post_meta($post_id, '_groupbuy_participant_id');
		delete_post_meta($post_id, '_groupbuy_participants_count');
		delete_post_meta($post_id, '_groupbuy_order_hold_on');
		
		
		$groupbuy_max_deals = get_post_meta($post_id, '_groupbuy_max_deals', true);
		update_post_meta($post_id, '_stock', $groupbuy_max_deals  );
		update_post_meta($post_id, '_stock_status', 'instock');

		$order_id = get_post_meta($post_id, 'order_id', true);
		// check if the custom field has a value
		if (!empty($order_id)) {
			delete_post_meta($post_id, '_order_id');
		}

		$wpdb->delete(
			$wpdb->usermeta, array(
				'meta_key'   => 'my_groupbuys',
				'meta_value' => $post_id,
			), array( '%s', '%s' )
		);

		do_action( 'woocommerce_groupbuy_do_relist', $post_id, $relist_from, $relist_to );


	}


	/**
	 * Add dropdown to filter groupbuy
	 *
	 * @param  (wp_query object) $query
	 *
	 * @return Void
	 */
	function admin_posts_filter_restrict_manage_posts(){

		//only add filter to post type you want
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'product'){
			$values = array(
				'Active' => 'active',
				'Finished' => 'finished',
				'Fail' => 'fail',
			);
			?>
			<select name="wc_groupbuy_filter">
			<option value=""><?php _e('Group Buy deals filter by ', 'wc_groupbuy'); ?></option>
			<?php
				$current_v = isset($_GET['wcl_filter'])? $_GET['wcl_filter']:'';
				foreach ($values as $label => $value) {
					printf
						(
							'<option value="%s"%s>%s</option>',
							$value,
							$value == $current_v? ' selected="selected"':'',
							$label
						);
					}
			?>
			</select>
			<?php
		}
	}

	/**
	 * If submitted filter by post meta
	 *
	 * make sure to change META_KEY to the actual meta key
	 * and POST_TYPE to the name of your custom post type
	 * @param  (wp_query object) $query
	 *
	 * @return Void
	 */
	function admin_posts_filter( $query ){
		global $pagenow;

		if (isset($_GET['post_type']) &&  $_GET['post_type'] == 'product' && is_admin() && $pagenow=='edit.php' && isset($_GET['wc_groupbuy_filter']) && $_GET['wc_groupbuy_filter'] != '') {

			switch ($_GET['wc_groupbuy_filter']) {
				case 'active':
					$query->query_vars['meta_query'] = array(
						array(
							
								'key'     => '_groupbuy_closed',
								'compare' => 'NOT EXISTS',
						)
					);

					$taxquery = $query->get('tax_query');
					if (!is_array($taxquery)) {
						$taxquery = array();
					}

					$taxquery []=
						array(
							'taxonomy' => 'product_type',
							'field' => 'slug',
							'terms' => 'groupbuy',

					);

					$query->set( 'tax_query', $taxquery );
				break;
				case 'finished':
					$query->query_vars['meta_query'] = array(
						array(
								'key'     => '_groupbuy_closed',
								'compare' => 'EXISTS',
						)
					   
					);

					break;
				case 'fail':
					$query->query_vars['meta_key'] = '_groupbuy_closed';
					$query->query_vars['meta_value'] = '1';

					break;

			}
		}
	}

	/**
	 *  Add groupbuy setings tab to woocommerce setings page
	 *
	 * @access public
	 *
	 */
	function groupbuy_settings_class($settings){

				$settings[] = include(  plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-settings-groupbuy.php' );
				return $settings;
	}

	/**
	 *  Add meta box to the product editing screen
	 *
	 * @access public
	 *
	 */
	function woocommerce_simple_groupbuy_meta() {

		global $post;

		$product_data = wc_get_product(  $post->ID );
		if($product_data){
			if($product_data->get_type() == 'groupbuy'){
				add_meta_box('groupbuy', __( 'Group Buy', 'wc_groupbuy' ), array($this,'woocommerce_simple_groupbuy_meta_callback'), 'product', 'normal','default');
			}
		}

	}

	/**
	 *  Callback for adding a meta box to the product editing screen used in woocommerce_simple_groupbuy_meta
	 *
	 * @access public
	 *
	 */
	function woocommerce_simple_groupbuy_meta_callback(){

		global $post;
			$product_data = wc_get_product(  $post->ID );

			$groupbuy_history = apply_filters('woocommerce__groupbuy_history_data', $product_data->groupbuy_history());
			$heading = esc_html( apply_filters('woocommerce_groupbuy_history_heading', __( 'Group Buy History', 'wc_groupbuy' ) ) );
			$groupbuy_winers = get_post_meta($post->ID, '_groupbuy_winners');

			
			$groupbuy_relisted = $product_data->get_groupbuy_relisted();
			if ( ! empty( $groupbuy_relisted ) ) {
			?>
				<p><?php esc_html_e( 'Groupbuy has been relisted on:', 'wc_groupbuy' ); ?> <?php echo date_i18n( get_option( 'date_format' ), strtotime( $groupbuy_relisted )).' '.date_i18n( get_option( 'time_format' ), strtotime( $groupbuy_relisted )); ?></p>
			<?php
			}
			if(($product_data->is_closed() === TRUE ) and ($product_data->is_started() === TRUE )) : ?>
				<p><?php _e('Group Buy deal has finished', 'wc_groupbuy') ?></p>
				<?php if ($product_data->get_groupbuy_fail_reason() == '1'){
					echo "<p>";
					 _e('Group Buy deal failed because there were no participants', 'wc_groupbuy');
					 echo "</p>";
				} elseif($product_data->get_groupbuy_fail_reason() == '2'){
					echo "<p>";
					_e('Group Buy deal failed because there was not enough participants', 'wc_groupbuy');
					echo " <button class='button button-primary do-api-refund' href='#' id='groupbuy-refund' data-product_id='".$product_data->get_id()."'>";
					_e('Refund ', 'wc_groupbuy');
					echo "</button>";
					echo '<div id="refund-status"></div>';
					echo "<//p>";
				}
				if($groupbuy_winers){ ?>

					<?php if (count($groupbuy_winers) < 2)  {?>

						<?php $winnerid = $groupbuy_winers[0]; ?>
						<p><?php _e('Group Buy deal winner is', 'wc_groupbuy') ?>: <span><a href='<?php get_edit_user_link($winnerid)?>'><?php echo get_userdata($winnerid)->display_name ?></a></span></p>
					<?php } else { ?>

						<p><?php _e('Group Buy deal winners are', 'wc_groupbuy') ?>:
							<ul>
							<?php foreach ($groupbuy_winers as $key => $winnerid) { ?>
									<li><a href='<?php get_edit_user_link($winnerid)?>'><?php echo get_userdata($winnerid)->display_name ?></a></li>
							<?php } ?>
							</ul>

						</p>


					<?php }	 ?>


				<?php } ?>


			<?php endif; ?>

			<h2><?php echo $heading; ?></h2>
			<table class="groupbuy-table">
			<?php if($groupbuy_history):?>

					<thead>
						<tr>
							<th><?php _e('Date', 'wc_groupbuy') ?></th>
							<th><?php _e('User', 'wc_groupbuy') ?></th>
							<th><?php _e('Order', 'wc_groupbuy') ?></th>
							<th class="actions"><?php _e('Actions', 'wc_groupbuy') ?></th>
						</tr>
					</thead>
					
					<?php 
						foreach ($groupbuy_history as $history_value) {
							if ( $history_value->date < $product_data->get_groupbuy_relisted() && ! isset( $displayed_relist ) ) {
								echo '<tr>';
								echo '<td class="date">'. date_i18n( get_option( 'date_format' ), strtotime( $product_data->get_groupbuy_relisted() )).' '.date_i18n( get_option( 'time_format' ), strtotime( $product_data->get_groupbuy_relisted() )) . '</td>';
								echo '<td colspan="4"  class="relist">';
								echo __( 'Groupbuy relisted', 'wc_groupbuy' );
								echo '</td>';
								echo '</tr>';
								$displayed_relist = true;
							}
						echo "<tr>";
						echo '<td class="date">'. date_i18n( get_option( 'date_format' ), strtotime( $history_value->date ) ).' '.date_i18n( get_option( 'time_format' ), strtotime( $history_value->date ) ). '</td>';
						echo "<td class='username'><a href='".get_edit_user_link($history_value->userid)."'>".get_userdata($history_value->userid)->display_name."</a></td>";
						echo "<td class='username'><a href='".admin_url( 'post.php?post='.$history_value->orderid.'&action=edit' )."'>".$history_value->orderid."</a></td>";
						echo "<td class='action'> <a href='#' data-id='".$history_value->id."' data-postid='".$post->ID."'    >".__('Delete', 'wc_groupbuy')."</a></td>";
						echo "</tr>";


					}?>
					<tr class="start">
						<?php if ($product_data->is_started() === TRUE ){
							echo '<td class="date">'.$product_data->get_groupbuy_dates_from().'</td>';
							echo '<td colspan="3"  class="started">';
							echo apply_filters('groupbuy_history_started_text', __( 'Group Buy deal started', 'wc_groupbuy' ), $product_data);
							echo '</td>';

						} else {
							echo '<td  class="date">'.$product_data->get_groupbuy_dates_from().'</td>';
							echo '<td colspan="3"  class="starting">';
							echo apply_filters('groupbuy_history_starting_text', __( 'Group Buy deal starting', 'wc_groupbuy' ), $product_data);
							echo '</td>' ;
						}?>
					</tr>

				<?php endif;?>
			</table>
			</ul><?php
	}
	/**
	 * groupbuy order hold on
	 *
	 * Checks for groupbuy product in order when order is created on checkout before payment
	 * @access public
	 * @param int, array
	 * @return void
	 */
	function groupbuy_order_hold_on($order_id){
		
		$order  = new WC_Order( $order_id );
		if ( $order) {
			if ( $order_items =  $order->get_items()) {
					foreach ( $order_items as $item_id => $item ) {
						if ( function_exists( 'wc_get_order_item_meta' ) ){
							$item_meta = wc_get_order_item_meta( $item_id, '' );
						} else{
							$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
						}
						$product_id   = $this->get_main_wpml_product_id( $item_meta['_product_id'][0] );
						$product_data = wc_get_product($product_id);
						if(is_object($product_data) && $product_data->get_type() == 'groupbuy' ){
							update_post_meta( $order_id, '_groupbuy', '1' );
							add_post_meta( $product_id, '_groupbuy_order_hold_on', $order_id );
						}
					}
			}
		}	
	}

	 /**
	 * groupbuy order
	 *
	 * Checks for groupbuy product in order and assign order id to groupbuy product
	 *
	 * @access public
	 * @param int, array
	 * @return void
	 */
	function groupbuy_order($order_id){
		global $wpdb;

		$log = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wc_groupbuy_log WHERE orderid=%d", $order_id) );

		if(!is_null($log)){
			return;
		}

		$order  = new WC_Order( $order_id );

		if ( $order) {

			if ( $order_items =  $order->get_items()) {

				foreach ( $order_items as $item_id => $item ) {

					if ( function_exists( 'wc_get_order_item_meta' ) ){
						$item_meta = wc_get_order_item_meta( $item_id, '' );
					} else{
						$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					}
					$product_id   = $this->get_main_wpml_product_id( $item_meta['_product_id'][0] );
					$product_data = wc_get_product($product_id);
					$product_data_type = method_exists( $product_data, 'get_type' ) ? $product_data->get_type() : $product_data->product_type;
					if($product_data_type == 'groupbuy' ){

						update_post_meta( $order_id, '_groupbuy', '1' );
						add_post_meta( $product_id, '_order_id', $order_id );
						delete_post_meta( $product_id, '_groupbuy_order_hold_on', $order_id );
						for ($i=0; $i < $item_meta["_qty"][0]; $i++) {
							add_post_meta( $product_id, '_groupbuy_participant_id', $order->get_user_id() );
							$participants = get_post_meta( $product_id, '_groupbuy_participants_count', TRUE ) ? get_post_meta( $product_id, '_groupbuy_participants_count', TRUE ) : 0;
							update_post_meta( $product_id, '_groupbuy_participants_count', intval($participants) + 1 );
							$this->add_groupbuy_to_user_metafield( $product_id, $order->get_user_id() );
							$this->log_participant($product_id, $order->get_user_id(), $order_id);
							
						}
						$order->update_status( 'groupbuy_progres' );
						do_action('wc_groupbuy_participate',$product_id, $order->get_user_id() , $order_id);

					}
				}
			}
		}
	}
	/**
	 * groupbuy order canceled
	 *
	 * Checks for groupbuy product in order and assign order id to groupbuy product
	 *
	 * @access public
	 * @param int, array
	 * @return void
	 */
	function groupbuy_order_canceled($order_id){
		global $wpdb;

		$log = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wc_groupbuy_log WHERE orderid=%d", $order_id) );

		if(is_null($log)){
			return;
		}

		$order  = new WC_Order( $order_id );

		if ( $order) {

			if ( $order_items =  $order->get_items()) {

				foreach ( $order_items as $item_id => $item ) {

					if ( function_exists( 'wc_get_order_item_meta' ) ){
						$item_meta = wc_get_order_item_meta( $item_id, '' );
					} else{
						$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					}
					$product_id   = $this->get_main_wpml_product_id( $item_meta['_product_id'][0] );
					$product_data = wc_get_product($product_id);
					$product_data_type = method_exists( $product_data, 'get_type' ) ? $product_data->get_type() : $product_data->product_type;
					if($product_data_type == 'groupbuy' ){

						update_post_meta( $order_id, '_groupbuy', '1' );
						add_post_meta( $product_id, '_order_id', $order_id );
						delete_post_meta( $product_id, '_groupbuy_order_hold_on', $order_id );
						for ($i=0; $i < $item_meta["_qty"][0]; $i++) {

							delete_post_meta( $product_id, '_groupbuy_participant_id', $order->get_user_id() );

							$participants = get_post_meta( $product_id, '_groupbuy_participants_count', TRUE ) ? get_post_meta( $product_id, '_groupbuy_participants_count', TRUE ) : 0;

							if($participants > 0 ){
								update_post_meta( $product_id, '_groupbuy_participants_count', intval($participants) - 1 );
							}	
							$this->remove_groupbuy_from_user_metafield( $product_id, $order->get_user_id() );
							$this->delete_log_participant($product_id, $order->get_user_id(), $order_id);

						}

						do_action('wc_groupbuy_cancel_participation',$product_id, $order->get_user_id() , $order_id);

					}
				}
			}
		}
	}

	/**
	 * groupbuy order failed
	 *
	 * Checks for groupbuy product in failed order
	 *
	 * @access public
	 * @param int, array
	 * @return void
	 */
	function groupbuy_order_failed($order_id){
		global $wpdb;

		$order  = new WC_Order( $order_id );

		if ( $order) {

			if ( $order_items =  $order->get_items()) {

				foreach ( $order_items as $item_id => $item ) {

					if ( function_exists( 'wc_get_order_item_meta' ) ){
						$item_meta = wc_get_order_item_meta( $item_id, '' );
					} else{
						$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					}
					$product_id   = $this->get_main_wpml_product_id( $item_meta['_product_id'][0] );
					$product_data = wc_get_product($product_id);
					$product_data_type = method_exists( $product_data, 'get_type' ) ? $product_data->get_type() : $product_data->product_type;
					if($product_data_type == 'groupbuy' ){
						delete_post_meta( $product_id, '_groupbuy_order_hold_on', $order_id );
					}
				}
			}
		}
	}

	/**
	 * Delete logs when groupbuy is deleted
	 *
	 * @access public
	 * @param  string
	 * @return void
	 *
	 */
	function del_groupbuy_logs( $post_id){
		global $wpdb;
		if ( $wpdb->get_var( $wpdb->prepare( 'SELECT groupbuy_id FROM '.$wpdb -> prefix .'wc_groupbuy_log WHERE groupbuy_id = %d', $post_id ) ) )
			return $wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb -> prefix .'wc_groupbuy_log WHERE groupbuy_id = %d', $post_id ) );

		return true;
	}


	/**
	 * Duplicate post
	 *
	 * Clear metadata when copy groupbuy
	 *
	 * @access public
	 * @param  array
	 * @return string
	 *
	 */
	 function woocommerce_duplicate_product($postid){
		$product = wc_get_product($postid);
		if (!$product)
			return FALSE;
		$product_type = method_exists( $product, 'get_type' ) ? $product->get_type() : $product->product_type;		
		if ($product_type != 'groupbuy')
			return FALSE;

		delete_post_meta($postid, '_groupbuy_participants_count');
		delete_post_meta($postid, '_groupbuy_closed');
		delete_post_meta($postid, '_groupbuy_fail_reason');
		delete_post_meta($postid, '_groupbuy_dates_to');
		delete_post_meta($postid, '_groupbuy_dates_from');
		delete_post_meta($postid, '_order_id');
		delete_post_meta($postid, '_groupbuy_order_hold_on');

		return TRUE;

	 }

	 /**
	 * Log Group Buy participant
	 *
	 * @param  int, int
	 * @return void
	 *
	 */
	public function log_participant($product_id, $current_user_id, $order_id) {

		global $wpdb;

		$log_bid = $wpdb -> insert($wpdb -> prefix . 'wc_groupbuy_log', array('userid' => $current_user_id, 'groupbuy_id' => $product_id,  'orderid' => $order_id,  'date' => current_time('mysql')), array('%d', '%d', '%d', '%s'));
	}

	/**
	 * Log Group Buy participant
	 *
	 * @param  int, int
	 * @return void
	 *
	 */
	public function delete_log_participant($product_id, $current_user_id, $order_id) {

		global $wpdb;

		$wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix."wc_groupbuy_log WHERE userid= %d AND groupbuy_id=%d AND orderid=%d", $current_user_id,$product_id,$order_id ) );
	}

	/**
	 * Ajax delete participate entry
	 *
	 * Function for deleting participate entry in wp admin
	 *
	 * @access public
	 * @param  array
	 * @return string
	 *
	 */
	function wp_ajax_delete_participate_entry(){

		global $wpdb;

		if ( !current_user_can('edit_product', $_POST["postid"]))  die();

		if($_POST["postid"] && $_POST["logid"]){

			$postid = intval($_POST["postid"]);
			$log = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wc_groupbuy_log WHERE id=%d", $_POST["logid"]) );
			$participants = get_post_meta($postid,'_groupbuy_participant_id', FALSE);

			if(!is_null($log)){

				$wpdb->query( $wpdb->prepare("DELETE FROM ".$wpdb->prefix."wc_groupbuy_log WHERE id= %d", $_POST["logid"]) );
				$pos = array_search($log->userid, $participants);
				unset($participants[$pos]);
				delete_post_meta($postid,'_groupbuy_participant_id');
				delete_post_meta($postid,'_order_id', $log->orderid);
				$count = get_post_meta( $postid, '_groupbuy_participants_count', TRUE ) ? get_post_meta( $postid, '_groupbuy_participants_count', TRUE ) : 0;
				$this->remove_groupbuy_from_user_metafield( $postid, $log->userid );
				if($count > 0){
						update_post_meta( $postid, '_groupbuy_participants_count', intval($count) - 1 );
				}

				foreach ($participants as $key => $value) {
						add_post_meta( $postid, '_groupbuy_participant_id', $value );
				}
				echo 'deleted';
				exit;
			}
			echo 'failed';
			exit;
		}
		echo 'failed';
		exit;
	}

	/**
	 * Sync meta with wpml
	 *
	 * Sync meta trough translated post
	 *
	 * @access public
	 * @param bool $url (default: false)
	 * @return void
	 *
	 */
	function sync_metadata_wpml($data){

		global $sitepress;

		$deflanguage = $sitepress->get_default_language();

		if(is_array($data)){
				$product_id = $data['product_id'];
		} else {
				$product_id = $data;
		}

		$meta_values    = get_post_meta( $product_id);
		$orginalid      = $sitepress->get_original_element_id($product_id,'post_product');
		$trid           = $sitepress->get_element_trid($product_id,'post_product');
		$all_posts      = $sitepress->get_element_translations($trid, 'post_product');

		unset ($all_posts[$deflanguage]);

		if (!empty($all_posts)){

			foreach ($all_posts as $key => $translatedpost) {

				if (isset($meta_values['_groupbuy_max_deals'][0]))
						update_post_meta($translatedpost->element_id, '_groupbuy_max_deals', $meta_values['_groupbuy_max_deals'][0]);
				if (isset($meta_values['_groupbuy_min_deals'][0]))
						update_post_meta($translatedpost->element_id, '_groupbuy_min_deals', $meta_values['_groupbuy_min_deals'][0]);
				if (isset($meta_values['_groupbuy_num_winners'][0]))
						update_post_meta($translatedpost->element_id, '_groupbuy_num_winners', $meta_values['_groupbuy_num_winners'][0]);
				if (isset($meta_values['_groupbuy_dates_from'][0]))
						update_post_meta($translatedpost->element_id, '_groupbuy_dates_from', $meta_values['_groupbuy_dates_from'][0]);
				if (isset($meta_values['_groupbuy_dates_to'][0]))
						update_post_meta($translatedpost->element_id, '_groupbuy_dates_to', $meta_values['_groupbuy_dates_to'][0]);
				if (isset($meta_values['_groupbuy_closed'][0]))
						update_post_meta($translatedpost->element_id, '_groupbuy_closed', $meta_values['_groupbuy_closed'][0]);
				if (isset($meta_values['_groupbuy_closed'][0]))
					update_post_meta($translatedpost->element_id, '_groupbuy_started', $meta_values['_groupbuy_started'][0]);
				if (isset($meta_values['_groupbuy_closed'][0]))
					update_post_meta($translatedpost->element_id, '_groupbuy_has_started', $meta_values['_groupbuy_has_started'][0]);
				if (isset($meta_values['_groupbuy_fail_reason'][0]))
						update_post_meta($translatedpost->element_id, '_groupbuy_fail_reason', $meta_values['_groupbuy_fail_reason'][0]);
				if (isset($meta_values['_order_id'][0]))
						update_post_meta($translatedpost->element_id, '_order_id', $meta_values['_order_id'][0]);

				if (isset($meta_values['_groupbuy_participants_count'][0]))
						update_post_meta( $translatedpost->element_id, '_groupbuy_participants_count',  $meta_values['_groupbuy_participants_count'][0] );
				if (isset($meta_values['_groupbuy_winners'][0]))
						update_post_meta( $translatedpost->element_id, '_groupbuy_winners',  $meta_values['_groupbuy_winners'][0]  );
				if (isset($meta_values['_groupbuy_participant_id'][0])){
						delete_post_meta($translatedpost->element_id, '_groupbuy_participant_id');
						foreach ( $meta_values['_groupbuy_winners'] as $key => $value) {
								add_post_meta( $translatedpost->element_id, '_groupbuy_participant_id', $value   );
						}
				}

				if (isset($meta_values['_regular_price'][0]))
						update_post_meta( $translatedpost->element_id, '_regular_price',  $meta_values['_regular_price'][0]  );
				if (isset($meta_values['_groupbuy_wpml_language'][0]))
						update_post_meta( $translatedpost->element_id, '_groupbuy_wpml_language',  $meta_values['_groupbuy_wpml_language'][0]  );
			}
		}
	}
	/**
	 *
	 * Add last language in use to custom meta of groupbuy
	 *
	 * @access public
	 * @param int
	 * @return void
	 *
	 */
	function add_language_wpml_meta($data){
		$language = isset($_SESSION['wpml_globalcart_language']) ? $_SESSION['wpml_globalcart_language'] : ICL_LANGUAGE_CODE;
		if ( isset( $data['product_id'] ) ) {
			update_post_meta($data['product_id'], '_groupbuy_wpml_language', $language);
		}
	}

	function change_email_language($product_id){

		global $sitepress;

		$lang = get_post_meta($product_id, '_groupbuy_wpml_language', true);

		if ($lang){

			$sitepress->switch_lang($lang,true);
			unload_textdomain('woocommerce');
			unload_textdomain('default');
			wc()->load_plugin_textdomain();
			load_default_textdomain();
			global $wp_locale;
			$wp_locale = new WP_Locale();
		}
	}

  /**
	 *
	 * Handle a refund via the edit order screen
	 *
	 */
	public static function groupbuy_refund() {

		check_ajax_referer( 'groupbuy-refund', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			die(-1);
		}

		$item_ids =array();$succes =array();$error = array();

		$product_id    = absint( $_POST['product_id'] );
		$refund_amount = 0;
		$refund_reason = __( 'Group Buy deal failed. No minimum ticket sold', 'wc_groupbuy' );
		$refund        = false;
		$response_data = array();

		$orders = self::get_product_orders($product_id);

		$groupbuy_order_refunded = get_post_meta($product_id,'_groupbuy_order_refunded');

		$relist_date = get_post_meta($product_id, '_groupbuy_relisted', true);

		foreach ($orders as $key => $order_id) {

			$order         = wc_get_order( $order_id );
			if ( $relist_date && $relist_date > $order->get_date_modified()->date( 'Y-m-d H:i:s' ) ){
				continue;
			}

			if(in_array($order_id, $groupbuy_order_refunded)){
				$error[$order_id] = __( 'Group Buy deal amount allready returned', 'woocommerce' );
				continue;
			}

			try {

				// Validate that the refund can occur
				$order         = wc_get_order( $order_id );
				$order_items   = $order->get_items();
				$refund_amount = 0;


				// Prepare line items which we are refunding
				$line_items = array();
				if ( $order_items =  $order->get_items()) {
					
					foreach ( $order_items as $item_id => $item ) {


						if ( function_exists( 'wc_get_order_item_meta' ) ){
							$item_meta = wc_get_order_item_meta( $item_id, '' );
						} else{
							$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
						}

						$product_data = wc_get_product($item_meta["_product_id"][0]);

						$product_data_type = method_exists( $product_data, 'get_type' ) ? $product_data->get_type() : $product_data->product_type;	

						if($product_data->product_type == 'groupbuy'  && $item_meta['_product_id'][0] == $product_id  ){

							$item_ids[] = $product_data->get_id();

							$refund_amount = wc_format_decimal($refund_amount)  + wc_format_decimal($item_meta['_line_total'] [0]);
							
							$line_items[ $product_data->get_id() ] = array( 'qty' => $item_meta['_qty'], 'refund_total' => wc_format_decimal($item_meta['_line_total']), 'refund_tax' =>  array_map( 'wc_format_decimal', $item_meta['_line_tax_data']) );

						}
					}
				}

				$max_refund  = wc_format_decimal( $refund_amount - $order->get_total_refunded() );

				if ( ! $refund_amount || $max_refund < $refund_amount || 0 > $refund_amount ) {
					throw new exception( __( 'Invalid refund amount', 'woocommerce' ) );
				}

				if ( WC()->payment_gateways() ) {
					$payment_gateways = WC()->payment_gateways->payment_gateways();
				}
				$payment_method = method_exists( $order, 'get_payment_method' ) ? $order->get_payment_method() : $order->payment_method;
				if ( isset( $payment_gateways[ $payment_method ] ) && $payment_gateways[ $payment_method ]->supports( 'refunds' ) ) {
					$result = $payment_gateways[ $payment_method ]->process_refund( $order_id, $refund_amount, $refund_reason );

					do_action( 'woocommerce_refund_processed', $refund, $result );

					if ( is_wp_error( $result ) ) {
						throw new Exception( $result->get_error_message() );
					} elseif ( ! $result ) {
						throw new Exception( __( 'Refund failed', 'woocommerce' ) );
					} else {
						// Create the refund object
						$refund = wc_create_refund( array(
							'amount'     => $refund_amount,
							'reason'     => $refund_reason,
							'order_id'   => $order_id,
							'line_items' => $line_items,
						) );

						$refund_id = method_exists( $refund, 'get_id' ) ? $refund->get_id() : $refund->id;

						if ( is_wp_error( $refund ) ) {
							throw new Exception( $refund->get_error_message() );
						}

						add_post_meta($product_id, '_groupbuy_order_refunded', $order_id );
					}

					// Trigger notifications and status changes
					if ( $order->get_remaining_refund_amount() > 0 || ( $order->has_free_item() && $order->get_remaining_refund_items() > 0 ) ) {

						do_action( 'woocommerce_order_partially_refunded', $order_id, $refund_id );

					} else {

						do_action( 'woocommerce_order_fully_refunded', $order_id, $refund_id );

						$order->update_status( apply_filters( 'woocommerce_order_fully_refunded_status', 'refunded', $order_id, $refund_id ) );
						$response_data['status'] = 'fully_refunded';
					}

					do_action( 'woocommerce_order_refunded', $order_id, $refund_id );

					// Clear transients
					wc_delete_shop_order_transients( $order_id );
					$succes[$order_id] = __( 'Refunded', 'woocommerce' );
					
				} elseif ( isset( $payment_gateways[ $payment_method ] ) && ! $payment_gateways[ $payment_method ]->supports( 'refunds' ) ){
					$error[ $order_id ] =  esc_html__( 'Payment does not support refund', 'woocommerce' );
				}


			} catch ( Exception $e ) {
				if ( $refund && is_a( $refund, 'WC_Order_Refund' ) ) {
					wp_delete_post( $refund_id, true );
				}

				$error[$order_id] =  $e->getMessage();
			}
		}

		wp_send_json (array('error'=> $error, 'succes' =>$succes));

	}

	/**
	 * Get the orders for a product
	 *
	 * @since 1.0.1
	 * @param int $id the product ID to get orders for
	 * @param string fields  fields to retrieve
	 * @param string $filter filters to include in response
	 * @param string $status the order status to retrieve
	 * @param $page  $page   page to retrieve
	 * @return array
	 */
	public static function get_product_orders( $id ) {

		global $wpdb;

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$order_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT order_id
			FROM {$wpdb->prefix}woocommerce_order_items
			WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_product_id' AND meta_value = %d )
			AND order_item_type = 'line_item'
		 ", $id ) );

		if ( empty( $order_ids ) ) {
			return array( 'orders' => array() );
		}

		return $order_ids;

	}

	/**
	 * Ouput custom columns for products.
	 *
	 * @param string $column
	 *
	 */
	public function render_product_columns( $column ) {

			global $post, $the_product;

			if ( empty( $the_product ) || $the_product->get_id() != $post->ID ) {
				$the_product = wc_get_product( $post );
			}

			if($column == 'product_type'){
				$the_product_type = method_exists( $the_product, 'get_type' ) ? $the_product->get_type() : $the_product->product_type;
				if ( 'groupbuy' == $the_product_type ) {
						$class ='';
						$closed = $the_product->get_groupbuy_closed();
						if($closed == '2'){	$class .= ' finished '; }

						if($closed == '1'){	$class .= ' fail '; }

						echo '<span class="groupbuy-status '.$class.'"></span>';
				}

			}

	}

	/**
	 * Search for [vendor] tag in recipients and replace it with author email
	 *
	 */
	public function add_vendor_to_email_recipients($recipient, $object) {
		
		if(!$object){
			return $recipient;
		}

		$key = FALSE;
		$author_info = false;
		$arrayrec = explode(',', $recipient);
		
		$post_id = method_exists( $object, 'get_id' ) ? $object->get_id() : $object->id;
		$post_author = get_post_field( 'post_author', $post_id );
		if (!empty($post_author)) {
			$author_info = get_userdata($post_author);
			$key = array_search($author_info->user_email, $arrayrec);
		}

		if (!$key && $author_info) {
			$recipient = str_replace('[vendor]', $author_info->user_email, $recipient);

		} else {
			$recipient = str_replace('[vendor]', '', $recipient);
		}

		return $recipient;
	}

	function check_for_plugin_update($checked_data) {
				global $wp_version;
				
				//Comment out these two lines during testing.
				if (empty($checked_data->checked)){
					return $checked_data;
				}
				
				$args = array(
					'slug' => $this->plugin_slug,
					'version' => $this->version,
				);
				$request_string = array(
						'body' => array(
							'action' => 'basic_check', 
							'request' => serialize($args),
							'api-key' => md5(get_bloginfo('url')),
							'purchase-code' => get_option('wc_groupbuy_purchase_code')
						),
						'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
					);
				
			
				// Start checking for an update
				$raw_response = wp_remote_post($this->api_url, $request_string);


												
				if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
					$response = unserialize($raw_response['body']);
				
				if (is_object($response) && !empty($response)) // Feed the update data into WP updater
					$checked_data->response[$this->plugin_basename] = $response;
				
				
				return $checked_data;
			}

			function plugin_api_call($def, $action, $args) {

				global $wp_version;
				
				if (!isset($args->slug) || ($args->slug != $this->plugin_slug))
					return $def;
				
				
				// Get the current version
				$plugin_info = get_site_transient('update_plugins');
				$current_version =  $this->version;
				$args->version = $current_version;
				
				$request_string = array(
						'body' => array(
							'action' => $action, 
							'request' => serialize($args),
							'api-key' => md5(get_bloginfo('url')),
							'purchase-code' => get_option('wc_groupbuy_purchase_code')
						),
						'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
					);
				
				$request = wp_remote_post($this->api_url, $request_string);
				
				if (is_wp_error($request)) {
					$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
				} else {
					$res = unserialize($request['body']);
					
					if ($res === false)
						$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
				}


				
				return $res;
			}

			 /**
			 * Get main product id for multilanguage purpose
			 *
			 * @access public
			 * @return int
			 *
			 */

			function get_main_wpml_product_id( $id ) {

				return intval( apply_filters( 'wpml_object_id', $id, 'product', false, apply_filters( 'wpml_default_language', null ) ) );

			}

			/**
			* Add groupbuy to user custom field
			*
			* @access public
			* @return void
			*
			*/
			function add_groupbuy_to_user_metafield( $product_id, $user_id ) {

				$my_groupbuys = get_user_meta( $user_id, 'my_groupbuys', false );
				if ( ! in_array( $product_id, $my_groupbuys ) ) {
						add_user_meta( $user_id, 'my_groupbuys', $product_id, false );
				}

			}

			/**
			* Delete groupbuy from user custom field
			*
			* @access public
			* @return void
			*
			*/
			function remove_groupbuy_from_user_metafield( $product_id, $user_id ) {
				$my_groupbuys = get_user_meta( $user_id, 'my_groupbuys', false );
				if (is_array( $my_groupbuys ) && in_array( $product_id, $my_groupbuys ) ) {
					delete_user_meta( $user_id, 'my_groupbuys', $product_id );
				}

			}


			// Register new status
			function register_groupbuy_order_status() {
				register_post_status( 'wc-groupbuy_progres', array(
				'label'                     => _x( 'Group buy in progress', 'Order status', 'woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Group buy in progress <span class="count">(%s)</span>', 'Group buy in progress<span class="count">(%s)</span>', 'woocommerce' )
				) );
				register_post_status( 'wc-groupbuy_failed', array(
				'label'                     => _x( 'Group buy failed', 'Order status', 'woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Group buy failed <span class="count">(%s)</span>', 'Group buy failed<span class="count">(%s)</span>', 'woocommerce' )
				) );
			}

			function add_groupbuy_to_order_statuses( $order_statuses ) {
				$order_statuses['wc-groupbuy_progres'] = _x( 'Group buy in progress', 'Order status', 'woocommerce' );
				$order_statuses['wc-groupbuy_failed'] = _x( 'Group buy failed', 'Order status', 'woocommerce' );

				return $order_statuses;
			}

			function change_order_status_on_closing_groupbuy($product_id){
				if ( $product_id ) {
					$product =  wc_get_product( $product_id );
					$orders = $this->get_product_orders($product_id);
					$relist_date = get_post_meta($product_id, '_groupbuy_relisted', true);

					foreach ($orders as $key => $order_id) {

						$order         = wc_get_order( $order_id );
						if ( ! $order  || ( $relist_date && $relist_date > $order->get_date_modified()->date( 'Y-m-d H:i:s' ) ) ) {
							continue;
						}
						if(is_object($order) && ( $order->get_status( 'view' ) == 'groupbuy_progres') ){
							if( $product->get_groupbuy_closed() == '1'){
								$order->update_status( 'groupbuy_failed' );
							} elseif($product->get_groupbuy_closed() == '2') {
								$order->update_status( 'processing' );
							}
						}
					}
				}

			}



}
