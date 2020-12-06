<?php
/**
 * Participate in Group Buy deal template
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product, $post;
$current_user                = wp_get_current_user();
$groupbuy_dates_to           = $product->get_groupbuy_dates_to();
$groupbuy_dates_from         = $product->get_groupbuy_dates_from();
$groupbuy_min_deal           = $product->get_groupbuy_min_deals();
$groupbuy_max_deals          = $product->get_groupbuy_max_deals();
$groupbuy_participants_count = $product->get_groupbuy_participants_count();

 if(($product->is_closed() === FALSE ) and ($product->is_started() === TRUE )) : ?>

    <div class="groupbuy-time" id="countdown"><?php echo apply_filters('time_text', __( 'Time left:', 'wc_groupbuy' ), $product); ?>
            <div class="main-groupbuy groupbuy-time-countdown" data-time="<?php echo $product->get_seconds_remaining() ?>" data-groupbuyid="<?php echo $product->get_id() ?>" data-format="<?php echo get_option( 'simple_groupbuy_countdown_format' ) ?>"></div>
    </div>

    <div class='groupbuy-ajax-change'>

            <p class="groupbuy-end"><?php echo __( 'Group Buy deal ends:', 'wc_groupbuy' ); ?> <?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $groupbuy_dates_to ));  ?>  <?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $groupbuy_dates_to ));  ?> <br />
                    <?php printf(__('Timezone: %s','wc_groupbuy') , get_option('timezone_string') ? get_option('timezone_string') : __('UTC+','wc_groupbuy').get_option('gmt_offset')) ?>
            </p>

            <p class="deal-info">
                    <span class="mind-eals"> <?php _e( 'Minimum:', 'wc_groupbuy' )?> <?php echo !empty($groupbuy_min_deal) ?  $groupbuy_min_deal : '0' ;?></span>
                    <span class="max-deals"> <?php _e( 'Maximum:', 'wc_groupbuy' )?> <?php echo !empty($groupbuy_max_deals ) ?  $groupbuy_max_deals  :  __( 'No maximum', 'wc_groupbuy' ) ;?></span>
                    <span class="current-sold"> <?php _e( 'Deals sold:', 'wc_groupbuy' )?> <?php echo !empty($groupbuy_participants_count) ?  $groupbuy_participants_count : '0' ;?></span>
             </p>

            <?php if( isset($groupbuy_max_deals)  &&( $groupbuy_max_deals > 0 )  && (get_option( 'simple_groupbuy_progressbar' ,'yes' ) == 'yes') ) : ?>

            <div class="wcl-progress-meter <?php if($product->is_groupbuy_max_deals_met()) {echo 'full';}  if(!$product->is_groupbuy_min_deals_met()) {echo 'no-min';} ?>">
                <span class="zero">0</span>
                <span class="max"><?php echo $groupbuy_max_deals ?></span>
                <progress  max="<?php echo $groupbuy_max_deals ?>" value="<?php echo !empty($groupbuy_participants_count) ? $groupbuy_participants_count : '0' ?>"  low="<?php echo $groupbuy_min_deal ?>"></progress>
            </div>

            <?php endif; ?>

    </div>

<?php elseif (($product->is_closed() === FALSE ) and ($product->is_started() === FALSE )):?>

	<div class="groupbuy-time future" id="countdown"><?php echo  __( 'Group Buy deal starts in:', 'wc_groupbuy' ) ?>
		<div class="groupbuy-time-countdown future" data-time="<?php echo $product->get_seconds_to_groupbuy() ?>" data-format="<?php echo get_option( 'simple_groupbuy_countdown_format' ) ?>"></div>
	</div>

	<p class="groupbuy-starts"><?php echo  __( 'Group Buy deal starts:', 'wc_groupbuy' ) ?> <?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $groupbuy_dates_from ));  ?>  <?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $groupbuy_dates_from ));  ?></p>
	<p class="groupbuy-end"><?php echo  __( 'Group Buy deal ends:', 'wc_groupbuy' ); ?> <?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $groupbuy_dates_to ));  ?>  <?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $groupbuy_dates_to ));  ?> </p>

<?php endif;
