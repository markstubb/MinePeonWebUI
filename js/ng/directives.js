'use strict';

/* Directives */
angular.module('Peon.directives', [])

// This directive can probable be constructed better
.directive('statusItem', function() {
	return function(scope, element, attrs) {
		var v=attrs.statusItem; // Value to check low and high against
    var l=attrs.low; // Threshold low: green
    var h=attrs.high; // Threshold high: red

		// Update UI
		function updateValue() {
			if(v>l){
				element.css('background','rgb('+Math.round(200-(h-v)/(h-l)*255)+','+Math.round((h-v)/(h-l)*200)+',0)');
				element.css('color','#fff');
			}
			else{
				element.removeAttr('style');
			}
		}

		// Watch these realtime values
		scope.$watch(attrs.statusItem, function(value) {
			v = value;
			updateValue();
		});

		updateValue();
	}
});