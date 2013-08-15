/*
 *  Project: Tinyslider v0.7
 *  Description: An image carousel with a small footprint.
 *
 *  Author: Cameron Condry <ccondry2@gmail.com>
 *  Copyright: 2013, Cameron Condry
 *  License: http://www.opensource.org/licenses/mit-license.php
 *  Dependencies:
 *  	jQuery <http://code.jquery.com/jquery.js>
 */

;(function ($, window, undefined) {

	// Create the defaults for the
	var pluginName = 'tinyslider',
		document = window.document,
		defaults = {
			debug: false,			// Enables debugging messages
			animate: true,			// Enables automatic transitions
			animate_wait: 0,		// Time to wait once an animation is issued
			controls: false,		// Enables controls for the slider
			duration: 1000,			// Time for animation to complete
			forward: true,			// Enables the default direction for automatic transitions
			pause_hover: true,		// Pauses the animation when hovering over slider
			infinite: false,		// Enabled infinite carousel
			interval: 2000,			// Time to wait between each animation

			// Available callbacks
			start: null,			// Function callback issued before the animation begins
			complete: null,			// Function callback issued after the animation ends

			// Element names
			viewport: 'viewport',		// Element class on the viewport
			overview: 'overview'		// Element class on the overview slides
		};

	// Plugin constructor
	function Plugin(element, options) {
		var tiny = this;

		tiny.element = $(element);
		tiny.options = $.extend({}, defaults, options);
		tiny.options = $.extend({}, tiny.options, {
			viewport: 		$('.' + tiny.options.viewport + ':first', element),
			overview: 		$('.' + tiny.options.overview + ':first', element),
			slide_count: 		0,
			slide_current: 		1,
			slide_size: 		0,
			btn_next: 		$('.next:first', element),
			btn_prev: 		$('.prev:first', element),
			pause: 			false,
			animating: 		false,
			timer: 			undefined
		});

		tiny.init();
	}

	Plugin.prototype = {
		init: function () {
			var tiny = this,
				options = tiny.options;

			tiny.debug('Tinyslider Initialized');

			// Save the individual slide information
			options.slide_count = options.overview.children().length;
			options.slide_size = $(options.overview[0]).outerWidth(true);
			options.overview_size = options.slide_count * options.slide_size

			// Line up the images
			options.overview.css({width: options.overview_size});

			// Set the event handlers
			tiny.set_timer();
			tiny.set_events();
			tiny.set_controls();

			tiny.debug(this.options);
		},

		// Animation tinyslider
		move: function (direction) {
			var tiny = this,
				options = tiny.options,
				slides = options.overview.children();

			tiny.debug('Direction: ' + (direction == 1 ? 'Right' : 'Left'));

			// Exit if an animation is already running
			if (options.animating) {
				tiny.debug('Waiting for animation to complete');
				return false;
			}

			options.animating = true;

			// Update current slide state
			options.slide_current += direction;
			options.slide_current = (options.slide_current >= 1) ? options.slide_current : options.slide_count;
			options.slide_current = (options.slide_current <= options.slide_count) ? options.slide_current : 1;

			// Callback before the animation begins
			var start = function () {
				if (typeof options.start === 'function') {
					tiny.debug('Start callback executing');
					options.start.call(tiny, options.slide_current, slides);
				}
			};

			// Callback after the animation completes
			var complete = function () {
				options.animating = false;

				if (typeof options.complete === 'function') {
					tiny.debug('Complete callback executing');
					options.complete.call(tiny, options.slide_current, slides);
				}
			};

			start();

			// Time to wait before starting animation
			setTimeout(function () {
				// Determine if the carousel is infinite
				if (!options.infinite) {
					options.overview.animate({
						left: -((options.slide_current - 1) * options.slide_size)
					},{
						duration: options.duration,
						complete: complete()
					});
				} else {

					var left = (direction < 0) ? 0 : -options.slide_size;

					// Rotate the last slide to the beginning
					if (direction < 0) {
						slides.last().css({
							marginLeft: -options.slide_size
						}).insertBefore(slides.first());

						slides = options.overview.children();
					}

					slides.first().animate({
						marginLeft: left
					},{
						duration: options.duration,
						complete: function () {
							// Rotate the first slide to the end
							if (direction > 0) {
								slides.first().css({
									marginLeft: 0
								}).insertAfter(slides.last());
							}

							complete();
						}
					});
				}

				tiny.set_timer();

			}, options.animate_wait);

			tiny.set_controls();

			tiny.debug('Page: ' + options.slide_current + '/' + options.slide_count);
		},

		// Force start the animations
		force_start: function (tiny) {
			var options = tiny.options;

			tiny.debug('Restarting animation');

			options.pause = false;
			tiny.set_timer();
		},

		// Force stop the animations
		force_stop: function (tiny) {
			var options = tiny.options;

			tiny.debug('Pausing animation');

			clearTimeout(options.timer);
			options.pause = true;
		},

		// Set the state of the controls
		set_controls: function () {
			var tiny = this,
				options = tiny.options;

			if (options.controls && !options.infinite) {
				tiny.debug('Updating control\'s state');

				options.btn_prev.toggleClass('disable', (options.slide_current <= 1));
				options.btn_next.toggleClass('disable', (options.slide_current >= options.slide_count))
			}
		},

		// Bind the events that occur while interacting
		set_events: function () {
			var tiny = this,
				options = tiny.options;

			// Stop animation during mouse hover
			if (options.animate && options.pause_hover) {
				tiny.debug('Pause hover enabled');

				tiny.element.hover(function () {
					tiny.force_stop(tiny);
				}, function () {
					tiny.force_start(tiny);
				});
			}

			// Bind events to controls
			if (options.controls) {
				tiny.debug('Button controls enabled');

				event_move = function (elem, direction) {
					var elem = $(elem);

					elem.on('click', function (event) {
						event.preventDefault();
						if (!elem.hasClass('disable')) {
							tiny.move(direction);
						}
					});
				};

				event_move(options.btn_prev, -1);
				event_move(options.btn_next, 1);
			}
		},

		// Set the timer for the next event
		set_timer: function () {
			var tiny = this,
				options = tiny.options;

			if (options.animate && !options.pause) {
				clearTimeout(options.timer);

				options.timer = setTimeout(function () {
					tiny.move(options.forward ? 1 : -1);
				}, options.interval);
			}
		},

		// Debugging message wrapper to safely output results
		debug: function (msg) {
			if (this.options.debug) {
				if (window.console && window.console.log) {
					window.console.log(msg);
				} else {
					alert(msg);
				}
			}
		}
	};

	// Plugin wrapper to prevent multiple instantiations
	$.fn[pluginName] = function (options) {
		return this.each(function () {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
			}
		});
	};

}(jQuery, window));
