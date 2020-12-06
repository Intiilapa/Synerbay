<?php
/**
 * Plugin Name: SynerBay
 * Description: Offer apply, invite, RFQ
 * Version: 1.0
 * Author: Kristóf Nagy
 */

namespace SynerBay;

if( ! defined( 'ABSPATH' ) ) {
    return;
}

class SynerBay {
    /**
     * Loading all dependencies
     * @return void
     */
    public function load() {
        add_action( 'wp_enqueue_scripts', [$this, 'loadScript'], 10);
        // basic files
        include_once 'WPActionLoader.php';
        include_once 'HTMLElement.php';
        include_once 'Ajax.php';
        include_once 'initPlugin.php';
    }

    public function loadScript() {
        // load synerbay.js
        wp_register_script( "synerbay-script", get_stylesheet_directory_uri() . '/synerbay.js');

        // load synerbay.js
        wp_enqueue_script("synerbay-script", get_stylesheet_directory_uri() . '/synerbay.js', ['jquery'], false, true);

        // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
        wp_localize_script( 'synerbay-script', 'synerbayAjax', array(
            'restURL' => rest_url(),
            'restNonce' => wp_create_nonce('wp_rest')
        ));
    }
}

/**
 * Loading my plugin
 * @return void
 */
function synerBayLoad() {
    $pg = new SynerBay();
    $pg->load();
}

// We need to call the function with the namespace
add_action('plugins_loaded', 'SynerBay\synerBayLoad');
