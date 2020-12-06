<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce Group Buy Product Class
 *
 * @class WC_Product_Auction
 *
 */
class WC_Product_groupbuy extends WC_Product {

    public $post_type = 'product';
    public $product_type = 'groupbuy';

    /**
     * Stores product data.
     * 
     * @var array
     */
    protected $extra_data = array(
       
    );

    /**
     * __construct function.
     *
     * @access public
     * @param mixed $product
     *
     */
    public function __construct( $product ) {
        global $sitepress;
        date_default_timezone_set("UTC");

        if(is_array($this->data))
            $this->data = array_merge( $this->data, $this->extra_data );

        parent::__construct( $product );
        $this->is_closed();
        $this->is_started();
    }

    /**
     * Returns the unique ID for this object.
     * @return int
     */
    public function get_id() {
        return $this->id; 
    }

    /**
     * Get internal type.
     *
     * @return string
     */
    public function get_type() {
        return 'groupbuy';
    }

    /**
     * Get remaining seconds till groupbuy end
     *
     * @access public
     * @return mixed
     *
     */
    function get_seconds_remaining() {
       
        if ($this->get_groupbuy_dates_to()){

            return strtotime($this->get_groupbuy_dates_to())  -  (get_option( 'gmt_offset' )*3600);

        } else {

            return FALSE;
        }
    }
    /**
     * Get seconds till groupbuy starts
     *
     * @access public
     * @return mixed
     *
     */
    function get_seconds_to_groupbuy() {
        
        if ($this->get_groupbuy_dates_from()){

            return strtotime($this->get_groupbuy_dates_from()) - (get_option( 'gmt_offset' )*3600);

        } else {

            return FALSE;
        }
        
    }
    /**
     * Has groupbuy started
     *
     * @access public
     * @return mixed
     *
     */
    function is_started() {

        $id = $this->get_main_wpml_product_id();

        if($this->get_groupbuy_has_started() === '1' ){
            return TRUE;
        }

        if (!empty($this->get_groupbuy_dates_from()) ){
            $date1 = new DateTime($this->get_groupbuy_dates_from());
            $date2 = new DateTime(current_time('mysql'));
            if ($date1 < $date2){
                    
                    update_post_meta( $id, '_groupbuy_has_started', '1');
                    delete_post_meta( $id, '_groupbuy_started');
                    do_action('woocommerce_lottery_started',$id);

            } else{
                    update_post_meta( $id, '_groupbuy_started', '0');
            }

            return ($date1 < $date2) ;
        } else {
            update_post_meta( $id, '_groupbuy_started', '0');
            return FALSE;
        }
    }
    /**
     * Does user participate in groupbuy
     *
     * @access public
     * @return mixed
     *
     */
    function user_participating($user_id) {

       global $wpdb;
       $result = $wpdb->get_row("SELECT 1 FROM ".$wpdb -> prefix."simple_groupbuy_log WHERE userid = $user_id");

       if ($result != null){
           return TRUE;
       } else {
           return FALSE;
       }
       return FALSE;
    }
    /**
     * Has groupbuy met min participants limit
     *
     * @access public
     * @return mixed
     *
     */
    function is_groupbuy_min_deals_met() {


        if (!empty($this->get_groupbuy_min_deals()) && $this->get_groupbuy_participants_count() && !empty($this->get_groupbuy_participants_count())){

            return ( intval($this->get_groupbuy_participants_count()) >= intval($this->get_groupbuy_min_deals()) );
        }
        return true;
    }
    /**
     * Has groupbuy met max participants limit
     *
     * @access public
     * @return mixed
     *
     */
    function is_groupbuy_max_deals_met() {

        if (!empty($this->get_groupbuy_max_deals()) ){

            return ( $this->get_groupbuy_participants_count() >= $this->get_groupbuy_max_deals());

        }
        if(empty($this->get_groupbuy_max_deals()))
            return false;

        return true;
    }
    /**
     * Has groupbuy finished
     *
     * @access public
     * @return mixed
     *
     */
    function is_finished() {

        if (!empty($this->get_groupbuy_dates_to())){

            $date1 = new DateTime($this->get_groupbuy_dates_to());
            $date2 = new DateTime(current_time('mysql'));
            return ($date1 < $date2) ;

        } else {
            return FALSE;
        }
    }
    /**
     * Is groupbuy closed
     *
     * @access public
     * @return bool
     *
     */
    function is_closed() {

        $id = $this->get_main_wpml_product_id();

        if (in_array($this->get_groupbuy_closed(), array('1','2')) ){

            return TRUE;

        } else {

            if (($this->is_finished() && $this->is_started()) or (get_option( 'simple_groupbuy_close_when_max' ) == 'yes' && $this->is_groupbuy_max_deals_met() )  ){

                global $product, $post;

                if(get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_order_hold_on', true )){
                    return TRUE;
                }

                if ( empty($this->get_groupbuy_participants_count())){


                    update_post_meta( $id, '_groupbuy_closed', '1');
                    update_post_meta( $id, '_groupbuy_fail_reason', '1');
                    $order_id = FALSE;
                    do_action('wc_groupbuy_close',  $id);
                    do_action('wc_groupbuy_fail', array('groupbuy_id' => $id , 'reason' => __('There were no participants','wc_groupbuy') ));
                    return FALSE;
                }

                if ( $this->is_groupbuy_min_deals_met() == FALSE){

                    update_post_meta( $id, '_groupbuy_closed', '1');
                    update_post_meta( $id, '_groupbuy_fail_reason', '2');
                    $order_id = FALSE;
                    do_action('wc_groupbuy_close',  $id);
                    do_action('wc_groupbuy_min_fail',  $id);
                    do_action('wc_groupbuy_fail', array('groupbuy_id' => $id , 'reason' => __('The item did not make it to minimum participants','wc_groupbuy') ));
                    return FALSE;

                }

                update_post_meta( $id, '_groupbuy_closed', '2');

                do_action('wc_groupbuy_close', $id);
                do_action('wc_groupbuy_won', $id);

                return TRUE;

            } else {

                return FALSE;

            }
        }
    }
    /**
     * Get groupbuy history
     *
     * @access public
     * @return object
     *
     */
    function groupbuy_history( $datefrom = FALSE, $user_id = FALSE ) {

        global $wpdb;
        $wheredatefrom ='';

        $id = $this->get_main_wpml_product_id();

        $relisteddate = get_post_meta( $id, '_groupbuy_relisted', true );
        if(!is_admin() && !empty($relisteddate)){
            $datefrom = $relisteddate;
        }

        if($datefrom){
            $wheredatefrom =" AND CAST(date AS DATETIME) > '$datefrom' ";
        }

        if($user_id){
            $wheredatefrom =" AND userid = $user_id";
        }
        
        $history = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'wc_groupbuy_log WHERE groupbuy_id =' .  $id . $wheredatefrom.' ORDER BY  `date` DESC');

