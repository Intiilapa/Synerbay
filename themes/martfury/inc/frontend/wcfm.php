<?php

/**
 * Class for all Vendor template modification
 *
 * @version 1.0
 */
class Martfury_WCFM {

	/**
	 * Construction function
	 *
	 * @since  1.0
	 * @return Martfury WCFM
	 */
	function __construct() {
		// Check if Woocomerce plugin is actived
		if ( ! class_exists( 'WCFM' ) ) {
			return;
		}

		if ( class_exists( 'TAWC_Deals' ) ) {
			add_filter( 'wcfm_product_manage_fields_pricing', array( $this, 'product_manage_fields_pricing' ), 20, 2 );
		}

		add_filter( 'wcfm_product_manage_fields_linked', array( $this, 'products_custom_fields_linked' ), 100, 3 );

		add_action( 'after_wcfm_products_manage_meta_save', array( $this, 'product_meta_save' ), 500, 2 );

		add_filter( 'wcfmmp_stores_default_args', array( $this, 'stores_list_default_args' ) );

		add_action( 'after_wcfm_products_manage_linked', array( $this, 'products_custom_fields' ), 20, 2 );

	}

	function product_manage_fields_pricing( $fields, $product_id ) {
		$quantity                 = get_post_meta( $product_id, '_deal_quantity', true );
		$sales_counts             = get_post_meta( $product_id, '_deal_sales_counts', true );
		$sales_counts             = intval( $sales_counts );
		$fields["_deal_quantity"] = array(
			'label'       => esc_html__( 'Sale quantity', 'martfury' ),
			'type'        => 'number',
			'class'       => 'wcfm-text wcfm_ele wcfm_half_ele sales_schedule_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking',
			'label_class' => 'wcfm_ele wcfm_half_ele_title sales_schedule_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking',
			'hints'       => esc_html__( 'Set this quantity will make the product to be a deal. The sale will end when this quantity is sold out.', 'martfury' ),
			'value'       => $quantity
		);

		$fields["_deal_sales_counts"] = array(
			'label'       => esc_html__( 'Sold Items', 'martfury' ),
			'type'        => 'number',
			'class'       => 'wcfm-text wcfm_ele wcfm_half_ele sales_schedule_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking',
			'label_class' => 'wcfm_ele wcfm_half_ele_title sales_schedule_ele wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking',
			'hints'       => esc_html__( 'Set this sold items should be less than the sale quantity.', 'martfury' ),
			'value'       => $sales_counts
		);

		return $fields;
	}

	function product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM;

		if ( class_exists( 'TAWC_Deals' ) ) {
			$_deal_quantity     = ( isset( $wcfm_products_manage_form_data['_deal_quantity'] ) ) ? intval( $wcfm_products_manage_form_data['_deal_quantity'] ) : 0;
			$_deal_sales_counts = ( isset( $wcfm_products_manage_form_data['_deal_sales_counts'] ) ) ? intval( $wcfm_products_manage_form_data['_deal_sales_counts'] ) : 0;
			update_post_meta( $new_product_id, '_deal_quantity', $_deal_quantity );
			if ( $_deal_quantity >= $_deal_sales_counts ) {
				update_post_meta( $new_product_id, '_deal_sales_counts', $_deal_sales_counts );
			}
		}

		$pbt_product_ids = ( isset( $wcfm_products_manage_form_data['mf_pbt_product_ids'] ) ) ? array_map( 'intval', (array) $wcfm_products_manage_form_data['mf_pbt_product_ids'] ) : array();
		update_post_meta( $new_product_id, 'mf_pbt_product_ids', $pbt_product_ids );

		// Video
		$video_url = ( isset( $wcfm_products_manage_form_data['video_url'] ) ) ? $wcfm_products_manage_form_data['video_url'] : '';
		update_post_meta( $new_product_id, 'video_url', $video_url );

		$video_thumbnail_src = ( isset( $wcfm_products_manage_form_data['video_thumbnail_src'] ) ) ? $wcfm_products_manage_form_data['video_thumbnail_src'] : '';

		$video_thumbnail_id = $WCFM->wcfm_get_attachment_id( $video_thumbnail_src );

		update_post_meta( $new_product_id, 'video_thumbnail', $video_thumbnail_id );

		$video_position = ( isset( $wcfm_products_manage_form_data['video_position'] ) ) ? $wcfm_products_manage_form_data['video_position'] : '';
		update_post_meta( $new_product_id, 'video_position', $video_position );

