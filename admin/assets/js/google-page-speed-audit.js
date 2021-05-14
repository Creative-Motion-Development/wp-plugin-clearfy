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
			action: 'wclearfy-google-pagespeed-audit-results',
			flush_cache: flush_cache,
			_wpnonce: nonce,
		};

		$.ajax(ajaxurl, {
			type: 'post',
			dataType: 'json',
			data: data,
			success: function(response) {
				console.log(response);

				if( !response || !response.success ) {
					if( response.data ) {
						console.log(response.data.error);
						$('.wclearfy-gogle-page-speed-audit__errors').text(response.data.error).show();
					} else {
						console.log(response);
					}
					return;
				}

				//$('.wclearfy-quick-start__google-page-speed-audit').show();

				if( response.data ) {
					if( response.data.before ) {
						if( !response.data.before.fake ) {
							$('.wclearfy-quick-start__g-audit-overlay', '#wclearfy-quick-start__g-audit-before').hide();
							$('.wclearfy-quick-start__g-audit-preloader', '#wclearfy-quick-start__g-audit-before').hide();
						} //else {
							//$('#wclearfy-quick-start__g-audit-warging-text-1').show();
							//$('.wclearfy-quick-start__g-audit-preloader', '#wclearfy-quick-start__g-audit-after').hide();
						//}

						$('.wclearfy-quick-start__g-audit-desktop-score-circle', '#wclearfy-quick-start__g-audit-before').wfCircularProgress({
							endPercent: (response.data.before.desktop.performance_score / 100),
							color: get_color(response.data.before.desktop.performance_score),
							inactiveColor: '#ececec',
							strokeWidth: 3,
							diameter: 100,
						});

						$('.wclearfy-quick-start__g-audit-mobile-score-circle', '#wclearfy-quick-start__g-audit-before').wfCircularProgress({
							endPercent: (response.data.before.mobile.performance_score / 100),
							color: get_color(response.data.before.mobile.performance_score),
							inactiveColor: '#ececec',
							strokeWidth: 3,
							diameter: 100,
						});

						// DESKTOP
						$('.wclearfy-quick-start__g-audit-statistic--desktop-first-contentful-paint', '#wclearfy-quick-start__g-audit-before')
							.text(response.data.before.desktop.performance_score);
						$('.wclearfy-quick-start__g-audit-statistic--desktop-speed-index', '#wclearfy-quick-start__g-audit-before')
							.text(response.data.before.desktop.speed_index);
						$('.wclearfy-quick-start__g-audit-statistic--desktop-interactive', '#wclearfy-quick-start__g-audit-before')
							.text(response.data.before.desktop.interactive);

						// MOBILE
						$('.wclearfy-quick-start__g-audit-statistic--mobile-first-contentful-paint', '#wclearfy-quick-start__g-audit-before')
							.text(response.data.before.mobile.performance_score);
						$('.wclearfy-quick-start__g-audit-statistic--mobile-speed-index', '#wclearfy-quick-start__g-audit-before')
							.text(response.data.before.mobile.speed_index);
						$('.wclearfy-quick-start__g-audit-statistic--mobile-interactive', '#wclearfy-quick-start__g-audit-before')
							.text(response.data.before.mobile.interactive);
					}
					if( response.data.after ) {
						if( !response.data.after.fake ) {
							$('.wclearfy-quick-start__g-audit-overlay', '#wclearfy-quick-start__g-audit-after').hide();
							$('.wclearfy-quick-start__g-audit-preloader', '#wclearfy-quick-start__g-audit-after').hide();
						} //else {
							//$('#wclearfy-quick-start__g-audit-warging-text-2').show();
							//$('.wclearfy-quick-start__g-audit-preloader', '#wclearfy-quick-start__g-audit-after').hide();
						//}

						$('.wclearfy-quick-start__g-audit-desktop-score-circle', '#wclearfy-quick-start__g-audit-after').wfCircularProgress({
							endPercent: (response.data.after.desktop.performance_score / 100),
							color: get_color(response.data.after.desktop.performance_score),
							inactiveColor: '#ececec',
							strokeWidth: 3,
							diameter: 100,
						});

						$('.wclearfy-quick-start__g-audit-mobile-score-circle', '#wclearfy-quick-start__g-audit-after').wfCircularProgress({
							endPercent: (response.data.after.mobile.performance_score / 100),
							color: get_color(response.data.after.mobile.performance_score),
							inactiveColor: '#ececec',
							strokeWidth: 3,
							diameter: 100,
						});

						// DESKTOP
						$('.wclearfy-quick-start__g-audit-statistic--desktop-first-contentful-paint', '#wclearfy-quick-start__g-audit-after')
							.text(response.data.after.desktop.performance_score);
						$('.wclearfy-quick-start__g-audit-statistic--desktop-speed-index', '#wclearfy-quick-start__g-audit-after')
							.text(response.data.after.desktop.speed_index);
						$('.wclearfy-quick-start__g-audit-statistic--desktop-interactive', '#wclearfy-quick-start__g-audit-after')
							.text(response.data.after.desktop.interactive);
						// MOBILE
						$('.wclearfy-quick-start__g-audit-statistic--mobile-first-contentful-paint', '#wclearfy-quick-start__g-audit-after')
							.text(response.data.after.mobile.performance_score);
						$('.wclearfy-quick-start__g-audit-statistic--mobile-speed-index', '#wclearfy-quick-start__g-audit-after')
							.text(response.data.after.mobile.speed_index);
						$('.wclearfy-quick-start__g-audit-statistic--mobile-interactive', '#wclearfy-quick-start__g-audit-after')
							.text(response.data.after.mobile.interactive);
					}
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
