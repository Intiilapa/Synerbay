<?php
/**
 * WooCommerce Group Buy Settings
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (  class_exists( 'WC_Settings_Page' ) ) :

/**
 * WC_Settings_Accounts
 */
class WC_Settings_groupbuy extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

      $this->id    = 'simple_groupbuy';
      $this->label = __( 'Group Buy Deals', 'wc_groupbuy' );

      add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
      add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
      add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'woocommerce_' . $this->id . '_settings', array(

			array(	'title' => __( 'Woocommerce Group Buy options', 'wc_groupbuy' ), 'type' => 'title','desc' => '', 'id' => 'simple_groupbuy_options' ),
          // array(
          //             'title'       => __( "Purchase code", 'wc_groupbuy' ),
          //             'desc'        => __( "Envato purchase code for updating plugin.", 'wc_groupbuy' ),
          //             'type'        => 'text',
          //             'id'        => 'wc_groupbuy_purchase_code',
          //             'default'       => ''
          //           ),
          array(
                  'title' 			=> __( 'Past Group Buy deals', 'wc_groupbuy' ),
                  'desc'        => __( 'Show finished Group Buy deals', 'wc_groupbuy' ),
                  'type' 				=> 'checkbox',
                  'id'					=> 'simple_groupbuy_finished_enabled',
                  'default' 		=> 'no'
          ),
          array(
                  'title' 			=> __( 'Future Group Buy deals', 'wc_groupbuy' ),
                  'desc'        => __( 'Show Group Buy deals that did not start yet', 'wc_groupbuy' ),
                  'type' 				=> 'checkbox',
                  'id'					=> 'simple_groupbuy_future_enabled',
                  'default' 		=> 'yes'
          ),
          array(
                  'title' 			=> __( "Do not show Group Buy deals on shop page", 'wc_groupbuy' ),
                  'desc'        => __( 'Do not mix Group Buy deals and regular products on shop page. Just show Group Buy deals on the groupbuy page (groupbuy base page)', 'wc_groupbuy' ),
                  'type' 				=> 'checkbox',
                  'id'					=> 'simple_groupbuy_dont_mix_shop',
                  'default' 		=> 'no'
          ),
          array(
                  'title' 			=> __( "Do not show Group Buy deals on product search page", 'wc_groupbuy' ),
                  'desc'        => __( 'Do not mix Group Buy deals and regular products on product search page (show Group Buy deals only when using Group Buy deals search)', 'wc_groupbuy' ),
                  'type' 				=> 'checkbox',
                  'id'					=> 'simple_groupbuy_dont_mix_search',
                  'default' 		=> 'no'
          ),
          array(
                  'title' 			=> __( "Do not show Group Buy deals on product category page", 'wc_groupbuy' ),
                  'desc'        => __( 'Do not mix Group Buy deals and regular products on product category page. Just show Group Buy deals on the groupbuy page (groupbuy base page)', 'wc_groupbuy' ),
                  'type' 				=> 'checkbox',
                  'id'					=> 'simple_groupbuy_dont_mix_cat',
                  'default' 		=> 'no'
          ),
          array(
                  'title' 			=> __( "Do not show Group Buy deals on product tag page", 'wc_groupbuy' ),
                  'desc'        => __( 'Do not mix Group Buy deals and regular products on product tag page. Just show groupbuy on the groupbuy page (groupbuy base page)', 'wc_groupbuy' ),
                  'type' 				=> 'checkbox',
                  'id'					=> 'simple_groupbuy_dont_mix_tag',
                  'default' 		=> 'no'
          ),
          array(
                  'title' 			=> __( "Countdown format", 'wc_groupbuy' ),
                  'desc'				=> __( "The format for the countdown display. Default is yowdHMS", 'wc_groupbuy' ),
                  'desc_tip' 		=> __( "Use the following characters (in order) to indicate which periods you want to display: 'Y' for years, 'O' for months, 'W' for weeks, 'D' for days, 'H' for hours, 'M' for minutes, 'S' for seconds. Use upper-case characters for mandatory periods, or the corresponding lower-case characters for optional periods, i.e. only display if non-zero. Once one optional period is shown, all the ones after that are also shown.", 'wc_groupbuy' ),
                  'type' 				=> 'text',
                  'id'					=> 'simple_groupbuy_countdown_format',
                  'default' 		=> 'yowdHMS'
          ),
          array(
                  'title'       => __( "Use compact countdown ", 'wc_simple_auctions' ),
                  'desc'        => __( 'Indicate whether or not the countdown should be displayed in a compact format.', 'wc_simple_auctions' ),
                  'type'        => 'checkbox',
                  'id'          => 'simple_groupbuy_compact_countdown',
                  'default'     => 'no'
              ),
          array(
                  'title'       => __( 'Woocommerce Group Buy deals Base Page', 'wc_groupbuy' ),
                  'desc' 				=> __( 'Set the base page for your Group Buy deals - this is where your Group Buy deals archive page will be.', 'wc_groupbuy' ),
                  'id' 					=> 'woocommerce_groupbuy_page_id',
                  'type' 				=> 'single_select_page',
                  'default'			=> '',
                  'class'				=> 'chosen_select_nostd',
                  'css' 				=> 'min-width:300px;',
                  'desc_tip'		=> true
                  ),
          array(
                  'title'       => __( 'Show progress bar', 'wc_groupbuy' ),
                  'desc' 				=> __( 'Show Group Buy deals progress bar on single product page (groupbuy details page)', 'wc_groupbuy' ),
                  'id' 					=> 'simple_groupbuy_progressbar',
                  'type' 				=> 'checkbox',
                  'default'			=> 'yes'
          ),
          array(
                  'title'       => __( 'Group Buy deals badge', 'wc_groupbuy' ),
                  'desc'      	=> __( 'Show Group Buy deals badge in loop', 'wc_groupbuy' ),
                  'id'        	=> 'simple_groupbuy_bage',
                  'type'      	=> 'checkbox',
                  'default'   	=> 'yes'
          ),
          array(
                  'title'       => __( 'Group Buy deals progress', 'wc_groupbuy' ),
                  'desc'      	=> __( 'Show Group Buy deals progress bar in loop', 'wc_groupbuy' ),
                  'id'        	=> 'simple_groupbuy_loopprogress',
                  'type'      	=> 'checkbox',
                  'default'   	=> 'yes'
          ),
          array(
                  'title'       => __( 'Group Buy deals time countdown', 'wc_groupbuy' ),
                  'desc'      	=> __( 'Show Group Buy deals time countdown bar in loop', 'wc_groupbuy' ),
                  'id'        	=> 'simple_groupbuy_loopcountdown',
                  'type'      	=> 'checkbox',
                  'default'   	=> 'yes'
          ),
          array(
                  'title'       => __( 'Close Group Buy deal when maximum ticket was sold', 'wc_groupbuy' ),
                  'desc'        => __( 'Option to instantly finish Group Buy deal when maximum number of tickets was sold', 'wc_groupbuy' ),
                  'type'        => 'checkbox',
                  'id'          => 'simple_groupbuy_close_when_max',
                  'default'     => 'no'
          ),
          array( 'type' => 'sectionend', 'id' => 'simple_groupbuy_options'),

		)); // End pages settings
	}
}

return new WC_Settings_groupbuy();

endif;