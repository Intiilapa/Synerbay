<?php

/**
 * Class for all Vendor template modification
 *
 * @version 1.0
 */
class Martfury_WCFMVendors {

	/**
	 * Construction function
	 *
	 * @since  1.0
	 * @return Martfury_Vendor
	 */
	function __construct() {
		// Check if Woocomerce plugin is actived
		if ( ! class_exists( 'WCFMmp' ) ) {
			return;
		}

		//remove display vendor by plugin
		add_filter( 'wcfmmp_is_allow_archive_product_sold_by', '__return_false' );

		switch ( martfury_get_option( 'catalog_vendor_name' )) {
			case 'display':
				// Always Display sold by
				add_action( 'woocommerce_shop_loop_item_title', array( $this, 'product_loop_display_sold_by' ), 6 );

				// Display sold by in product list
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'product_loop_sold_by' ), 7 );

				// Display sold by on hover
				add_action( 'martfury_product_loop_details_hover', array( $this, 'product_loop_sold_by' ), 15 );

				// Display sold by in product deals
				add_action( 'martfury_woo_after_shop_loop_item_title', array( $this, 'product_loop_sold_by' ), 20 );
				break;

			case 'hover':

				if ( martfury_get_option( 'product_loop_hover' ) == '3' ) {
					// Always Display sold by
					add_action( 'woocommerce_shop_loop_item_title', array(
						$this,
						'product_loop_display_sold_by'
					), 6 );
				}

				// Display sold by in product list
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'product_loop_sold_by' ), 7 );

				// Display sold by on hover
				add_action( 'martfury_product_loop_details_hover', array( $this, 'product_loop_sold_by' ), 15 );

				// Display sold by in product deals
				add_action( 'martfury_woo_after_shop_loop_item_title', array( $this, 'product_loop_sold_by' ), 20 );
				break;
			case 'profile':

				// Always Display sold by
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_vendor_profile' ), 10 );

				// Display sold by on hover
				add_action( 'martfury_product_loop_details_hover', array( $this, 'display_vendor_profile' ), 45 );

				// Display sold by in product deals
				add_action( 'martfury_woo_after_shop_loop_item', array( $this, 'display_vendor_profile' ), 20 );
				break;
		}


		if ( martfury_get_option( 'wcfm_single_sold_by_template' ) == 'theme' ) {
			add_filter( 'wcfmmp_is_allow_single_product_sold_by', '__return_false' );

			add_action( 'martfury_single_product_header', array(
				$this,
				'product_loop_sold_by',
			) );
		}

		add_filter( 'body_class', array(
			$this,
			'wcfm_body_classes',
		) );

		if ( martfury_get_option( 'wcfm_store_header_layout' ) == 'theme' ) {

			add_filter( 'wcfm_is_allow_store_name_on_header', '__return_true' );
			add_filter( 'wcfm_is_allow_store_name_on_banner', '__return_false' );
		}

		add_filter( 'martfury_site_content_container_class', array( $this, 'vendor_dashboard_container_class' ) );
		add_filter( 'martfury_page_header_container_class', array( $this, 'vendor_dashboard_container_class' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 30 );
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'martfury-wcfm', get_template_directory_uri() . '/css/vendors/wcfm-vendor.css', array(), '1.0.0' );
	}


	function product_loop_display_sold_by() {
		echo '<div class="mf-vendor-name">';
		$this->product_loop_sold_by();
		echo '</div>';
	}


	function product_loop_sold_by() {

		if ( ! class_exists( 'WCFM' ) ) {
			return;
		}

		global $WCFM, $post, $WCFMmp;

		$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );

		if ( ! $vendor_id ) {
			return;
		}

		$sold_by_text = apply_filters( 'wcfmmp_sold_by_label', esc_html__( 'Sold By:', 'martfury' ) );
		if ( $WCFMmp ) {
			$sold_by_text = $WCFMmp->wcfmmp_vendor->sold_by_label( absint( $vendor_id ) );
		}
		$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint( $vendor_id ) );

		echo '<div class="sold-by-meta">';
		echo '<span class="sold-by-label">' . $sold_by_text . ': ' . '</span>';
		echo wp_kses_post( $store_name );
		echo '</div>';
	}

	function display_vendor_profile() {
		global $WCFM, $WCFMmp, $product;

		if ( function_exists('wcfm_is_store_page') && wcfm_is_store_page() ) {
			return;
		}
		if ( ! $product ) {
			return;
		}
		if ( ! method_exists( $product, 'get_id' ) ) {
			return;
		}

		if ( $WCFMmp->wcfmmp_vendor->is_vendor_sold_by() ) {
			$product_id = $product->get_id();

			$vendor_id = wcfm_get_vendor_id_by_post( $product_id );

			if ( apply_filters( 'wcfmmp_is_allow_archive_sold_by_advanced', false ) ) {
				$WCFMmp->template->get_template( 'sold-by/wcfmmp-view-sold-by-advanced.php', array(
					'product_id' => $product_id,
					'vendor_id'  => $vendor_id
				) );
			} else {
				$WCFMmp->template->get_template( 'sold-by/wcfmmp-view-sold-by-simple.php', array(
					'product_id' => $product_id,
					'vendor_id'  => $vendor_id
				) );
			}
		}
	}

	function wcfm_body_classes( $classes ) {
		if ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() && martfury_get_option( 'wcfm_store_header_layout' ) == 'theme' ) {
			$classes[] = 'wcfm-template-themes';
		}

		if ( martfury_get_option( 'catalog_vendor_name' ) == 'profile' ) {
			$classes[] = 'mf-vendor-profile';
		}

		return $classes;
	}

	function vendor_dashboard_container_class( $container ) {

		if ( ! function_exists( 'is_wcfm_page' ) ) {
			return $container;
		}

		if ( is_wcfm_page() ) {
			if ( intval( martfury_get_option( 'vendor_dashboard_full_width' ) ) ) {
				$container = 'martfury-container';
			}
		}

		return $container;
	}

}

