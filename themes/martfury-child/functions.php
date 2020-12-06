<?php
add_action( 'wp_enqueue_scripts', 'martfury_child_enqueue_scripts', 20 );
function martfury_child_enqueue_scripts() {

    // load synerbay.js
    wp_register_script( "synerbay-script", get_stylesheet_directory_uri() . '/synerbay.js');

    // load synerbay.js
    wp_enqueue_script("synerbay-script", get_stylesheet_directory_uri() . '/synerbay.js', ['jquery'], false, true);

    // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
    wp_localize_script( 'synerbay-script', 'synerbayAjax', array(
        'restURL' => rest_url(),
//            'restNonce' => wp_create_nonce('Syn3rB4yZoZo')
        'restNonce' => wp_create_nonce('wp_rest')
    ));

    wp_enqueue_style( 'martfury-child-style', get_stylesheet_uri() );
    wp_enqueue_style('child-style-custom', get_stylesheet_directory_uri() . '/custom.css');
	if ( is_rtl() ) {
		wp_enqueue_style( 'martfury-rtl', get_template_directory_uri() . '/rtl.css', array(), '20180105' );
	}
}

/*
 *
 * Enable custom details for Dokan dashboard for vendors
 *
 * @package dokan
 *
 */

add_filter( 'dokan_settings_form_bottom', 'extra_fields', 10, 2);

 function extra_fields( $current_user, $profile_info ){
    $vendor_vat              = isset( $profile_info['vendor_vat'] ) ? $profile_info['vendor_vat'] : '';
    $vendor_industry         = isset( $profile_info['vendor_industry'] ) ? $profile_info['vendor_industry'] : '';
    $vendor_type             = isset( $profile_info['vendor_type'] ) ? $profile_info['vendor_type'] : '';
    $vendor_shipping_to      = isset( $profile_info['vendor_shipping_to'] ) ? $profile_info['vendor_shipping_to'] : '';
    $vendor_revenue          = isset( $profile_info['vendor_revenue'] ) ? $profile_info['vendor_revenue'] : '';
    $vendor_employees        = isset( $profile_info['vendor_employees'] ) ? $profile_info['vendor_employees'] : '';
    $vendor_product_range    = isset( $profile_info['vendor_product_range'] ) ? $profile_info['vendor_product_range'] : '';
    ?>
    <hr>
    <!-- VAT -->
    <div class="custom dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="setting_address">
            <?php _e( 'VAT', 'dokan' ); ?>
        </label>
        <div class="dokan-w5">
            <p style="text-align: left;"><?php echo $vendor_vat ?></p>
        </div>
    </div>

     <!-- Company Industry -->
     <div class="custom dokan-form-group">
         <label class="dokan-w3 dokan-control-label" for="setting_address">
             <?php _e( 'Industry', 'dokan' ); ?>
         </label>
         <div class="dokan-w5">
             <p style="text-align: left; text-transform: capitalize;"><?php echo $vendor_industry ?></p>
         </div>
     </div>

     <!-- Company Type -->
     <div class="custom dokan-form-group">
         <label class="dokan-w3 dokan-control-label" for="setting_address">
             <?php _e( 'Company Type', 'dokan' ); ?>
         </label>
         <div class="dokan-w5">
             <p style="text-align: left; text-transform: capitalize;"><?php echo $vendor_type ?></p>
         </div>
     </div>

     <!-- Shipping to -->
     <div class="custom dokan-form-group">
         <label class="dokan-w3 dokan-control-label" for="setting_address">
             <?php _e( 'Shipping to', 'dokan' ); ?>
         </label>
         <div class="dokan-w5">
             <p style="text-align: left;"><?php echo $vendor_shipping_to ?></p>
         </div>
     </div>

     <!-- Annual revenue -->
     <div class="custom dokan-form-group">
         <label class="dokan-w3 dokan-control-label" for="setting_address">
             <?php _e( 'Annual revenue', 'dokan' ); ?>
         </label>
         <div class="dokan-w5">
             <p style="text-align: left;"><?php echo $vendor_revenue ?></p>
         </div>
     </div>

     <!-- Employees -->
     <div class="custom dokan-form-group">
         <label class="dokan-w3 dokan-control-label" for="setting_address">
             <?php _e( 'Employees', 'dokan' ); ?>
         </label>
         <div class="dokan-w5">
             <p style="text-align: left;"><?php echo $vendor_employees ?></p>
         </div>
     </div>

     <!-- Product range -->
     <div class="custom dokan-form-group">
         <label class="dokan-w3 dokan-control-label" for="setting_address">
             <?php _e( 'Product range', 'dokan' ); ?>
         </label>
         <div class="dokan-w5">
             <p style="text-align: left;"><?php echo $vendor_product_range ?></p>
         </div>
     </div>
     <hr>
    <?php
}

