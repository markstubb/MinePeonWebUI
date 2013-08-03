'use strict';

/* Directives */
angular.module('Peon.directives', [])

// Sets background color by interpolating between green and red.
// Thinking about oth interpolation functions or maybe more colors
.directive('statusItem', function() {
	return function(scope, element, attrs) {
		var i=attrs.statusItem;
		var g=attrs.good; // Threshold good: green
		var b=attrs.bad; // Threshold bad: red

		function update(){
			var x=2*(i-g)/(b-g);
			element.css('background',(b==g)?'#666':'rgb('+Math.round(Math.min(x, 1)*200)+','+Math.round((2 - Math.max(x, 1)) * 150)+',0)');
			element.css('color','#fff');
		}

		scope.$watch(attrs.good,       function(v) {g=v;update();});
		scope.$watch(attrs.bad,        function(v) {b=v;update();});
		scope.$watch(attrs.statusItem, function(v) {i=v;update();});
	}
})
// Toggles .active based on $location.path()
.directive('menuActive', function($rootScope,$location) {
	return function(scope, element, attrs) {
		$rootScope.$on("$routeChangeStart", function (event, next, current) {
			(element.children()[0].hash === "#"+$location.path()) ? element.addClass("active") : element.removeClass("active");
		});
	}
});