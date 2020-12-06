<?php
/**
 * groupbuy badge template
 *
 * @author 	WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

?>

<?php if ( method_exists( $product, 'get_type') && $product->get_type() == 'groupbuy' ) : ?>
	
	<div class="wcl-progress-meter progresbar-<?php echo $product->get_id(); ?> <?php if($product->is_groupbuy_max_deals_met()) {echo 'full';} if(!$product->is_groupbuy_min_deals_met()) {echo 'no-min';} ?>">
    <span class="zero">0</span>
    <span class="max"><?php echo $product->get_groupbuy_max_deals() ?></span>
    <progress  max="<?php echo $product->get_groupbuy_max_deals() ?>" value="<?php echo $product->get_groupbuy_participants_count() ? $product->get_groupbuy_participants_count() : '0' ;?>"  low="<?php echo $product->get_groupbuy_min_deals() ?>">60%</progress>
	</div>
<?php endif;