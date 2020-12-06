<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    wc_groupbuy
 * @subpackage wc_groupbuy/includes
 * @author     wpgenie <info@wpgenie.org>
 */
class wc_groupbuy_i18n {
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        $domain = 'wc_groupbuy';
        $langdir = dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages';
        load_plugin_textdomain( $domain, false, $langdir.'/');

    }

}
