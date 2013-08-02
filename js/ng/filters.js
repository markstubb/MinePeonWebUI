'use strict';

/* Filters */

angular.module('Peon.filters', [])
.filter('fromNow', function() {
	return function(date) {
		return moment(date).fromNow();
	}
})
.filter('percent', function() {
	return function(number) {
		return Math.round(100*number) + " %";
	}
})
.filter('temp', function() {
	return function(temp) {
		return temp + " °C";
	}
});
