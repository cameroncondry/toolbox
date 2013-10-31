/**
 *    Navigation slider that rotates content.
 *
 *    @package: Toolbox\Navigation
 *    @author: Cameron Condry <ccondry2@gmail.com>
 *    @copyright: 2013 Cameron Condry
 *    @license: http://www.opensource.org/licenses/mit-license.php
 *    @link: http://www.cameroncondry.com
 *    @version: 0.3
 */

;(function ($, window, undefined) {

	// Create the defaults once
	var pluginName = 'navigation',
		document = window.document,
		previous = 0,
		is_animating = false,
		sections = {},

		defaults = {
			debug: false,			// Turns on debugging messages
			duration: 1000,			// Total duration for the animation
			duration_offset: 0.7,	// Offset modifier between separate sections
			marginLeft: -1000,		// Distance sections will travel
			start: 0				// Beginning block to display
		};

	// The actual plugin constructor
	function Plugin(element, options) {
		this.element = element;
		this.is_animating = is_animating;
		this.options = $.extend({}, defaults, options);
		this.previous = this.options.start;
		this.sections = sections;

		if (element.length == 0) {
			this.debug_msg('Unable to find the anchors.');
		} else {
			this.init();
		}
	}

	Plugin.prototype.init = function () {
		var self = this,
			count = 0,
			layers = self.element.length;

		self.debug_msg('Plugin Initialized');
		self.debug_msg(self.element);
		self.debug_msg(self.options);

		// Iterate over the anchors
		$.each(self.element, function (i, elem) {
			var selector = elem.hash,
				section = $(selector);

			// Hide all elements that are not the starting element
			if (count != self.options.start) { section.hide(); }

			// Stack the sections using z-index
			section.css({'z-index': layers--});

			// Set the defaults to each section
			self.sections[selector] = {
				element: section,
				margin: section.css('marginLeft'),
				name: selector,
				order: count++
			};

			// Register the event handler
			$(elem).on('click', function (event) {
				event.preventDefault();
				self.move(selector);
			});
		});

		self.debug_msg(self.sections);
	};

	Plugin.prototype.move = function(selector) {

		var self = this,
			target = self.sections[selector].order,
			previous = self.previous,
			distance = Math.abs(target - previous),
			removing = (previous < target),
			timer = null;

			self.debug_msg('move(' + selector + ') ' + 'Distance: ' + distance);

		if (self.is_animating || distance === 0) {
			self.debug_msg('Animation currently executing, or distance == 0');
		} else {
			self.is_animating = true;

			// Calculate the animation durations
			var	duration = self.options.duration,
				duration_delay = 0;

			// Reverse the delay if elements are being added to the stack
			if (!removing) {
				duration_delay = ((distance > 1) ? ((duration / distance) * self.options.duration_offset) : 0);
			}

			// Scroll to the top of the page while updating sections
			$('html, body').animate({
				scrollTop: 0
			}, {
				duration: duration / 2,
				queue: false
			});

			$.each(self.sections, function (i, section) {

				self.debug_msg('Section: ' + section.name);

				var is_showing = false;

				// Float the sections for the animation
				section.element.css({position: 'absolute'});

				// Set a timeout to switch the target section to relative positioning
				if (section.order == target) {
					clearTimeout(timer);
					timer = setTimeout(function () {
						section.element.css({position: 'relative'});
					}, duration)
				}

				// Show all sections in between the target and previous
				if (removing && (section.order >= previous) && (section.order <= target)) {
					is_showing = true;
				}

				if (!removing && (section.order <= previous) && (section.order >= target)) {
					is_showing = true;
				}

				// Change show/hide ratios depending on direction. The previous section should
				// take 100% of the hide duration.
				var duration_show = (removing ? 0.4 : 0.6);
				duration_show = (section.order == previous) ? 0 : duration_show;

				var duration_hide = (removing ? 0.6 : 0.4)
				duration_hide = (section.order == previous) ? 1 : duration_hide;

				section.element.animate({
					opacity: (is_showing ? 'show' : 'hide')
				}, {
					duration: duration * duration_show,
					queue: false,
					complete: function () {
						if (section.order != target) {
							section.element.animate({
								opacity: 'hide'
							},{
								duration: duration * duration_hide,
								queue: false
							});
						}
					}
				});

				// Only animate if the section is not already on the stack
				if (is_showing && ((!removing && section.order != previous) || (removing && section.order != target))) {
					var duration_move = duration - duration_delay;

					self.debug_msg('Delay(' + duration_delay + ') + Move(' + duration_move + ') = Duration(' + duration + ')');

					section.element.delay(duration_delay).animate({
						marginLeft: ((section.order < target) ? self.options.marginLeft : section.margin)
					},{
						duration: duration_move,
						complete: function () {
							self.is_animating = false;
						}
					});

					if (!removing) {
						duration_delay -= ((duration / distance) * self.options.duration_offset);
					} else {
						duration_delay += ((duration / distance) * self.options.duration_offset);
					}
				}
			});

			// Store the previous section information
			self.previous = target;
		}
	};

	// Log message if debugging is enabled
	Plugin.prototype.debug_msg = function(msg) {
		if (this.options.debug) {
			if (window.console && window.console.log) {
				window.console.log(msg);
			}
		}
	};

	// Initialize the Plugin
	$.fn[pluginName] = function (options) {
		return $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
	};

}(jQuery, window));