About
=======
Display elements in a simple and highly customizable carousel. 

Example
-------

```js
$(document).ready(function () {
	$('.slideshow').tinyslider({
		animate: true,
		infinite: true,
		beforeanimate: function (elem, index) {
			console.log(index);
		},
		callback: function (elem, index) {
			console.log('animation complete');
		}
	});
});
```

```css
<style type="text/css">
.slideshow { height: 1%; overflow: hidden; }
.buttons { display: block; float: left; height: 16px; margin: 70px 5px 0 5px; position: relative; }
.disable { visibility: hidden; }

.viewport img { display: block; max-height: 150px; max-width: 150px; }
.viewport { border: 1px solid #000; float: left; height: 150px; position: relative; overflow: hidden; width: 150px; padding: 0; }

.overview { height: 150px; left: 0; top: 0; list-style: none; margin: 0; padding: 0; position: absolute; }
.overview li { float: left; height: 150px;margin: 0; padding: 0; text-align: left; width: 150px; }
</style>
```

```html
<div class="slideshow">
	<a href="#" class="buttons prev">Prev</a>
	<div class="viewport">
		<ul class="overview">
			<li><img src="images/hippo.jpg" alt="Hippo"></li>
			<li><img src="images/monkey.jpg" alt="Monkey"></li>
		</ul>
	</div>
	<a href="#" class="buttons next">Next</a>
</div>
```

Customizable Options
--------------------

#### Name: Description
Option: Default

#### Debug Mode: Turns on debug mode to display interactions in the console. *Not included in the minified version of tinyslider*.
debug: false

#### Animate: Animates the element during the transition.
animate: false

#### Before Animation: Callback function called before each animation begins.
beforeanimate: null

#### Animation Wait Time: Time the element waits before starting the next animation.
beforeanimatewait: 0

#### After Animation: Callback function called once each animation ends.
callback: null

#### Controls: Sets up handlers to manually move the elements from one slide to the next.
controls: false

#### Duration: Time the animation will take to complete.
duration: 1000

#### Forward: Sets the direction the animation will run, set to false to run in the opposite direction.
forward: true

#### Infinite Scroll: Sets the elements to infinitely scroll in one direction.
infinite: false

#### Interval Time: Wait time between each animation.
intervaltime: 4000

#### Pause on Hover: Pauses the animation when a hover event is fired.
pause_on_hover: true

--------------------
The following options do not work with "infinite: true".

#### Display: Number of elements moved at once.
display: 1

#### Start: Element where the carousel starts.
start: 1