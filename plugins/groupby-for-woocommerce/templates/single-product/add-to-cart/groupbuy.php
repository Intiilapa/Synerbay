<?php
/**
 * Group Buy deal product add to cart template
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product, $post;

$id = $product->get_main_wpml_product_id();

if ( ! $product->is_purchasable() OR ! $product->is_in_stock() OR $product->is_closed() ) return;

?>
<?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <form class="buy-now cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>">
        <?php

        do_action('woocommerce_before_add_to_cart_button');

        if ( ! $product->is_sold_individually() )
            woocommerce_quantity_input( array(
                'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
                'max_value' => apply_filters( 'woocommerce_quantity_input_max',  $product->get_max_purchase_quantity(), $product ),
                'step' => 5,
            ) );
        ?>

        <div>
            <input type="hidden" name="add-to-cart" value="<?php echo $product->get_id(); ?>" />
            <input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
        </div>

        <?php do_action('synerbay_offerApplyButton', $product);?>
        <?php do_action('woocommerce_after_add_to_cart_button'); ?>

    </form>


<?php
// todo Remco ezt majd a headerbe vagy footerbe kellene hívni
    do_action('synerbay_loader');
?>
<?php
// todo Remco ezt majd a headerbe vagy footerbe kellene hívni
    do_action('synerbay_loginModal');
?>
