jQuery(document).ready( function($) {


	$( ".groupbuy-time-countdown" ).each( function( index ) {

		var time 		= $(this).data('time');
		var format 	= $(this).data('format');

		if ( format == '' ) {	format = 'yowdHMS';	}

		var etext ='';

		if( $(this).hasClass('future') ) {
				var etext = '<div class="started">'+wc_groupbuy_data.started+'</div>';
		} else {
				var etext = '<div class="over">'+wc_groupbuy_data.finished+'</div>';
		}

		if(wc_groupbuy_data.compact_counter == 'yes'){
			compact	 = true;
		} else{
			compact	 = false;
		}

		$(this).gbcountdown({

			until: $.gbcountdown.UTCDate( -(new Date().getTimezoneOffset()), new Date(time*1000) ),
			format: format,
			expiryText: etext,
			compact:  compact

		});

	});

});
