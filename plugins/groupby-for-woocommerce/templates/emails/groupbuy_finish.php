<?php
/**
 * Admin email notification for group buy deal finish
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
$product_data = wc_get_product(  $product_id );

?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p><?php printf( __( "Group buy deal <a href='%s'>%s</a> has finished.", 'wc_groupbuy' ),get_permalink($product_id), $product_data->get_title() ); ?></p>


<?php do_action('woocommerce_email_footer');