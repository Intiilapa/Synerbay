jQuery(document).ready(function($){

    var calendar_image = '';

    if (typeof woocommerce_writepanel_params != 'undefined'){
            calendar_image = woocommerce_writepanel_params.calendar_image;
    } else if (typeof woocommerce_admin_meta_boxes != 'undefined'){
            calendar_image = woocommerce_admin_meta_boxes.calendar_image;
    }

    jQuery('.datetimepicker').datetimepicker({
        defaultDate: "",
        dateFormat: "yy-mm-dd",
        numberOfMonths: 1,
        showButtonPanel: true,
        showOn: "button",
        buttonImage: calendar_image,
        buttonImageOnly: true
    });

    var productType = jQuery('#product-type').val();
    if (productType=='groupbuy'){
        jQuery('.show_if_simple').show();
        jQuery('.inventory_options').hide();
        jQuery('#groupbuy_tab .required').each(function(index, el) {
            jQuery(this).attr("required", true);
        });
    } else {
        jQuery('#groupbuy_tab .required').each(function(index, el) {
            jQuery(this).attr("required", false);
        });
    }

    jQuery('#product-type').on('change', function(){
        if  (jQuery(this).val() =='groupbuy'){
            jQuery('.show_if_simple').show();
            jQuery('.inventory_options').hide();
            jQuery('#groupbuy_tab .required').each(function(index, el) {
                jQuery(this).attr("required", true);
            });
        } else {
            jQuery('#groupbuy_tab .required').each(function(index, el) {
                jQuery(this).attr("required", false);
            });
        }
    });

    jQuery('label[for="_virtual"]').addClass('show_if_groupbuy');

    jQuery('label[for="_downloadable"]').addClass('show_if_groupbuy');

    jQuery('.groupbuy-table .action a').on('click',function(event){
        var logid = $(this).data('id');
        var postid = $(this).data('postid');
        var curent = $(this);
        jQuery.ajax({
        type : "post",
        url : ajaxurl,
        data : {action: "delete_groupbuy_participate_entry", logid : logid, postid: postid},
        success: function(response) {
               if (response === 'deleted'){
                       curent.parent().parent().addClass('deleted').fadeOut('slow');
               }
           }
        });
        event.preventDefault();
    });

    jQuery('#groupbuy-refund').on('click',function(event){
        if ( window.confirm( woocommerce_admin_meta_boxes.i18n_do_refund ) ) {
            var product_id = $(this).data('product_id');
            var curent = $(this);
            var $wrapper     = $('#groupbuy' );

            $( "#refund-status" ).empty();

            $wrapper.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });


            jQuery.ajax({
            type : "post",
            url : ajaxurl,
            data : {action: "groupbuy_refund", product_id : product_id , security : woocommerce_groupbuy.groupbuy_refund_nonce},
            success: function(response) {
                if(response.error && jQuery.isEmptyObject(response.error) === false ){

                     $( "#refund-status" ).append( '<div class="error notice"></div>');

                    $.each(response.error, function(index, value) {

                        $( "#refund-status .error" ).append( '<p class"error">'+index + ': ' +value + '</p>' );

                    });
                }
                if(response.succes &&  jQuery.isEmptyObject(response.succes) === false){

                    $( "#refund-status" ).append( '<div class="updated notice"></div>');
                    $.each(response.succes, function(index, value) {

                        $( "#refund-status .updated " ).append( '<li class"ok">'+index + ': ' +value + '</li>' );

                    });
                }
                $wrapper.unblock();
               }
            });
        }
            event.preventDefault();
    });

    jQuery('#general_product_data #_regular_price').on('keyup',function(){
        jQuery('#groupbuy_tab #_groupbuy_regular_price').val(jQuery(this).val());
    });

    var groupbuymaxwinners = jQuery('#_groupbuy_num_winners').val();

     if ( groupbuymaxwinners > 1){
        $('._groupbuy_multiple_winner_per_user_field').show();
      } else{
        $('._groupbuy_multiple_winner_per_user_field').hide();
      }
    
    jQuery('#relistgroupbuy').on('click',function(event){
            event.preventDefault();
            jQuery('.relist_groupbuy_dates_fields').toggle();
            
        
    });
});

jQuery( function ( $ ) {
        $( document.body )
            .on( 'wc_add_error_tip_groupbuy', function( e, element, error_type ) {
            var offset = element.position();

            if ( element.parent().find( '.wc_error_tip' ).size() === 0 ) {
                element.after( '<div class="wc_error_tip ' + error_type + '">' + woocommerce_groupbuy[error_type] + '</div>' );
                element.parent().find( '.wc_error_tip' )
                    .css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
                    .css( 'top', offset.top + element.height() )
                    .fadeIn( '100' );
            }
        })
        .on( 'wc_remove_error_tip_groupbuy', function( e, element, error_type ) {
            element.parent().find( '.wc_error_tip.' + error_type ).remove();
        })

        .on( 'keyup change', '#_max_tickets.input_text[type=number]', function() {
            var max_ticket_field = $( this ), min_ticket_field;

            min_ticket_field = $( '#_min_tickets' );

            var max_ticket    = parseInt( max_ticket_field.val());
            var min_ticket = parseInt( min_ticket_field.val());

            if ( max_ticket <= min_ticket ) {
                $( document.body ).triggerHandler( 'wc_add_error_tip_groupbuy', [ $(this), 'i18_max_ticket_less_than_min_ticket_error' ] );
            } else {
                $( document.body ).triggerHandler( 'wc_remove_error_tip_groupbuy', [ $(this), 'i18_max_ticket_less_than_min_ticket_error' ] );
            }
        })

         .on( 'keyup change focusout ', '#_groupbuy_num_winners.input_text[type=number]', function() {
            var groupbuy_num_winners_field = $( this );
            var groupbuy_winers    = parseInt( groupbuy_num_winners_field.val());

            if ( groupbuy_winers <= 0 || !groupbuy_winers) {
                $( document.body ).triggerHandler( 'wc_add_error_tip_groupbuy', [ $(this), 'i18_minimum_winers_error' ] );
            } else {
                $( document.body ).triggerHandler( 'wc_remove_error_tip_groupbuy', [ $(this), 'i18_minimum_winers_error' ] );
            }


              if ( groupbuy_winers > 1){
                $('._groupbuy_multiple_winner_per_user_field').show();
              } else{
                $('._groupbuy_multiple_winner_per_user_field').hide();
              }
        });

});
