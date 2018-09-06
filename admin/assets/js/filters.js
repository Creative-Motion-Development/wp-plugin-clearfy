/*!
 * Filers & Hooks API
 * Copyright 2018, Webcraftic, http://webcraftic.com
 * 
 * @since 1.0.0
 * @pacakge core
 */
(function($) {
	'use strict';

	if( !$.wbcr_clearfy ) {
		$.wbcr_clearfy = {};
	}
	$.wbcr_clearfy.filters = $.wbcr_clearfy.filters || {

		/**
		 * A set of registered filters.
		 */
		_items: {},

		/**
		 * A set of priorities of registered filters.
		 */
		_priorities: {},

		/**
		 * Applies filters to a given input value.
		 */
		run: function(filterName, args) {
			var input = args && args.length > 0 ? args[0] : null;
			if( !this._items[filterName] ) {
				return input;
			}

			for( var i in this._priorities[filterName] ) {
				if( !this._priorities[filterName].hasOwnProperty(i) ) {
					continue;
				}

				var priority = this._priorities[filterName][i];

				for( var k = 0; k < this._items[filterName][priority].length; k++ ) {
					var f = this._items[filterName][priority][k];
					input = f.apply(f, args);
				}
			}

			return input;
		},

		/**
		 * Registers a new filter.
		 */
		add: function(filterName, callback, priority) {

			if( !priority ) {
				priority = 10;
			}

			if( !this._items[filterName] ) {
				this._items[filterName] = {};
			}
			if( !this._items[filterName][priority] ) {
				this._items[filterName][priority] = [];
			}
			this._items[filterName][priority].push(callback);

			if( !this._priorities[filterName] ) {
				this._priorities[filterName] = [];
			}
			if( $.inArray(priority, this._priorities[filterName]) === -1 ) {
				this._priorities[filterName].push(priority);
			}

			this._priorities[filterName].sort(function(a, b) {
				return a - b;
			});
		}
	};

	$.wbcr_clearfy.hooks = $.wbcr_clearfy.hooks || {

		/**
		 * Applies filters to a given input value.
		 */
		run: function(filterName, args) {
			$.wbcr_clearfy.filters.run(filterName, args);
		},

		/**
		 * Registers a new filter.
		 */
		add: function(filterName, callback, priority) {
			$.wbcr_clearfy.filters.add(filterName, callback, priority);
		}
	};

})(jQuery);