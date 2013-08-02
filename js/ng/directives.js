'use strict';

/* Directives */
angular.module('Peon.directives', [])

// This directive can probable be constructed better
.directive('myValue', function() {
	return function(scope, element, attrs) {
		var v=attrs.myValue; // Value to check low and high against
		var s=attrs.myShow; // Shown value, because after filter, value would be unusable
    var l=attrs.low; // Threshold low: green
    var h=attrs.high; // Threshold high: red

    // Replace element
    element.addClass('btn statusValue');
    element.html('<small>'+element.text()+'</small> <span>'+v+'</span>');
    var val=angular.element(element.children()[1]);

    // Add unit
    if(attrs.unit){
    	element.append(' <small>'+attrs.unit+'</small>');
    }

		// Update UI
		function updateTime() {
			if(v>l){
				element.css('background','rgb('+Math.round(200-(h-v)/(h-l)*255)+','+Math.round((h-v)/(h-l)*200)+',0)');
			}
			else{
				element.removeAttr('style');
			}
			val.text(s);
		}

		// Watch these realtime values
		scope.$watch(attrs.myValue, function(value) {
			v = value;
			updateTime();
		});
		scope.$watch(attrs.myShow, function(value) {
			s = value;
			updateTime();
		});

		updateTime();
	}
});