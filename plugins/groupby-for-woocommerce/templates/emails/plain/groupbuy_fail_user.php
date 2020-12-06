<?php
/**
 * Email notification for user when deal was not success - better luck next time (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$product_data = wc_get_product(  $product_id );

echo $email_heading . "\n\n";

printf(__("We are sorry. There was no minimum user(s) for %s deal.  Better luck next time.", 'wc_groupbuy'),  $product_data -> get_title(), wc_price($current_bid));  
echo "\n\n";
echo get_permalink($product_id);
echo "\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );