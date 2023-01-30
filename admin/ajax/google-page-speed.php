<?php
/**
 * Ajax plugin configuration
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 2017 Webraftic Ltd
 * @version 1.0
 */

//AIzaSyD85-8Tmp_Ixc43AgqyeLpNZNlGP150LbA

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

add_action('wp_ajax_wclearfy-fetch-google-pagespeed-audit', function () {
	check_ajax_referer('fetch_google_page_speed_audit');

	if( !WCL_Plugin::app()->currentUserCan() ) {
		wp_die(-1);
	}

	$flush_cache = (bool)WCL_Plugin::app()->request->post('flush_cache', false);

	$results = get_transient(WCL_Plugin::app()->getPrefix() . 'fetch_google_page_speed_audits');

	if( !empty($results) ) {
		if( $flush_cache ) {
			delete_transient(WCL_Plugin::app()->getPrefix() . 'fetch_google_page_speed_audits');
		} else {
			wp_send_json_success($results);
		}
	}

	$site_url = get_home_url();

	// Check if plugin is installed in localhost
	if( substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.' || $_SERVER['REMOTE_ADDR'] == '::1' ) {
		$site_url = 'https://cm-wp.com/';
	}

	$results = [];
	$strategy_arr = array(1 => 'desktop', 2 => 'mobile');

	foreach($strategy_arr as $strategy_id => $strategy_text) {
		$google_page_speed_call = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=" . $site_url . "&key=AIzaSyD85-8Tmp_Ixc43AgqyeLpNZNlGP150LbA&strategy=" . $strategy_text;

		//Fetch data from Google PageSpeed API
		$response = wp_remote_get($google_page_speed_call, array('timeout' => 30));
		$response_code = wp_remote_retrieve_response_code($response);

		$response_error = null;
		if( is_wp_error($response) ) {
			$response_error = $response;
		} elseif( 200 !== $response_code ) {
			$response_error = new WP_Error('api-error', /* translators: %d: Numeric HTTP status code, e.g. 400, 403, 500, 504, etc. */ sprintf(__('Invalid API response code (%d).'), $response_code));
		}

		if( is_wp_error($response_error) ) {
			wp_send_json_error([
				'error' => $response_error->get_error_message(),
				'code' => $response_error->get_error_code()
			]);
		}

		$google_ps = json_decode($response['body'], true);

		if( isset($google_ps['error']) ) {
			wp_send_json_error([
				'error' => $google_ps['error']['message'],
				'code' => $google_ps['error']['code']
			]);
		}

		$results[$strategy_text] = [
			'performance_score' => ($google_ps['lighthouseResult']['categories']['performance']['score'] * 100),
			'first_contentful_paint' => $google_ps['lighthouseResult']['audits']['first-contentful-paint']['displayValue'],
			'speed_index' => $google_ps['lighthouseResult']['audits']['speed-index']['displayValue'],
			'interactive' => $google_ps['lighthouseResult']['audits']['interactive']['displayValue']
		];

		set_transient(WCL_Plugin::app()->getPrefix() . 'fetch_google_page_speed_audits', $results, 201);
	}
	wp_send_json_success($results);
});

add_action('wp_ajax_wclearfy-google-pagespeed-audit-results', function () {
	$get_before_audit_results = WCL_Plugin::app()->getPopulateOption('google_page_speed_audit_before');
	$get_after_audit_results = WCL_Plugin::app()->getPopulateOption('google_page_speed_audit_after');

	$results = [
		'before' => !empty($get_before_audit_results) ? $get_before_audit_results : [
			'fake' => 1,
			'desktop' => [
				'performance_score' => 0,
				'first_contentful_paint' => '??',
				'speed_index' => '??',
				'interactive' => '??'
			],
			'mobile' => [
				'performance_score' => 0,
				'first_contentful_paint' => '??',
				'speed_index' => '??',
				'interactive' => '??'
			],
		],
		'after' => !empty($get_after_audit_results) ? $get_after_audit_results : [
			'fake' => 1,
			'desktop' => [
				'performance_score' => 0,
				'first_contentful_paint' => '??',
				'speed_index' => '??',
				'interactive' => '??'
			],
			'mobile' => [
				'performance_score' => 0,
				'first_contentful_paint' => '??',
				'speed_index' => '??',
				'interactive' => '??'
			]
		]
	];
	wp_send_json_success($results);
});

