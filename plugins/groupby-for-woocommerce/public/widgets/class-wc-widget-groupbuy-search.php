<?php
/**
 * WooCommerce Group Buy Search Widgett
 *
 * @category 	Widgets
 * @version 	1.0.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Widget_Groupbuy_Search extends WC_Widget {

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
   *
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_groupbuy_search';
		$this->widget_description = __( 'A Search box for Group Buy Deals only.', 'wc_groupbuy' );
		$this->widget_id          = 'woocommerce_groupbuy_search';
		$this->widget_name        = __( 'Search Group Buy Deals', 'wc_groupbuy' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'wc_groupbuy' )
			)
		);

		parent::__construct();
	}

	/**
	 * Widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		$this->widget_start( $args, $instance );

		ob_start();

		do_action( 'pre_get_groupbuy_search_form'  );

		wc_get_template( 'groupbuy-searchform.php' );

		$form = apply_filters( 'get_groupbuy_search_form', ob_get_clean() );

		echo $form;

		$this->widget_end( $args );
	}

}