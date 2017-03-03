;(function($){
	var TOUCHSTART, TOUCHEND;
	//normal touch events
	if (typeof(window.ontouchstart) != 'undefined') {
	  TOUCHSTART = 'touchstart';
	  TOUCHMOVE = 'touchmove';
	  TOUCHEND   = 'touchend';
	//microsoft touch events
	} else if (typeof(window.onmspointerdown) != 'undefined') {
	  TOUCHSTART = 'MSPointerDown';
	  TOUCHMOVE = 'MSPointerMove';
	  TOUCHEND   = 'MSPointerUp';
	} else {
	  TOUCHSTART = 'mousedown';
	  TOUCHMOVE = 'mousemove';
	  TOUCHEND   = 'mouseup';
	}

	$.fn.extend({
		tap:function(callback,context){
			this.on(TOUCHSTART,$.proxy(callback,context));
			return this;
		},
		tapmove:function(callback,context){
			this.on(TOUCHMOVE,$.proxy(callback,context));
			return this;
		},
		tapend:function(callback,context){
			this.on(TOUCHEND,$.proxy(callback,context));
			return this;
		},
		offTap:function(callback,context){
			this.off(TOUCHSTART);
		}
	});

})(jQuery);

//on、off
