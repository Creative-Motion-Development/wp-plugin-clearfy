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
				var $this = $(this), modeName = $this.closest('.wbcr-clearfy-confirm-popup').data('mode'),
					switcher = $('div[data-mode="' + modeName + '"]', '#wbcr-clearfy-quick-mode-board'),
					modeArgs = switcher.data('mode-args'),
					flushRedirect = modeArgs && modeArgs.flush_redirect ? true : false;

				self.hideConfirmationPopup();
				switcher.addClass('wbcr-clearfy-loading');

				self.sendRequest({
						action: 'wbcr_clearfy_configurate',
						mode: modeName,
						flush_redirect: flushRedirect
					}, function(data) {
						if( !flushRedirect ) {
							switcher.removeClass('wbcr-clearfy-loading');
						}

						if( !data || data.error ) {
							$.wbcr_clearfy.hooks.run('clearfy/quick_start/configurated_error', [modeName, data]);
							return;
						}

						if( data.export_options ) {
							$('#wbcr-clearfy-import-export').html(data.export_options);
						}
					},
					function(data) {
						$.wbcr_clearfy.hooks.run('clearfy/quick_start/configurated', [modeName, data]);

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
					if( data.updateNotice ) {
						if( !$('.wbcr-clr-update-package').length ) {
							$('.wbcr-factory-content').prepend(
								'<div class="alert alert-warning wbcr-factory-warning-notice">\
									<p>\
									<span class="dashicons dashicons-warning"></span>\
									' + data.updateNotice + '\
								</p>\
							</div>\
							');
						}
					} else {
						if( $('.wbcr-clr-update-package').length ) {
							$('.wbcr-clr-update-package').closest('.wbcr-factory-warning-notice').remove();
						}
					}
				});

				return false;
			});
		},
		sendRequest: function(request_data, beforeValidateCallback, successCallback) {
			var self = this,
				errorContanier = $('.wbcr-clearfy-switch-error-message'),
				defaultErrorMessage = errorContanier.text();

			if( wbcr_clearfy_ajax === undefined ) {
				console.log('Undefinded wbcr_clearfy_ajax object.');
				return;
			}

			//var ajaxUrl = wbcr_clearfy_ajax ? wbcr_clearfy_ajax.ajaxurl : ajaxurl;

			if( typeof request_data === 'object' ) {
				request_data.security = wbcr_clearfy_ajax.ajax_nonce;
			}

			$.ajax(ajaxurl, {
				type: 'post',
				dataType: 'json',
				data: request_data,
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

					successCallback && successCallback(data);

					if( !request_data.flush_redirect ) {
						$('.wbcr-clearfy-switch-success-message').fadeIn(600).delay(3000).fadeOut(600);
						return;
					}

					window.location.href = wbcr_clearfy_ajax.flush_cache_url;
					// открыть уведомление

				}
			});
		}
	};

	$(document).ready(function() {
		general.init();
	});

})(jQuery);
