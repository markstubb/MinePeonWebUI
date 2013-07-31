'use strict';

/* Filters */

angular.module('Peon.filters', [])
.filter('fromNow', function() {
	return function(date) {
		return moment(date).fromNow();
	}
});
