<?php
/**
 * WooCommerce Group Buy Random Group Buy Widget
 *
 * @author 		WooThemes
 * @version 	1.0.0
 * @extends 	WP_Widget
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_groupbuy_Widget_Random_Groupbuy extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
   *
	 */
	function __construct() {
		$this->id_base = 'woocommerce_random_groupbuy';
		$this->name    = __( 'WooCommerce Random Group Buy Deals', 'wc_groupbuy' );
		$this->widget_options = array(
			'classname'   => 'woocommerce widget_random_groupbuy',
			'description' => __( 'Display a list of random Group Buy Deals on your site.', 'wc_groupbuy' ),
		);

		parent::__construct( $this->id_base, $this->name, $this->widget_options );
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
	function widget( $args, $instance ) {
		global $woocommerce;

		// Use default title as fallback
		$title = ( '' === $instance['title'] ) ? __('Random groupbuy', 'wc_groupbuy' ) : $instance['title'];
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		// Setup product query
		$query_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $instance['number'],
			'orderby'        => 'rand',
			'no_found_rows'  => 1
		);

		$query_args['meta_query'] = array();
	    $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
	    $query_args['meta_query']   = array_filter( $query_args['meta_query'] );
		$query_args['tax_query'] = array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'groupbuy'));
		$query_args['is_groupbuy_archive'] = TRUE;
		$query_args['suppress_filters']               = true;
		$current_language= apply_filters( 'wpml_current_language', NULL );
		if ( $current_language ) {
			$query_args['posts_per_page'] =  -1;
		}

		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {
			if ( $current_language ) {
				$default_languag = apply_filters( 'wpml_default_language', NULL );
				if($default_languag !== $current_language ){
					$post_ids = wp_list_pluck( $r->posts, 'ID' );
					$query_args                        = array('posts_per_page' => $number, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product', 'post__in' => $post_ids, 'orderby' => 'post__in');
					$query_args['is_groupbuy_archive'] = TRUE;
					$query_args['suppress_filters'] = false;
					$query_args['show_future_groupbuy'] = true;
					
					$r = new WP_Query($query_args);
				}
			}
		}

		if ( $query->have_posts() ) {
			$hide_time = empty( $instance['hide_time'] ) ? 0 : 1;
			echo $args['before_widget'];

			if ( '' !== $title ) {
				echo $args['before_title'], $title, $args['after_title'];
			} ?>

			<ul class="product_list_widget">
				<?php while ($query->have_posts()) : $query->the_post(); global $product;
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
				 ?>
					<li>
						<a href="<?php the_permalink() ?>">
							<?php
								if ( has_post_thumbnail() )
									the_post_thumbnail( 'shop_thumbnail' );
								else
									echo woocommerce_placeholder_img( 'shop_thumbnail' );
							?>
							<?php the_title() ?>
						</a>
						<?php echo $product->get_price_html() ?>
						<?php echo $time ?>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
			echo $args['after_widget'];
		}
		wp_reset_postdata();
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
		$instance = array(
			'title'           => strip_tags($new_instance['title']),
			'number'          => absint( $new_instance['number'] ),
			'hide_time'       => empty( $new_instance['hide_time'] ) ? 0 : 1,
		);
		return $instance;
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
		$title           = isset( $instance['title'] ) ? $instance['title'] : '';
		$number          = isset( $instance['number'] ) ? (int) $instance['number'] : 5;
		$hide_time = empty( $instance['hide_time'] ) ? 0 : 1;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php _e( 'Title:', 'wc_groupbuy' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('title') ) ?>" type="text" value="<?php echo esc_attr( $title ) ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ) ?>"><?php _e( 'Number of groupbuy to show:', 'wc_groupbuy' ) ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('number') ) ?>" type="text" value="<?php echo esc_attr( $number ) ?>" size="3" />
		</p>
		<p><input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hide_time') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_time') ); ?>"<?php checked( $hide_time ); ?> />
		<label for="<?php echo $this->get_field_id('hide_time'); ?>"><?php _e( 'Hide time left', 'wc_groupbuy' ); ?></label></p>
		<?php
	}
}