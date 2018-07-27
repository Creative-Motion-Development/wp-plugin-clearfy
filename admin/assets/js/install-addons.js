/**
 * This code provides tools for downloading, installing external add-ons for the Clearfy plugin
 *
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 10.09.2017, Webcraftic
 * @version 1.0
 */


(function($) {
	'use strict';

	var externalAddon = {
		init: function() {
			this.events();
		},
		events: function() {
			var self = this;

			/**
			 * This event is intended for installation, removal, activation, deactivation of external add-ons
			 */

			$(document).on('click', '.wbcr-clr-update-component-button', function() {
				var $this = $(this),
					button_i18n = $(this).data('i18n'),
					plugin_slug = $(this).data('slug'),
					plugin_action = $(this).data('plugin-action'),
					plugin = $(this).data('plugin'),
					storage = $(this).data('storage'),
					wpnonce = $(this).data('wpnonce');

				var action = 'install-plugin';

				if( storage == 'freemius' || ((storage == 'wordpress' || storage == 'internal') && (plugin_action == 'activate' || plugin_action == 'deactivate')) ) {
					action = 'wbcr-clearfy-update-component';
				} else if( storage == 'wordpress' && plugin_action == 'delete' ) {
					action = 'delete-plugin';
				}

				var data = {
					action: action,
					slug: plugin_slug,
					storage: storage,
					plugin: plugin,
					plugin_action: plugin_action,
					_wpnonce: wpnonce
				};

				if( plugin_action == 'install' ) {
					$this.addClass('updating-message');
				}
				$this.addClass('disabled').text(button_i18n.loading);

				self.sendRequest(data, function(response) {
					console.log(response);

					if( response.success ) {
						$this.removeClass('disabled').removeClass('updating-message');
						
						if ( response.data.updateNotice ) {
							if ( ! $('.wbcr-clr-update-package').length ) {
								$('.wbcr-factory-content').prepend(
								 '<div class="alert alert-warning wbcr-factory-warning-notice">\
									<p>\
									<span class="dashicons dashicons-warning"></span>\
									'+response.data.updateNotice+'\
									</p>\
								</div>\
								'); 
							}
						} else {
							if ( $('.wbcr-clr-update-package').length ) {
								$('.wbcr-clr-update-package').closest( '.wbcr-factory-warning-notice' ).remove();
							}
						}

						if( plugin_action == 'install' ) {

							plugin_action = 'activate';
							$this.data('plugin-action', 'activate');
							$this.attr('data-plugin-action', 'activate');
							$this.removeClass('button-default').addClass('button-primary');

						} else if( plugin_action == 'activate' ) {

							plugin_action = 'deactivate';
							$this.data('plugin-action', 'deactivate');
							$this.attr('data-plugin-action', 'deactivate');
							$this.removeClass('button-primary').addClass('button-default');

							// If the button is installed inside the notification,
							// then delete the button container after activating the component

							if( $this.closest('.wbcr-clr-new-component').length ) {
								$this.closest('.wbcr-clr-new-component').remove();
							}

							// If the button is installed on the components page,
							// the active and inactive components are highlighted

							if( $this.closest('.plugin-card').length ) {
								$this.closest('.plugin-card').removeClass('plugin-status-deactive');
								$this.closest('.plugin-card').find('.delete-now').remove();
							}

						} else if( plugin_action == 'deactivate' ) {

							plugin_action = 'activate';
							$this.data('plugin-action', 'activate');
							$this.attr('data-plugin-action', 'activate');
							$this.removeClass('button-default').addClass('button-primary');

							// If the button is installed on the components page,
							// the active and inactive components are highlighted

							if( $this.closest('.plugin-card').length ) {
								$this.closest('.plugin-card').addClass('plugin-status-deactive');

								if( response.data['delete_button'] && response.data['delete_button'] != '' ) {
									$this.before($(response.data['delete_button']).addClass('delete-now'));
								}
							}
						} else if( plugin_action == 'delete' ) {

							plugin_action = 'install';
							$this.closest('.plugin-card').find('.install-now').data('plugin-action', 'install');
							$this.closest('.plugin-card').find('.install-now').attr('data-plugin-action', 'install');
							$this.closest('.plugin-card').find('.install-now').removeClass('button-primary').addClass('button-default');
							$this.closest('.plugin-card').find('.install-now').text(button_i18n.install);

							// If the button is installed on the components page,
							// the active and inactive components are highlighted

							if( $this.closest('.plugin-card').length ) {
								$this.closest('.plugin-card').addClass('plugin-status-deactive');
								$this.remove();
							}
						}
					} else {
						console.log(response.data.errorMessage);

						if( plugin_action == 'install' ) {
							$this.removeClass('updating-message');
						}
					}

					$this.text(button_i18n[plugin_action]);
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
				}
			});
		}
	};

	$(document).ready(function() {
		externalAddon.init();
	});

})(jQuery);
