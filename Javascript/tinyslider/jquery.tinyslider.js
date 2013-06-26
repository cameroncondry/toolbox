/*
 * Copyright (c) 2013 Cameron Condry <ccondry2@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * ===========================================================================
 *
 * Tiny Slider
 *
 * Displays elements in a simple and highly customizable carousel. 
 * 
 * @author: Cameron Condry <ccondry2@gmail.com>
 * @license: http://www.opensource.org/licenses/mit-license.php
 * @version: 0.6.0
 * 
 * Dependencies:
 * 		jQuery <http://code.jquery.com/jquery.js>
 *
 */
;(function($, window, document, undefined) {

	$.tiny = $.tiny || { };

	$.tiny.slider = {
		options: {
			debug: false			// Turns on debugging information

			, animate: false		// Denotes if the animation should occur
			, beforeanimate: null	// Function called before animation begins
			, beforeanimatewait: 0	// Time to wait before starting the animation
			, callback: null		// Function called when the animation completes
			, controls: false		// Uses the Next and Previous buttons
			, duration: 1000		// Duration the animation will take to complete
			, forward: true			// Set the direction of the animation
			, infinite: false		// Sets an infinite scrolling loop with the blocks
			, intervaltime: 4000	// Duration between each animation
			, pause_on_hover: true	// Pauses the animation on mouse hover events

			// Element names
			, viewport: 'viewport'
			, overview: 'overview'

			// The following options do not work with an infinite carousel
			, display: 1			// Number of blocks to move at one time
			, start: 1 				// Starting block
		}
	};

	$.fn.tinyslider = function (params) {
		var settings = $.extend({}, $.tiny.slider.options, params);

		return this.each(function () { new Slider($(this), settings); });
	};

	function Slider(root, settings) {
		var self		= this
		, viewport 		= $('.' + settings.viewport + ':first', root)
		, content		= $('.' + settings.overview + ':first', root)
		, slides		= $('ul.' + settings.overview, root)
		, pages			= content.children()
		, page_count	= pages.length
		, page_current	= 0
		, page_size		= 0
		, steps			= 0
		, btn_next		= $('.next:first', root)
		, btn_prev		= $('.prev:first', root)
		, forward		= true
		, pause			= false
		, animating		= false
		, timer			= undefined
		;

		this.move = function (direction) {

			// Wait until any previous animation completes before starting the next animation
			if (animating) {
				if (settings.debug) {
					console.log('Slider::move(' + direction + ') Waiting for animation to complete');
				}
			} else {

				// Lock further animations while processing
				animating = true;

				// Set the block to move towards
				page_current += direction;

				// Wrap the current page when at the end and beginning of the slideshow
				page_current = (page_current <= steps - 1) ? page_current : 0;
				page_current = (page_current >= 0) ? page_current : steps - 1;

				// Callback before the animation starts
				if (typeof settings.beforeanimate === 'function') {

					if (settings.debug) {
						console.log('Slider::move(' + direction + ').beforeanimate()');
					}

					settings.beforeanimate.call(this, pages[page_current], page_current);
				}

				// Callback after the animation ends
				var callback = function () {

					// Reset the animating state once complete
					animating = false;

					if (typeof settings.callback === 'function') {

						if (settings.debug) {
							console.log('Slider::move(' + direction + ').callback()');
						}

						settings.callback.call(this, pages[page_current], page_current);
					}
				}

				// Wait time before starting the animation
				setTimeout(function () {

					// Determine if the the carousel is infinite
					if (settings.infinite) {

						var list = $(slides).children();

						// Determine direction
						if (direction > 0) {

							// Move to the next slide
							list.first().animate({
								marginLeft: '-=' + page_size + 'px'
							}, {
								queue: false,
								duration: settings.duration,
								complete: function () {
									// Rotate the first item to the last item
									list.first().css({
										marginLeft: '0px',
									}).insertAfter(list.last());

									callback();
								}
							});
						} else {

							// Rotate the last item to the first item
							list.last().css({
								marginLeft: '-' + page_size + 'px',
							}).insertBefore(list.first());

							// Refresh the list before animating
							list = $(slides).children();

							// Move to the previous slide
							list.first().animate({
								marginLeft: '0'
							}, {
								queue: false,
								duration: settings.duration,
								complete: function () {
									callback();
								}
							});
						}
						
					} else {
						content.animate({
							left: -(page_current * (page_size * settings.display)) + 'px'
						}, {
							queue: false,
							duration: settings.duration,
							complete: function () {
								callback();
							}
						});
					}
				}, settings.beforeanimatewait);

				set_timer();
				set_buttons();

				if (settings.debug) {
					console.log('Slider::move(' + direction + ') Current Page: ' + page_current + ' / ' + steps);
				}
			}
		}

		this.start = function () {

			if (settings.debug) {
				console.log('Slider::start()');
			}

			pause = false;
			set_timer();
		}

		this.stop = function () {

			if (settings.debug) {
				console.log('Slider::stop()');
			}

			clearTimeout(timer);
			pause = true;
		}

		function set_timer() {

			if (settings.animate && !pause) {
				clearTimeout(timer);

				timer = setTimeout(function () {
					self.move(settings.forward ? 1 : -1);
				}, settings.intervaltime);
			}
		}

		function set_buttons() {
			if (settings.controls && !settings.infinite) {

				if (settings.debug) {
					console.log('Slider::set_buttons() Current Page: ' + page_current + ' / ' + steps);
				}

				btn_next.toggleClass('disable', !(page_current + 1 < steps));
				btn_prev.toggleClass('disable', page_current <= 0);
			}
		}

		function set_events() {
			// Stop animation during mouse hover
			if (settings.animate && settings.pause_on_hover) {
				root.hover(self.stop, self.start);
			}

			// Move the slideshow based on which control is clicked
			if (settings.controls) {
				btn_next.on('click', function (e) {
					var elem = e.currentTarget

					if (!$(elem).hasClass('disable')) {
						self.move(1);
					}
					return false; 
				});
				btn_prev.on('click', function (e) {
					var elem = e.currentTarget

					if (!$(elem).hasClass('disable')) {
						self.move(-1);
					}
					return false;
				});
			}
		}

		function initialize() {

			if (settings.debug) {
				console.log('Slider::initialize()');
			}

			// Retrieve the block size to calculate full width of the element
			page_size = $(pages[0]).outerWidth(true);

			// Set the distance and starting location
			steps = Math.max(1, Math.ceil(page_count / settings.display));
			page_current = Math.min(steps, Math.max(1, settings.start)) - 1;

			// Calculate and set the full width of the element
			content.css('width', page_size * page_count);

			// Begin the animation
			set_timer();
			set_events();
			set_buttons();

			if (settings.debug) {
				console.log('steps: ' + steps);
				console.log('page_size: ' + page_size);
				console.log('page_count: ' + page_count);
				console.log('page_current: ' + page_current);
				console.log('settings.start: ' + settings.start);
				console.log('settings.display: ' + settings.display);
				console.log('settings.infinite: ' + settings.infinite);
				console.log('settings.duration: ' + settings.duration);
				console.log('settings.intervaltime: ' + settings.intervaltime);
			}

			return self;
		}

		initialize();
	};

} (jQuery, window, document));