		$product_360_ids = array();
		if ( isset( $wcfm_products_manage_form_data['product_360_view_src'] ) ) {
			foreach ( $wcfm_products_manage_form_data['product_360_view_src'] as $gallery_imgs ) {
				$product_360_src = isset( $gallery_imgs['image'] ) ? $gallery_imgs['image'] : '';
				if ( $product_360_src ) {
					$product_360_ids[] = $WCFM->wcfm_get_attachment_id( $product_360_src );
				}

			}
		}

		if ( ! empty( $product_360_ids ) ) {
			update_post_meta( $new_product_id, 'wcfm_product_360_view', implode( ',', $product_360_ids ) );
		} else {
			update_post_meta( $new_product_id, 'wcfm_product_360_view', '' );
		}

		$custom_badges_text = ( isset( $wcfm_products_manage_form_data['custom_badges_text'] ) ) ? $wcfm_products_manage_form_data['custom_badges_text'] : '';
		$_is_new            = ( isset( $wcfm_products_manage_form_data['_is_new'] ) ) ? 'yes' : 'no';
		update_post_meta( $new_product_id, 'custom_badges_text', $custom_badges_text );
		update_post_meta( $new_product_id, '_is_new', $_is_new );
	}

	function products_custom_fields_linked( $fields, $product_id, $products_array ) {

		if ( ! intval( martfury_get_option( 'product_fbt' ) ) ) {
			return $fields;
		}

		if ( ! in_array( 'fbt', martfury_get_option( 'wcfm_dashboard_custom_fields' ) ) ) {
			return $fields;
		}

		$pbt_product_ids = get_post_meta( $product_id, 'mf_pbt_product_ids', true );
		$pbt_product_ids = $pbt_product_ids ? $pbt_product_ids : array();
		if ( ! empty( $pbt_product_ids ) ) {
			foreach ( $pbt_product_ids as $pbt_product_id ) {
				$products_array[ $pbt_product_id ] = get_post( absint( $pbt_product_id ) )->post_title;
			}
		}
		$fields["mf_pbt_product_ids"] = array(
			'label'       => esc_html__( 'Frequently Bought Together', 'martfury' ),
			'type'        => 'select',
			'attributes'  => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ),
			'class'       => 'wcfm-select wcfm_ele simple variable',
			'label_class' => 'wcfm_title',
			'options'     => $products_array,
			'value'       => $pbt_product_ids,
		);

		return $fields;

	}

	function products_custom_fields( $product_id, $product_type ) {
		global $WCFM;
		if ( in_array( 'video', martfury_get_option( 'wcfm_dashboard_custom_fields' ) ) ) {
			?>
            <!-- collapsible 8 - Product Video -->
            <div class="page_collapsible products_manage_video <?php echo apply_filters( 'wcfm_pm_block_class_linked', 'simple variable external grouped' ); ?> <?php echo apply_filters( 'wcfm_pm_block_custom_class_linked', '' ); ?>"
                 id="wcfm_products_manage_form_linked_head"><label
                        class="wcfmfa fa-video"></label><?php esc_html_e( 'Product Video', 'martfury' ); ?><span></span>
            </div>
            <div class="wcfm-container simple variable external grouped <?php echo apply_filters( 'wcfm_pm_block_custom_class_linked', '' ); ?>">
                <div id="wcfm_products_manage_form_linked_expander" class="wcfm-content">
					<?php

					$video_url           = get_post_meta( $product_id, 'video_url', true );
					$video_thumbnail_id  = get_post_meta( $product_id, 'video_thumbnail', true );
					$image_thumbnail     = wp_get_attachment_image_src( $video_thumbnail_id, 'full' );
					$video_thumbnail_src = $image_thumbnail ? $image_thumbnail[0] : '';
					$video_position      = get_post_meta( $product_id, 'video_position', true );
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_video', array(
						"video_url"           => array(
							'label'       => esc_html__( 'Video URL', 'martfury' ),
							'type'        => 'text',
							'class'       => 'wcfm-text wcfm_ele simple variable external grouped booking',
							'label_class' => 'wcfm_title',
							'value'       => $video_url
						),
						"video_thumbnail_src" => array(
							'label'       => esc_html__( 'Video Thumbnail', 'martfury' ),
							'type'        => 'upload',
							'class'       => 'wcfm-upload wcfm_ele simple variable external grouped booking',
							'label_class' => 'wcfm_title',
							'value'       => $video_thumbnail_src
						),
						"video_position"      => array(
							'label'       => esc_html__( 'Video Position', 'martfury' ),
							'type'        => 'select',
							'class'       => 'wcfm-select wcfm_ele simple variable external grouped booking',
							'label_class' => 'wcfm_title',
							'options'     => array(
								'1' => esc_html__( 'The last product gallery', 'martfury' ),
								'2' => esc_html__( 'The first product gallery', 'martfury' ),
							),
							'value'       => $video_position
						),

					), $product_id ) );
					?>
                </div>
            </div>
            <!-- end collapsible -->
            <div class="wcfm_clearfix"></div>
		<?php } ?>
		<?php
		if ( in_array( '360', martfury_get_option( 'wcfm_dashboard_custom_fields' ) ) ) {

			?>
            <!-- collapsible 8 - Product 360 -->
            <div class="page_collapsible products_manage_360_view <?php echo apply_filters( 'wcfm_pm_block_class_linked', 'simple variable external grouped' ); ?> <?php echo apply_filters( 'wcfm_pm_block_custom_class_linked', '' ); ?>"
                 id="wcfm_products_manage_form_linked_head"><label
                        class="wcfmfa fa-film"></label><?php esc_html_e( 'Product 360 View', 'martfury' ); ?>
                <span></span>
            </div>
            <div class="wcfm-container simple variable external grouped <?php echo apply_filters( 'wcfm_pm_block_custom_class_linked', '' ); ?>">
                <div id="wcfm_products_manage_form_linked_expander" class="wcfm-content">
					<?php
					$images_meta = get_post_meta( $product_id, 'wcfm_product_360_view', true );
					$images_meta = $images_meta ? explode( ',', $images_meta ) : array();
					$images_360  = array();
					if ( $images_meta ) {
						foreach ( $images_meta as $image_id ) {
							$image                 = wp_get_attachment_image_src( $image_id, 'full' );
							$images_360[]['image'] = $image ? $image[0] : '';
						}
					}

					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_video', array(
						"product_360_view_src" => array(
							'label'       => esc_html__( 'Images', 'martfury' ),
							'type'        => 'multiinput',
							'class'       => 'wcfm-text wcfm-gallery_image_upload wcfm_ele simple variable external grouped booking',
							'label_class' => 'wcfm_title',
							'value'       => $images_360,
							'options'     => array(
								"image" => array(
									'type'    => 'upload',
									'class'   => 'wcfm_gallery_upload',
									'prwidth' => 75
								),
							),
						),

					), $product_id ) );
					?>
                </div>
            </div>
            <!-- end collapsible -->
            <div class="wcfm_clearfix"></div>
			<?php
		}

		?>
        <!-- collapsible 8 - Custom  Badges -->
        <div class="page_collapsible products_badges_view <?php echo apply_filters( 'wcfm_pm_block_class_linked', 'simple variable external grouped' ); ?> <?php echo apply_filters( 'wcfm_pm_block_custom_class_linked', '' ); ?>"
             id="wcfm_products_manage_form_linked_head"><label
                    class="wcfmfa fa-globe"></label><?php esc_html_e( 'Badges', 'martfury' ); ?>
            <span></span>
        </div>
        <div class="wcfm-container simple variable external grouped <?php echo apply_filters( 'wcfm_pm_block_custom_class_linked', '' ); ?>">
            <div id="wcfm_products_manage_form_linked_expander" class="wcfm-content">
				<?php
				$custom_badges_text = get_post_meta( $product_id, 'custom_badges_text', true );
				$is_new             = get_post_meta( $product_id, '_is_new', true );
				$_is_new_enable     = $is_new === 'yes' ? 'enable' : '';
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_badges', array(
					"custom_badges_text" => array(
						'label'       => esc_html__( 'Custom Badge Text', 'martfury' ),
						'type'        => 'text',
						'class'       => 'wcfm-text wcfm_ele simple variable external grouped booking',
						'label_class' => 'wcfm_title',
						'value'       => $custom_badges_text
					),
					"_is_new"            => array(
						'label'       => esc_html__( 'New product?', 'martfury' ),
						'type'        => 'checkbox',
						'class'       => 'wcfm-checkbox wcfm_ele simple variable external grouped booking',
						'label_class' => 'wcfm_title',
						'hints'       => esc_html__( 'Enable to set this product as a new product. A "New" badge will be added to this product.', 'martfury' ),
						'value'       => 'enable',
						'dfvalue'     => $_is_new_enable
					),
				), $product_id ) );
				?>
            </div>
        </div>
        <!-- end collapsible -->
        <div class="wcfm_clearfix"></div>
		<?php
	}

	function stores_list_default_args( $default ) {
		$default['per_row']  = 2;
		$default['per_page'] = 8;
		$default['theme']    = '';

		return $default;
	}

}

