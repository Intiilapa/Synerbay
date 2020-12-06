<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce Group Buy user eamil for finished deal
 *
 * @class 		WC_Email_groupbuy_Win
 * @extends 	WC_Email
 */

class WC_Email_groupbuy_Win extends WC_Email {

    /** @var string */
    var $title;

    /** @var string */
    var $groupbuy_id;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    function __construct() {

        global $wc_groupbuy;

        $this->id             = 'groupbuy_win';
        $this->title          = __( 'Group Buy deal succeed.', 'wc_groupbuy' );
        $this->description    = __( 'Group Buy deal succeed emails are sent to user when group deal finished and it succeed to get minimum deals.', 'wc_groupbuy' );
        $this->customer_email = true;
        
        $this->template_html  = 'emails/groupbuy_win.php';
        $this->template_plain = 'emails/plain/groupbuy_win.php';
        $this->template_base  =  $wc_groupbuy->get_path(). 'templates/';
        
        $this->subject        = __( 'Group Buy deal succeed on {blogname}', 'wc_groupbuy');
        $this->heading        = __( 'Group Buy deal succeed!', 'wc_groupbuy');

        add_action( 'wc_groupbuy_won_notification', array( $this, 'trigger' ) );

        // Call parent constructor
        parent::__construct();
    }
    /**
     * trigger function.
     *
     * @access public
     * @return void
     */
    function trigger( $product_id ) {


        if ( !$this->is_enabled() ) return;

        if ( $product_id ) {

            $product_data = wc_get_product(  $product_id );

            $winning_users =  get_post_meta( $product_id, '_groupbuy_participant_id');

            if ( is_array($winning_users) ){
                $winning_users = array_unique ( $winning_users );
            }

            if ( $product_data && !empty($winning_users)) {

                foreach ($winning_users as $user) {

                    $this->object      = new WP_User( $user );
                    $this->recipient   = $this->object->user_email;
                    $this->groupbuy_id  = $product_id;
                    $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                }
            }
        }
    }
    /**
     * get_content_html function.
     *
     * @access public
     * @return string
     */
    function get_content_html() {
        ob_start();
        wc_get_template(
                $this->template_html, array(
                    'email_heading' => $this->get_heading(),
                    'blogname'      => $this->get_blogname(),
                    'product_id'    => $this->groupbuy_id
                ) );
        return ob_get_clean();
    }
    /**
     * get_content_plain function.
     *
     * @access public
     * @return string
     */
    function get_content_plain() {
        ob_start();
        wc_get_template(
                $this->template_plain, array(
                    'email_heading' => $this->get_heading(),
                    'blogname'      => $this->get_blogname(),
                    'product_id'    => $this->groupbuy_id
        ) );
        return ob_get_clean();
    }
}