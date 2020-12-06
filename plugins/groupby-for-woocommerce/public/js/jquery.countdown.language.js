/* http://keith-wood.name/countdown.html
 */
(function($) {
	$.gbcountdown.regionalOptions['us'] = {
		labels: [wc_groupbuy_language_data .labels.Years, wc_groupbuy_language_data .labels.Months, wc_groupbuy_language_data .labels.Weeks, wc_groupbuy_language_data .labels.Days, wc_groupbuy_language_data .labels.Hours, wc_groupbuy_language_data .labels.Minutes, wc_groupbuy_language_data .labels.Seconds],
		labels1: [wc_groupbuy_language_data .labels1.Year, wc_groupbuy_language_data .labels1.Month, wc_groupbuy_language_data .labels1.Week, wc_groupbuy_language_data .labels1.Day, wc_groupbuy_language_data .labels1.Hour, wc_groupbuy_language_data .labels1.Minute, wc_groupbuy_language_data .labels1.Second],
		
		compactLabels: [wc_groupbuy_language_data .compactLabels.y, wc_groupbuy_language_data .compactLabels.m, wc_groupbuy_language_data .compactLabels.w, wc_groupbuy_language_data .compactLabels.d],
		whichLabels: function(amount) {
			return (amount == 1 ? 1 : (amount >= 2 && amount <= 4 ? 2 : 0));
		},
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.gbcountdown.setDefaults($.gbcountdown.regionalOptions['us']);
})(jQuery);
