<?php
/**
 * Group Buy deal winners block template
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
$current_user = wp_get_current_user();
?>
<?php if ( isset($product) && $product->get_groupbuy_order_hold_on() ){ ?>
	<p><?php _e('Please be patient. We are waiting for some orders to be payed!','wc_groupbuy') ?></p>
<?php } else { ?>
	<?php if ($product->is_user_participating()) : ?>
			<?php if($product->get_groupbuy_closed() == '2') :?>
					<p><?php _e('Congratulations! Group Buy deal was success.','wc_groupbuy') ?></p>
			<?php else:?>
						<p><?php _e('Sorry, better luck next time. Deal failed.','wc_groupbuy') ?></p>
			<?php endif;?>
	<?php endif;
}
