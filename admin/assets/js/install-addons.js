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

			$('.wbcr-clr-update-external-addon').click(function() {
				var $this = $(this),
					plugin_slug = $(this).data('plugin-slug'),
					plugin_action = $(this).data('plugin-action'),
					button_i18n = $(this).data('i18n'),
					wpnonce = $(this).data('wpnonce');

				var action = 'install-plugin';

				if( plugin_action == 'activate' || plugin_action == 'deactivate' ) {
					action = 'wbcr-clearfy-update-external-addon';
				}

				var data = {
					action: action,
					slug: plugin_slug,
					plugin_action: plugin_action,
					_wpnonce: wpnonce
				};

				if( plugin_action == 'install' ) {
					$this.addClass('updating-message');
				}
				$this.addClass('disabled').text(button_i18n.loading);

				self.sendRequest(data, function(response) {
					if( response.success ) {
						$this.removeClass('disabled').removeClass('updating-message');

						if( plugin_action == 'install' ) {

							plugin_action = 'activate';
							$this.data('plugin-action', 'activate');
							$this.attr('data-plugin-action', 'activate');

						} else if( plugin_action == 'activate' ) {

							plugin_action = 'deactivate';
							$this.data('plugin-action', 'deactivate');
							$this.attr('data-plugin-action', 'deactivate');

							// If the button is installed inside the notification,
							// then delete the button container after activating the component

							if( $this.closest('.wbcr-clr-new-component').lenght ) {
								$this.closest('.wbcr-clr-new-component').remove();
							}

							// If the button is installed on the components page,
							// the active and inactive components are highlighted

							if( $this.closest('.plugin-card').length ) {
								$this.closest('.plugin-card').removeClass('plugin-status-deactive');
							}

						} else if( plugin_action == 'deactivate' ) {

							plugin_action = 'activate';
							$this.data('plugin-action', 'activate');
							$this.attr('data-plugin-action', 'activate');

							// If the button is installed on the components page,
							// the active and inactive components are highlighted

							if( $this.closest('.plugin-card').length ) {
								$this.closest('.plugin-card').addClass('plugin-status-deactive');
							}
						}
					} else {
						console.log(response.data.errorMessage);

						if( plugin_action == 'install' ) {
							$this.removeClass('updating-message');
						}
					}

					$this.text(button_i18n[plugin_action]);

					console.log(response);
				});

				return false;
			});

			$('.wbcr-clr-activate-preload-addon').click(function() {
				var $this = $(this);
				var plugin_slug = $(this).data('component-name');
				var wpnonce = $(this).data('wpnonce');

				var data = {
					action: 'wbcr-clearfy-activate-preload-addon',
					component_name: plugin_slug,
					_wpnonce: wpnonce
				};

				$this.addClass('disabled').text('Идет активация...');

				self.sendRequest(data, function(response) {
					if( response.success ) {
						$this.closest('.wbcr-clr-new-component').remove();
					} else {
						console.log(response.data.errorMessage);
						$this.removeClass('disabled').text('Активировать');
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
				}
			});
		}
	};

	$(document).ready(function() {
		externalAddon.init();
	});

})(jQuery);
