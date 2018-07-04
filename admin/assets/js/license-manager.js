/**
 * General
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 10.09.2017, Webcraftic
 * @version 1.0
 */


jQuery(function($) {
	
	$(document).on( 'click', '.wcl-control-btn', function() {
		$('.wcl-control-btn').hide();
		var wrapper = $('#wcl-license-wrapper');
		var loader = wrapper.data('loader');
		$(this).after('<img class="wcl-loader" src="'+loader+'">');
		var data = {
			action: 'wcl_licensing',
			_wpnonce: $('#_wpnonce').val(),
			license_action: $(this).data( 'action' ),
			licensekey: '',
		};
		if ( $(this).data( 'action' ) == 'activate' ) {
			data.licensekey = $('#license-key').val();
		}
		$.post( ajaxurl, data, function( response ) {
			wrapper.html( response );
		});
		return false;
	});
	
});
