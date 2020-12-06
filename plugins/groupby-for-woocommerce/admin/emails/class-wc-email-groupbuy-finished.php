<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooCommerce Group Buy admin email for finished deal
 *
 * @class 		WC_Email_groupbuy_Finished
 * @extends 	WC_Email
 */

class WC_Email_groupbuy_Finished extends WC_Email {

    /** @var string */
    var $title;

    /** @var string */
    var $groupbuy_id;


    //** @var object */
    var $winning_users;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    function __construct() {

        global $wc_groupbuy;

        $this->id             = 'groupbuy_finished';
        $this->title          = __( 'Group Buy deal Finish', 'wc_groupbuy' );
        $this->description    = __( 'Group Buy deal finish emails are sent to admin when groupbuy has finished.', 'wc_groupbuy' );
        
        $this->template_html  = 'emails/groupbuy_finish.php';
        $this->template_plain = 'emails/plain/groupbuy_finish.php';
        $this->template_base  = $wc_groupbuy->get_path() .  'templates/';
        
        $this->subject        = __( 'Group Buy deal finished on {blogname}', 'wc_groupbuy');
        $this->heading        = __( 'Group Buy deal finished!', 'wc_groupbuy');

        add_action( 'wc_groupbuy_close_notification', array( $this, 'trigger' ) );

        // Call parent constructor
        parent::__construct();

        // Other settings
        $this->recipient = $this->get_option( 'recipient' );

        if ( ! $this->recipient )
                $this->recipient = get_option( 'admin_email' );
    }

    /**
     * trigger function.
     *
     * @access public
     * @return void
     */
    function trigger( $groupbuy_id ) {

        if ( $groupbuy_id ) {
            $this->groupbuy_id = $groupbuy_id;
            $this->object = wc_get_product(  $groupbuy_id );
        }

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) return;

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
    /**
     * Initialise Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	$this->form_fields = array(
            'enabled' => array(
                    'title' 		  => __( 'Enable/Disable', 'woocommerce' ),
                    'type' 		    => 'checkbox',
                    'label' 		  => __( 'Enable this email notification', 'woocommerce' ),
                    'default' 		=> 'yes'
            ),
            'recipient' => array(
                    'title' 		     => __( 'Recipient(s)', 'woocommerce' ),
                    'type' 		       => 'text',
                    'description' 	 => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce' ), esc_attr( get_option('admin_email') ) ),
                    'placeholder' 	 => '',
                    'default' 		   => ''
            ),
            'subject' => array(
                    'title' 		      => __( 'Subject', 'woocommerce' ),
                    'type' 		        => 'text',
                    'description' 	  => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
                    'placeholder' 	  => '',
                    'default' 		    => ''
            ),
            'heading' => array(
                    'title' 		    => __( 'Email Heading', 'woocommerce' ),
                    'type' 		      => 'text',
                    'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
                    'placeholder' 	=> '',
                    'default' 		  => ''
            ),
            'email_type' => array(
                    'title' 		     => __( 'Email type', 'woocommerce' ),
                    'type' 		       => 'select',
                    'description' 	 => __( 'Choose which format of email to send.', 'woocommerce' ),
                    'default' 		   => 'html',
                    'class'		       => 'email_type',
                    'options'		=> array(
                            'plain'	=> __( 'Plain text', 'woocommerce' ),
                            'html' 	=> __( 'HTML', 'woocommerce' ),
                            'multipart' => __( 'Multipart', 'woocommerce' ),
                    )
            )
        );
    }

}