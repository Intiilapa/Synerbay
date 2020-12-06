<?php
/**
 * Group Buy deal badge template
 *
 * @author WooThemes
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

?>
<?php if ( method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy' ) : ?>
	<?php if (  ( $product->is_closed() === FALSE ) &&  ($product->is_started() === TRUE )) : ?>
		<div class="groupbuy-time" id="countdown-<?php echo $product->get_id() ?>"><?php echo apply_filters('time_text', __( 'Time left:', 'wc_groupbuy' ), $product); ?>
	            <div class="main-groupbuy groupbuy-time-countdown" data-time="<?php echo $product->get_seconds_remaining() ?>" data-groupbuyid="<?php echo $product->get_id() ?>" data-format="<?php echo get_option( 'simple_groupbuy_countdown_format' ) ?>"></div>
	    </div>
	<?php elseif (( $product->is_closed() === FALSE ) && ($product->is_started() === FALSE )):?>

		<div class="groupbuy-time future" id="countdown-<?php echo $product->get_id() ?>"><?php echo  __( 'Group Buy deal starts in:', 'wc_groupbuy' ) ?>
			<div class="groupbuy-time-countdown future" data-time="<?php echo $product->get_seconds_to_groupbuy() ?>" data-format="<?php echo get_option( 'simple_groupbuy_countdown_format' ) ?>"></div>
		</div>

	<?php endif;?>   
<?php endif;    
