/**
 * Setup master
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 12.08.2020, Webcraftic
 * @version 1.0
 */

(function($) {
	'use strict';

	window.wclearfy_fetch_google_pagespeed_audit = function(nonce, flush_cache) {

		let data = {
			action: 'wclearfy-fetch-google-pagespeed-audit',
			flush_cache: flush_cache,
			_wpnonce: nonce,
		};

		$.ajax(ajaxurl, {
			type: 'post',
			dataType: 'json',
			data: data,
			success: function(response) {
				console.log(response);

				$('.wclearfy-gogle-page-speed-audit__preloader').hide();

				if( !response || !response.success ) {
					if( response.data ) {
						console.log(response.data.error);
						$('.wclearfy-gogle-page-speed-audit__errors').text(response.data.error).show();
					} else {
						console.log(response);
					}
					return;
				}

				$('.wclearfy-gogle-page-speed-audit').show();

				if( response.data && response.data.desktop ) {
					$('#wclearfy-desktop-score__circle').wfCircularProgress({
						endPercent: (response.data.desktop.performance_score / 100),
						color: get_color(response.data.desktop.performance_score),
						inactiveColor: '#ececec',
						strokeWidth: 5,
						diameter: 150,
					});

					$('#wclearfy-statistic__desktop-first-contentful-paint').text(response.data.desktop.performance_score);
					$('#wclearfy-statistic__desktop-speed-index').text(response.data.desktop.speed_index);
					$('#wclearfy-statistic__desktop-interactive').text(response.data.desktop.interactive);
				}

				if( response.data && response.data.mobile ) {
					$('#wclearfy-mobile-score__circle').wfCircularProgress({
						endPercent: (response.data.mobile.performance_score / 100),
						color: get_color(response.data.mobile.performance_score),
						inactiveColor: '#ececec',
						strokeWidth: 5,
						diameter: 150,
					});

					$('#wclearfy-statistic__mobile-first-contentful-paint').text(response.data.desktop.performance_score);
					$('#wclearfy-statistic__mobile-speed-index').text(response.data.desktop.speed_index);
					$('#wclearfy-statistic__mobile-interactive').text(response.data.desktop.interactive);
				}

			},
			error: function(xhr, ajaxOptions, thrownError) {

				$('.wclearfy-gogle-page-speed-audit__preloader').hide();

				console.log(xhr.status);
				console.log(xhr.responseText);
				console.log(thrownError);

				$('.wclearfy-gogle-page-speed-audit__errors').text('Status: ' + xhr.status + 'Error:' + xhr.responseText).show();
			}
		});

		function get_color(score) {
			let desktopColor;

			if( score > 70 ) {
				desktopColor = '#a8d207';
			} else if( score > 40 ) {
				desktopColor = '#f18727';
			} else {
				desktopColor = '#cd2727';
			}

			return desktopColor;
		}
	}
})(jQuery);
