/**
 * This code provides tools for downloading, installing external add-ons for the Clearfy plugin
 *
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 10.09.2017, Webcraftic
 * @version 1.0
 */


(function($) {
	'use strict';

	var clearfyPackage = {
		init: function() {
			this.events();
		},
		events: function() {
			var self = this;

			/**
			 * This event is intended for installation, removal, activation, deactivation of external add-ons
			 */

			$(document).on('click', '.wbcr-clr-update-package', function() {
				var $this = $(this),
					loading = $(this).data( 'loading' ),
					wpnonce = $(this).data('wpnonce');
					
				var data = {
					action: 'wbcr-clearfy-update-package',
					_wpnonce: wpnonce
				};

				$this.addClass('disabled').text(loading);
				
				self.sendRequest(data, function(response) {
					var alert_block = $this.closest('div.alert');
					if( response.success ) { 
						alert_block.removeClass('alert-warning').addClass('alert-success');
						alert_block.find('p').html( '<span class="dashicons dashicons-plus"></span> ' + response.data.msg );
						setTimeout( function() { alert_block.hide() }, 3000 );
					} else {
						alert_block.removeClass('alert-warning').addClass('alert-danger');
						alert_block.find('p').html( '<span class="dashicons dashicons-warning"></span> ' + response.data.msg );
					}
				});
				
				return false;
			});
		},
		sendRequest: function(data, callback) {
			$.ajax(ajaxurl, {
				type: 'post',
				dataType: 'json',
				data: data,
				success: function(data, textStatus, jqXHR) {
					callback && callback(data);
				},
				error: function(data) {
					console.log(data);
				}
			});
		}
	};

	$(document).ready(function() {
		clearfyPackage.init();
	});

})(jQuery);