//Back-end
add_action( 'dokan_store_profile_saved', 'save_extra_fields', 15 );

function save_extra_fields( $store_id ) {
    $dokan_settings = dokan_get_store_info($store_id);

    if ( isset( $_POST['vendor_vat'] ) ) {
        $dokan_settings['vendor_vat'] = $_POST['vendor_vat'];
    }

    if ( isset( $_POST['vendor_type'] ) ) {
        $dokan_settings['vendor_type'] = $_POST['vendor_type'];
    }

    update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );
}

// Frond-end
add_action( 'dokan_store_header_info_fields', 'save_seller_url', 10);

function save_seller_url($store_user){
    $store_info    = dokan_get_store_info( $store_user); ?>
<!--
    <?php if ( isset( $store_info['vendor_vat'] ) && !empty( $store_info['vendor_vat'] ) ) { ?>
        <i class="fa fa-globe"></i>
        <a href="<?php echo esc_html( $store_info['vendor_vat'] ); ?>"><?php echo esc_html( $store_info['vendor_vat'] ); ?></a>
    <?php } ?>

    <?php if ( isset( $store_info['vendor_type'] ) && !empty( $store_info['vendor_type'] ) ) { ?>
        <i class="fa fa-globe"></i>
        <a href="<?php echo esc_html( $store_info['vendor_type'] ); ?>"><?php echo esc_html( $store_info['vendor_type'] ); ?></a>
    <?php } ?>
-->
    <!-- END -->
    <?php }


/**
 * @snippet    Hide Price & Add to Cart for Logged Out Users

add_action('after_setup_theme','activate_filter') ;
function activate_filter(){
    add_filter('woocommerce_get_price_html', 'show_price_logged');
}
function show_price_logged($price){
    if(is_user_logged_in() ){
        return $price;
    }
    else
    {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
        return '<a class="hide_pricing" href="' . get_permalink(woocommerce_get_page_id('myaccount')) . '">Login to See Prices</a>';
    }
}
 */

//add_filter('woocommerce_add_to_cart_redirect', '_add_to_cart_redirect');
//function _add_to_cart_redirect() {
//    global $woocommerce;
//        $checkout_url = wc_get_checkout_url();
//    return $checkout_url;
//}

/*
 *
 * Remove all product types and make groupbuy set as default
 *
 */

//add_filter( 'product_type_selector', 'remove_grouped_and_external' );
//function remove_grouped_and_external( $product_types ){
//
//    //unset( $product_types['grouped'] );
//    //unset( $product_types['external'] );
//    //unset( $product_types['variable'] );
//    //unset( $product_types['simple'] );
//
//
//    return $product_types;
//}
//

///**
// * Auto Complete all WooCommerce orders.
// */
//add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
//function custom_woocommerce_auto_complete_order( $order_id ) {
//    if ( ! $order_id ) {
//        return;
//    }
//
//    $order = wc_get_order( $order_id );
//    $order->update_status( 'completed' );
//}
//
//

add_action( 'phpmailer_init', 'setup_phpmailer_init' );
function setup_phpmailer_init( $phpmailer )
{
    $phpmailer->Host = 'smtp-relay.sendinblue.com'; // for example, smtp.mailtrap.io
    $phpmailer->Port = 587; // set the appropriate port: 465, 2525, etc.
    $phpmailer->Username = 'remcoad@gmail.com'; // your SMTP username
    $phpmailer->Password = 'p2C64WxZO9LB0NRg'; // your SMTP password
    $phpmailer->SMTPAuth = true;
    $phpmailer->SMTPSecure = 'tls'; // preferable but optional
    $phpmailer->IsSMTP();
}
