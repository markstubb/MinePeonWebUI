'use strict';

/* Filters */

angular.module('Peon.filters', [])
.filter('fromNow', function() {
	return function(date) {
		return moment(date).fromNow();
	}
})
.filter('shortUrl', function() {
	return function(temp) {
		return temp.replace('//', '').split(':')[1];
	}
})
.filter('mhs', function() {
	return function(mhs) {
		if(mhs<1000){
			return mhs;
		}
	}
})
.filter('fromStamp', function() {
	return function(timestamp) {
		return new Date(timestamp*1000);
	}
});