        return $history;
    }
    /**
     * Wrapper for get_permalink
     *
     * @return string
     *
     */
    public function get_permalink() {

          $id = $this->get_main_wpml_product_id();
          return get_permalink( $id );
    }
    /**
     * Is user participating in groupbuy
     *
     * @access public
     * @return bool
     *
     */
    function is_user_participating($userid = FALSE ){

        $id = $this->get_main_wpml_product_id();

        if(!$userid) {
            $userid = get_current_user_id();
        }

        $participants = get_post_meta( $id, '_groupbuy_participant_id', false);

        if($participants and is_array($participants)){

            return in_array($userid, $participants);

        } else {

            return FALSE;
        }

        return false;
    }
    /**
     * Is user participating in groupbuy
     *
     * @access public
     * @return int
     *
     */
    function count_user_deals($userid = FALSE ){

        $id = $this->get_main_wpml_product_id();

        if(!$userid) {
            $userid = get_current_user_id();
        }

        $users_qty = array_count_values( get_post_meta($id, '_groupbuy_participant_id') );

        $current_user_qty = isset($users_qty[$userid]) ? intval($users_qty[$userid]) : 0;

        return $current_user_qty;
    }
    /**
     * Get main product id for multilanguage purpose
     *
     * @access public
     * @return int
     *
     */
    function get_main_wpml_product_id(){

        global $sitepress;

        if (function_exists('icl_object_id') && function_exists('pll_default_language')) { // Polylang with use of WPML compatibility mode
            $id = icl_object_id($this->id,'product',false, pll_default_language());
            if($id === null){
                $id = $this->id;
            }
        }
        elseif (function_exists('icl_object_id') && method_exists($sitepress, 'get_default_language')) { // WPML
            $id = icl_object_id($this->id,'product',false, $sitepress->get_default_language());
            if($id === null){
                $id = $this->id;
            }
        }
        else {
            $id = $this->id;
        }

        return $id;

    }

    public function is_purchasable() {

        $purchasable = true;

        // Products must exist of course
        if ( ! $this->exists() ) {
            $purchasable = false;

        // Other products types need a price to be set
        } elseif ( $this->get_price() === '' ) {
            $purchasable = false;

        // Check the product is published
        } elseif ( get_post_status($this->get_id()) !== 'publish' && ! current_user_can( 'edit_post', $this->id ) ) {
            $purchasable = false;
        }
         elseif ( $this->is_closed() == true ) {
            $purchasable = false;
        }
         elseif ( $this->is_started() == false ) {
            $purchasable = false;
        }

        return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
    }


    /**
     * Get the add to cart button text.
     *
     * @return string
     */
    public function add_to_cart_text() {

            if ( $this->is_closed() ) {

                $text =  __( 'Read more', 'wc_groupbuy' );

            } elseif( !$this->is_started() OR $this->is_groupbuy_max_deals_met() ) {

                $text =  __( 'Read more', 'wc_groupbuy' );

            } else {
                $text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Participate', 'wc_groupbuy' ) : __( 'Read more', 'wc_groupbuy' );
            }


        return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
    }

      /**
     * Get max quantity which can be purchased at once.
     *
     * @since  3.0.0
     * @return int Quantity or -1 if unlimited.
     */
    public function get_max_purchase_quantity() {
        $max_tickets_per_user = $this->get_max_tickets_per_user() ? $this->get_max_tickets_per_user() : false;
        if($max_tickets_per_user !== false){
            $max_tickets_per_user = $max_tickets_per_user - $this->count_user_tickets_in_cart();
               if( is_user_logged_in() ){
                    $user_tickets = $this->count_user_tickets();
                    return intval($max_tickets_per_user) - intval($user_tickets);
               }
               return $max_tickets_per_user;
        }
        return $this->get_stock_quantity();
    }

    /**
     *
     * @access public
     * @return int
     * 
     */ 

    function count_user_tickets($userid = FALSE ){
        
        $id = $this->get_main_wpml_product_id();

        if(!$userid) {
            $userid = get_current_user_id();
        }

        $users_qty = array_count_values( get_post_meta($id, '_groupbuy_participant_id') );

        $current_user_qty = isset($users_qty[$userid]) ? intval($users_qty[$userid]) : 0;


        return $current_user_qty;
    }

    /**
     *
     * @access public
     * @return int
     * 
     */ 

    function count_user_tickets_in_cart($userid = FALSE ){
        
        $id = $this->get_main_wpml_product_id();

        if(!$userid) {
            $userid = get_current_user_id();
        }
        $count = 0; // Initializing

        foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {

            if ( ! isset( $product_quantities[ $values['product_id'] ] ) ) {
                $product_quantities[ $values['product_id'] ] = 0;
            }
            if( $values['product_id'] == $id ){
                $count += $values['quantity'];
            }

        }

         return intval( $count );


    }




    /**
     * Get groupbuy started
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_started( $context = 'view' ) {
         
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_started', true );
        
    }

    /**
     * Get groupbuy started
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_has_started( $context = 'view' ) {
         
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_has_started', true );
        
    }
    /**
     * Get groupbuy_min_deals
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_min_deals( $context = 'view' ) {
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_min_deals', true );
        
    }
    /**
     * Get groupbuy_dates_to
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_dates_to( $context = 'view' ) {
         

        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_dates_to', true );
        
    }
    /**
     * Get groupbuy_dates_from
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_dates_from( $context = 'view' ) {

        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_dates_from', true );
        
    }
    /**
     * Get groupbuy_participants_count
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_participants_count( $context = 'view' ) {
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_participants_count', true );
        
    }
     /**
     * Get get_groupbuy_max_deals
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_max_deals( $context = 'view' ) {
         
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_max_deals', true );
        
    }
    /**
     * Get groupbuy_closed
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_closed( $context = 'view' ) {
         
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_closed', true );
        
    }
    /**
     * Get groupbuy_fail_reason
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_fail_reason( $context = 'view' ) {
        
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_fail_reason', true );
        
    }
    /**
     * Get groupbuy_participant_id
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_participant_id( $context = 'view' ) {

        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_participant_id', true );
        
    }
     /**
     * Get max_tickets_per_user
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_max_tickets_per_user( $context = 'view' ) {
         
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_max_deals_per_user', true );
    }
    /**
     * Get get_groupbuy_max_deals_per_user
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_max_deals_per_user( $context = 'view' ) {
       
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_max_deals_per_user', true );
        
    }

    /**
     * Get get_groupbuy_order_hold_on
     *
     * @since 1.1
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_order_hold_on( $context = 'view' ) {
       
        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_order_hold_on', true );
        
    }

    public function groupbuy_update_lookup_table( ) {
        global $wpdb;

        $id    = absint( $this->get_main_wpml_product_id() );
        $table = 'wc_product_meta_lookup';
        $existing_data = wp_cache_get( 'lookup_table', 'object_' . $id );
        $update_data   = $this->groupbuy_get_data_for_lookup_table( $id );

        if ( ! empty( $update_data ) && $update_data !== $existing_data ) {
            $wpdb->replace(
                $wpdb->$table,
                $update_data
            );
            wp_cache_set( 'lookup_table', $update_data, 'object_' . $id );
        }
    }

    public function groupbuy_get_data_for_lookup_table( $id ){

        $price_meta   = (array) get_post_meta( $id, '_price', false );
        $manage_stock = get_post_meta( $id, '_manage_stock', true );
        $stock        = 'yes' === $manage_stock ? wc_stock_amount( get_post_meta( $id, '_stock', true ) ) : null;
        $price        = wc_format_decimal( get_post_meta( $id, '_price', true ) );
        $sale_price   = wc_format_decimal( get_post_meta( $id, '_sale_price', true ) );
        return array(
            'product_id'     => absint( $id ),
            'sku'            => get_post_meta( $id, '_sku', true ),
            'virtual'        => 'yes' === get_post_meta( $id, '_virtual', true ) ? 1 : 0,
            'downloadable'   => 'yes' === get_post_meta( $id, '_downloadable', true ) ? 1 : 0,
            'min_price'      => reset( $price_meta ),
            'max_price'      => end( $price_meta ),
            'onsale'         => $sale_price && $price === $sale_price ? 1 : 0,
            'stock_quantity' => $stock,
            'stock_status'   => get_post_meta( $id, '_stock_status', true ),
            'rating_count'   => array_sum( (array) get_post_meta( $id, '_wc_rating_count', true ) ),
            'average_rating' => get_post_meta( $id, '_wc_average_rating', true ),
            'total_sales'    => get_post_meta( $id, 'total_sales', true ),
        );
    }

    /**
     * Get get_groupbuy_relisted
     *
     * @since 1.2.8
     * @param  string $context
     * @return string
     */
    public function get_groupbuy_relisted( $context = 'view' ) {

        return get_post_meta( $this->get_main_wpml_product_id(), '_groupbuy_relisted', true );
        
    }

}