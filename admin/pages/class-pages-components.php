<?php
/**
 * This file is the add-ons page.
 *
 * @author        Alex Kovalev <alex@byonepress.com>
 * @since         1.0.0
 * @copyright (c) 2017, OnePress Ltd
 *
 */

// Exit if accessed directly
if( !defined('ABSPATH') ) {
	exit;
}

class WCL_ComponentsPage extends \WBCR\FactoryClearfy000\Pages\Components {

	public function get_components()
	{
		$default_image = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzNjAiIGhlaWdodD0iMzYwIiB2aWV3Ym94PSIwIDAgMzYwIDM2MCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0icmdiKDcwLCA4MSwgOTMpIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjE1IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLCAwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wNTQ2NjY2NjY2NjY2NjciIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDYwLCAwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4wNDYiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEyMCwgMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDIiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDU0NjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgyNDAsIDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjAyODY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAwLCAwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4xMDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCwgNjApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA5OCIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDYwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMTUzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTIwLCA2MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDYzMzMzMzMzMzMzMzMzIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxODAsIDYwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wMzczMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI0MCwgNjApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iI2RkZCIgZmlsbC1vcGFjaXR5PSIwLjE0MTMzMzMzMzMzMzMzIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzMDAsIDYwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wMzczMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAsIDEyMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDg5MzMzMzMzMzMzMzMzIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg2MCwgMTIwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wODkzMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEyMCwgMTIwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4wODA2NjY2NjY2NjY2NjciIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMTIwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMzI2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjQwLCAxMjApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjE1IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzMDAsIDEyMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDk4IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLCAxODApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA2MzMzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDE4MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDIiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEyMCwgMTgwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4wMzczMzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMTgwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMTUzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjQwLCAxODApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA2MzMzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAwLCAxODApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iI2RkZCIgZmlsbC1vcGFjaXR5PSIwLjA1NDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCwgMjQwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiNkZGQiIGZpbGwtb3BhY2l0eT0iMC4xMDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDI0MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDcyIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMjAsIDI0MCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMTE1MzMzMzMzMzMzMzMiIHN0cm9rZT0iIzAwMCIgc3Ryb2tlLW9wYWNpdHk9IjAuMDIiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE4MCwgMjQwKSIgLz48cG9seWxpbmUgcG9pbnRzPSIxOS44LDAsNDAuMiwwLDYwLDE5LjgsNjAsNDAuMiw0MC4yLDYwLDE5LjgsNjAsMCw0MC4yLDAsMTkuOCwxOS44LDAiIGZpbGw9IiMyMjIiIGZpbGwtb3BhY2l0eT0iMC4xMzI2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjQwLCAyNDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjA4MDY2NjY2NjY2NjY2NyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAwLCAyNDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iIzIyMiIgZmlsbC1vcGFjaXR5PSIwLjEzMjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLCAzMDApIiAvPjxwb2x5bGluZSBwb2ludHM9IjE5LjgsMCw0MC4yLDAsNjAsMTkuOCw2MCw0MC4yLDQwLjIsNjAsMTkuOCw2MCwwLDQwLjIsMCwxOS44LDE5LjgsMCIgZmlsbD0iI2RkZCIgZmlsbC1vcGFjaXR5PSIwLjAzNzMzMzMzMzMzMzMzMyIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utb3BhY2l0eT0iMC4wMiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNjAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMTI0IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMjAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDI4NjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxODAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjZGRkIiBmaWxsLW9wYWNpdHk9IjAuMDcyIiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgyNDAsIDMwMCkiIC8+PHBvbHlsaW5lIHBvaW50cz0iMTkuOCwwLDQwLjIsMCw2MCwxOS44LDYwLDQwLjIsNDAuMiw2MCwxOS44LDYwLDAsNDAuMiwwLDE5LjgsMTkuOCwwIiBmaWxsPSIjMjIyIiBmaWxsLW9wYWNpdHk9IjAuMDI4NjY2NjY2NjY2NjY3IiBzdHJva2U9IiMwMDAiIHN0cm9rZS1vcGFjaXR5PSIwLjAyIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgzMDAsIDMwMCkiIC8+PC9zdmc+';

		$components = [];
		$components = array_merge($components, [
			[
				'name' => 'cache',
				'title' => __('Cache', 'clearfy'),
				'type' => 'internal',
				'build' => 'premium',
				'disable_button' => is_plugin_active('wp-rocket/wp-rocket.php'),
				'disable_button_reason' => __('You cannot activate the component while the wp rocket plugin is active!', 'clearfy'),
				'url' => 'https://clearfy.pro/cache/',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/cache-icon-128x128.png',
				'description' => __('A very fast caching engine for WordPress that produces static html files.', 'clearfy')
			],
			[
				'name' => 'hide_my_wp',
				'title' => __('Hide my wp', 'clearfy'),
				'type' => 'internal',
				'build' => 'premium',
				'url' => 'http://clearfy.pro/hide-my-wp/',
				'icon' => '//s3-us-west-2.amazonaws.com/freemius/plugins/2318/icons/db36219969de82e3d07042cc03eb53b0.png',
				'description' => __('You can protect your WP by preventing the hacker from knowing which CMS, plugins, themes you use. It disables identification of your CMS.', 'clearfy')
			],
			[
				'name' => 'seo_friendly_images',
				'title' => __('Seo friendly images', 'clearfy'),
				'type' => 'internal',
				'build' => 'premium',
				'url' => 'https://clearfy.pro/',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/sfi-icon-256x256.png',
				'description' => __('Automatically assign alt and title for images, flexibly customize the template.', 'clearfy')
			],
			[
				'name' => 'async_js',
				'title' => __('Async JS', 'clearfy'),
				'type' => 'internal',
				'build' => 'premium',
				'url' => 'https://clearfy.pro/',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/async-js-icon-128x128.png',
				'description' => __('Async Javascript lets you add async or defer attribute to scripts to exclude to help increase the performance of your WordPres…', 'clearfy')
			],
			[
				'name' => 'robin_image_optimizer',
				'title' => __('Robin image optimizer', 'clearfy'),
				'url' => 'https://wordpress.org/plugins/robin-image-optimizer/',
				'type' => 'wordpress',
				'build' => 'freemium',
				'base_path' => 'robin-image-optimizer/robin-image-optimizer.php',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/rio-icon-128x128.png',
				'description' => __('Automatic image optimization without any quality loss. No limitations, no paid plans. The best Wordpress image optimization plugin allows optimizing any amount of images for free!', 'clearfy')
			],
			[
				'name' => 'titan_security',
				'title' => __('Firewall and Malware scanner', 'clearfy'),
				'url' => 'https://wordpress.org/plugins/titan-security/',
				'type' => 'creativemotion',
				'build' => 'freemium',
				'base_path' => 'anti-spam/anti-spam.php',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/titan-icon-256x256.jpg',
				'description' => __('Titan Security - Anti-virus, Firewall and Malware Scan', 'clearfy')
			],
			[
				'name' => 'hide_login_page',
				'title' => __('Hide login page', 'clearfy'),
				'url' => 'https://wordpress.org/plugins/hide-login-page/',
				'type' => 'wordpress',
				'build' => 'free',
				'base_path' => 'hide-login-page/hide-login-page.php',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/hlp-icon-128x128.png',
				'description' => __('Hide Login Page is a very light plugin that lets you easily and safely change the url of the login form page to anything you want.', 'clearfy')
			],
			[
				'name' => 'html_minify',
				'title' => __('Html minify', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'free',
				'icon' => $default_image,
				'description' => __('Ever look at the HTML markup of your website and notice how sloppy and amateurish it looks? The Minify HTML options cleans up sloppy looking markup and minifies, which also speeds up download', 'clearfy')
			],
			[
				'name' => 'minify_and_combine',
				'title' => __('Minify and combine (JS, CSS)', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'free',
				'icon' => $default_image,
				'description' => __('Improve your speed score on GTmetrix, Pingdom Tools and Google PageSpeed Insights by merging and minifying CSS, JavaScript.', 'clearfy')
			],
			[
				'name' => 'ga_cache',
				'title' => __('Google Analytics Cache', 'clearfy'),
				'url' => 'https://wordpress.org/plugins/simple-google-analytics/',
				'type' => 'internal',
				'build' => 'free',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/gac-icon-128x128.jpg',
				'description' => __('To improve Google Page Speed indicators Analytics caching is needed. However, it can also slightly increase your website loading speed, because Analytics js files will load locally.', 'clearfy')
			],
			[
				'name' => 'updates_manager',
				'title' => __('Updates manager', 'clearfy'),
				'url' => 'https://wordpress.org/plugins/webcraftic-updates-manager/',
				'type' => 'internal',
				'build' => 'freemium',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/upm-icon-128x128.png',
				'description' => __('Disable updates enable auto updates for themes, plugins and WordPress.', 'clearfy')
			],
			[
				'name' => 'comments_tools',
				'title' => __('Comments tools', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'free',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/dic-icon-128x128.png',
				'description' => __('Bulk disable and remove comments, disable “Website” field, hides external links, disable XML-RPC.', 'clearfy')
			],
			[
				'name' => 'widget_tools',
				'title' => __('Widgets tools', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'free',
				'icon' => $default_image,
				'description' => __('Disable unused widgets such as tag cloud, links, calendar etc.', 'clearfy')
			],
			[
				'name' => 'disable_notices',
				'title' => __('Disable admin notices', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'free',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/dan-icon-128x128.png',
				'description' => __('Disables admin notices bulk or individually. Collects notices into the admin bar.', 'clearfy')
			],
			[
				'name' => 'adminbar_manager',
				'title' => __('Admin bar manager', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'free',
				'icon' => $default_image,
				'description' => __('Disables admin bar. Allows to change and remove admin bar elements.', 'clearfy')
			],
			[
				'name' => 'yoast_seo',
				'title' => __('Yoast SEO optimization', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'free',
				'icon' => $default_image,
				'description' => __('Set of optimization functions for the popular Yoast SEO plugin.', 'clearfy')
			]
		]);

		if( !is_plugin_active('gonzales/gonzales.php') ) {
			array_unshift($components, [
				'name' => 'assets_manager',
				'title' => __('Asset manager', 'clearfy'),
				'url' => '#',
				'type' => 'internal',
				'build' => 'freemium',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/asm-icon-128x128.png',
				'description' => __('Selectively disable unused scripts and styles on the pages of your website.', 'clearfy')
			]);
		} else {
			array_unshift($components, [
				'name' => 'gonzales',
				'title' => __('Asset manager', 'clearfy'),
				'url' => 'https://wordpress.org/plugins/robin-image-optimizer/',
				'type' => 'wordpress',
				'build' => 'freemium',
				'base_path' => 'gonzales/gonzales.php',
				'icon' => WCL_PLUGIN_URL . '/admin/assets/img/asm-icon-128x128.png',
				'description' => __('Selectively disable unused scripts and styles on the pages of your website.', 'clearfy')
			]);
		}

		$components[] = [
			'name' => 'cyrlitera',
			'title' => __('Transliteration of Cyrillic alphabet', 'clearfy'),
			'type' => 'internal',
			'build' => 'free',
			'url' => 'https://wordpress.org/plugins/cyrlitera/',
			'icon' => WCL_PLUGIN_URL . '/admin/assets/img/ctr-icon-128x128.png',
			'description' => __('Converts Cyrillic permalinks of post, pages, taxonomies and media files to the Latin alphabet. Supports Russian, Ukrainian, Georgian, Bulgarian languages.', 'clearfy')
		];

		return $components;
	}
}
