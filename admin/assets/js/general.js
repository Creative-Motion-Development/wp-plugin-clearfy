/**
 * General
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 10.09.2017, Webcraftic
 * @version 1.0
 */


(function($) {
	'use strict';

	var general = {
		init: function() {
			this.qickStartAssistent();
			this.importOptions();
		},
		qickStartAssistent: function() {
			var self = this;

			$('.wbcr-clearfy-button-activate-mode').click(function() {

				var switcher = $(this).closest('.wbcr-clearfy-switch'),
					modeName = switcher.data('mode'),
					modeOptions = switcher.data('mode-options');

				if( switcher.hasClass('wbcr-clearfy-loading') || switcher.hasClass('wbcr-clearfy-active') ) {
					return false;
				}

				self.showConfirmationPopup(modeName, modeOptions);
				return false;
			});

			$('.wbcr-clearfy-popup-button-cancel').click(function() {
				self.hideConfirmationPopup();
			});

			/*$('.wbcr-clearfy-button-deativate-mode').click(function() {
			 var $this = $(this),
			 switcher = $(this).closest('.wbcr-clearfy-switch'),
			 modeName = switcher.data('mode');

			 if( switcher.hasClass('wbcr-clearfy-loading') ) {
			 return false;
			 }

			 switcher.addClass('wbcr-clearfy-loading');

			 self.sendRequest({
			 action: 'wbcr_clearfy_configurate',
			 mode: modeName,
			 cancel_mode: true
			 }, function(data) {
			 switcher.removeClass('wbcr-clearfy-loading');

			 if( data && data.export_options ) {
			 $('#wbcr-clearfy-import-export').html(data.export_options);
			 }
			 },
			 function() {
			 if( modeName != 'reset' ) {
			 switcher.removeClass('wbcr-clearfy-active');
			 }
			 });
			 return false;
			 });*/

			$('.wbcr-clearfy-popup-button-ok').click(function() {
				var $this = $(this), modeName = $(this).closest('.wbcr-clearfy-confirm-popup').data('mode'),
					switcher = $('div[data-mode="' + modeName + '"]', '#wbcr-clearfy-quick-mode-board');

				self.hideConfirmationPopup();
				switcher.addClass('wbcr-clearfy-loading');

				self.sendRequest({
						action: 'wbcr_clearfy_configurate',
						mode: modeName
					}, function(data) {
						switcher.removeClass('wbcr-clearfy-loading');

						if( data && data.export_options ) {
							$('#wbcr-clearfy-import-export').html(data.export_options);
						}
					},
					function() {
						if( modeName != 'reset' ) {
							switcher.addClass('wbcr-clearfy-active');
							return;
						}

						$('.wbcr-clearfy-switch').removeClass('wbcr-clearfy-active');
					});

				return false;
			});
		},

		showConfirmationPopup: function(modeName, options) {
			var self = this;

			$('.wbcr-clearfy-layer').fadeIn();

			var popupElem = $('.wbcr-clearfy-confirm-popup');
			popupElem.data('mode', modeName);
			popupElem.fadeIn();

			if( modeName != 'reset' ) {
				var printOptTitles = '';

				if( options ) {
					for( var opt in options ) {
						if( !options.hasOwnProperty(opt) ) {
							continue;
						}
						printOptTitles += '<li>' + options[opt] + '</li>';

					}
					$('.wbcr-clearfy-list-options').html(printOptTitles);
					popupElem.addClass('wbcr-clearfy-default-warning-options');
				}
				return;
			}

			popupElem.addClass('wbcr-clearfy-reset-warning-options');
		},

		hideConfirmationPopup: function() {
			$('.wbcr-clearfy-layer').fadeOut(100);
			var popupElem = $('.wbcr-clearfy-confirm-popup');

			popupElem.fadeOut(100, function() {
				popupElem.removeClass('wbcr-clearfy-default-warning-options');
				popupElem.removeClass('wbcr-clearfy-reset-warning-options');
			});

		},

		importOptions: function() {
			var self = this;

			$('.wbcr-clearfy-import-options-button').click(function() {
				var settings = $('#wbcr-clearfy-import-export').val(),
					$this = $(this);

				if( !settings ) {
					return;
				}

				$(this).prop('disabled', true);

				self.sendRequest({
					action: 'wbcr_clearfy_import_settings',
					settings: settings
				}, function(data) {
					//console.log(data);
					$this.prop('disabled', false);
				});

				return false;
			});
		},
		sendRequest: function(data, beforeValidateCallback, successCallback) {

			var errorContanier = $('.wbcr-clearfy-switch-error-message'),
				defaultErrorMessage = errorContanier.text();

			if( wbcr_clearfy_ajax === undefined ) {
				console.log('Undefinded wbcr_clearfy_ajax object.');
				return;
			}

			var ajaxUrl = wbcr_clearfy_ajax ? wbcr_clearfy_ajax.ajaxurl : ajaxurl;

			if( typeof data === 'object' ) {
				data.security = wbcr_clearfy_ajax.ajax_nonce;
			}

			$.ajax(ajaxurl, {
				type: 'post',
				dataType: 'json',
				data: data,
				success: function(data, textStatus, jqXHR) {

					beforeValidateCallback && beforeValidateCallback(data);

					if( !data || data.error ) {
						if( data ) {
							console.log(data.error);
							if( !errorContanier.is(':visible') ) {
								errorContanier.html(defaultErrorMessage + '<br>' + data.error);
							}
						}

						if( !errorContanier.is(':visible') ) {
							errorContanier.fadeIn(600).delay(10000).fadeOut(600);
						}
						return;
					}

					successCallback && successCallback();

					$('.wbcr-clearfy-switch-success-message').fadeIn(600).delay(3000).fadeOut(600);

				}
			});
		}
	};

	$(document).ready(function() {
		general.init();
	});

})(jQuery);
