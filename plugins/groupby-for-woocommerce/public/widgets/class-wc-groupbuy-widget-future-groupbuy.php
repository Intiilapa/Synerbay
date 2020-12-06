<?php
/**
 * WooCommerce Group Buy Future Group Buy Widget
 *
 * @category 	Widgets
 * @version 	1.0.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_groupbuy_Widget_Future_Groupbuy extends WP_Widget {

	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
   *
	 */
	function __construct() {

		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'woocommerce widget__future_groupbuy';
		$this->woo_widget_description = __( 'Display a list of your future Group Buy Deals on your site.', 'wc_groupbuy' );
		$this->woo_widget_idbase = 'woocommerce_future_groupbuy';
		$this->woo_widget_name = __( 'WooCommerce Future Group Buy Deals', 'wc_groupbuy' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );

		parent::__construct('future_groupbuy', $this->woo_widget_name, $widget_ops);

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Widget function
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
     *
	 */
	function widget($args, $instance) {

		global $woocommerce;

		$cache = wp_cache_get('widget_future_groupbuy', 'widget');


		if ( !is_array($cache) ) $cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}



		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Future Group Buy Deals', 'wc_groupbuy' ) : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

		$query_args                         = array('posts_per_page' => $number, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product');
		$query_args['meta_query']           = array();
		$query_args['meta_query'][]         = $woocommerce->query->stock_status_meta_query();
		$query_args['meta_query'][]         = array(
					                                'key'     => '_groupbuy_closed',
					                                'compare' => 'NOT EXISTS',
						                        );
		$query_args['meta_query'][]         = array('key' => '_groupbuy_started','value' => '0' );
		$query_args['meta_query']           = array_filter( $query_args['meta_query'] );
		$query_args['tax_query']            = array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'groupbuy'));
		$query_args['is_groupbuy_archive']  = TRUE;
		$query_args['meta_key']             = '_groupbuy_dates_to';
		$query_args['orderby']              = 'meta_value';
		$query_args['order']                = 'ASC';
		$query_args['show_future_groupbuy'] = true;
		$query_args['suppress_filters']               = true;
		$current_language= apply_filters( 'wpml_current_language', NULL );
		if ( $current_language ) {
			$query_args['posts_per_page'] =  -1;
		}

		$r = new WP_Query($query_args);
		if ( $r->have_posts() ) {
			if ( $current_language ) {
				$default_languag = apply_filters( 'wpml_default_language', NULL );
				if($default_languag !== $current_language ){
					$post_ids = wp_list_pluck( $r->posts, 'ID' );
					$query_args                        = array('posts_per_page' => $number, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product', 'post__in' => $post_ids, 'orderby' => 'post__in');
					$query_args['is_groupbuy_archive'] = TRUE;
					$query_args['suppress_filters'] = false;
					$query_args [ 'show_future_groupbuy' ] = true;
					$query_args [ 'show_past_groupbuy' ] = true;
					$r = new WP_Query($query_args);
				}
			}
		}


		if ( $r->have_posts() ) {

		    $hide_time = empty( $instance['hide_time'] ) ? 0 : 1;
			echo $before_widget;

			if ( $title )
				echo $before_title . $title . $after_title;

			echo '<ul class="product_list_widget">';

			while ( $r->have_posts()) {
				$r->the_post();

				global $product;
				$time = '';
				$timetext = __('Time left', 'wc_groupbuy');
				$datatime = $product->get_seconds_remaining();
				if(!$product->is_started()){
					$timetext = __('Starting in', 'wc_groupbuy');
					$datatime = $product->get_seconds_to_groupbuy();
				}
				if($hide_time != 1 && !$product->is_closed())
					$time = '<span class="time-left">'.apply_filters('time_text',$timetext,$product->get_id()).'</span>
					<div class="groupbuy-time-countdown" data-time="'.$datatime.'" data-groupbuyid="'.$product->get_id().'" data-format="'.get_option( 'simple_groupbuy_countdown_format' ).'"></div>';
				if($product->is_closed())
						$time = '<span class="has-finished">'.apply_filters('time_text',__('groupbuy finished', 'wc_groupbuy'),$product->get_id()).'</span>';
				echo '<li>
					<a href="' . get_permalink() . '">
						' . ( has_post_thumbnail() ? get_the_post_thumbnail( $r->post->ID, 'shop_thumbnail' ) : woocommerce_placeholder_img( 'shop_thumbnail' ) ) . ' ' . get_the_title() . '
					</a> ' . $product->get_price_html() . $time . '

				</li>';
			}

			echo '</ul>';

			echo $after_widget;
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		if ( isset( $args['widget_id'] ) ) $cache[$args['widget_id']] = $content;

		echo $content;

		wp_cache_set('widget_future_groupbuy', $cache, 'widget');
	}

	/**
	 * Update function
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
     *
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['hide_time'] = empty( $new_instance['hide_time'] ) ? 0 : 1;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_future_groupbuy']) ) delete_option('widget_future_groupbuy');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_future_groupbuy', 'widget');
	}

	/**
	 * Form function
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
     *
	 */
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
		$hide_time = empty( $instance['hide_time'] ) ? 0 : 1;
        ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'wc_groupbuy' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of groupbuy to show:', 'wc_groupbuy' ); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_time') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_time') ); ?>"<?php checked( $hide_time ); ?> />
		<label for="<?php echo $this->get_field_id('hide_time'); ?>"><?php _e( 'Hide time left', 'wc_groupbuy' ); ?></label></p>
        <?php
	}
}