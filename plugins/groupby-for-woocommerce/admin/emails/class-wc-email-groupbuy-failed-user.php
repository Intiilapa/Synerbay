<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce Group Buy user email for failed deal
 *
 * @class 		WC_Email_groupbuy_failed_user
 * @extends 	WC_Email
 */

class WC_Email_groupbuy_failed_user extends WC_Email {

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

        $this->id             = 'groupbuy_fail_user';
        $this->title          = __( 'Group Buy deal Fail', 'wc_groupbuy' );
        $this->description    = __( 'Group Buy deal Fail emails are sent to user when Group Buy deal fails.', 'wc_groupbuy' );
        $this->customer_email = true;
        
        $this->template_html  = 'emails/groupbuy_fail_user.php';
        $this->template_plain = 'emails/plain/groupbuy_fail_user.php';
        $this->template_base  = $wc_groupbuy->get_path() .  'templates/';
        
        $this->subject        = __( 'Group Buy deal failed on {blogname}', 'wc_groupbuy');
        $this->heading        = __( 'Better luck next time!', 'wc_groupbuy');

        add_action( 'wc_groupbuy_min_fail_notification', array( $this, 'trigger' ) );

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

            $participants =  get_post_meta( $product_id, '_groupbuy_participant_id');
            if ( is_array($participants) ){
                $uniqueparticipants = array_unique ( $participants );
            }

            if ( $product_data && !empty($uniqueparticipants)) {

                foreach ($uniqueparticipants as $user) {
                    $this->object      = new WP_User( $user );
                    $this->recipient   = $this->object->user_email;
                    $this->groupbuy_id     = $product_id;
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