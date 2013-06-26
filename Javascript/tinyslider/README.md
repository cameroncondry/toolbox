Tiny Slider
=======
Animate elements in a simple and highly customizable carousel. 

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

.viewport img {
	display: block;
	max-height: 150px;
	max-width: 150px;
}
.viewport { 
	border: 1px solid #000;
	float: left;
	height: 150px;
	position: relative;
	overflow: hidden;
	width: 150px;
	padding: 0;
}

.overview {
	height: 150px;
	left: 0;
	top: 0;
	list-style: none; margin: 0; padding: 0; position: absolute; }
.overview li {
	float: left;
	height: 150px;
	margin: 0;
	padding: 0;
	text-align: left;
	width: 150px;
}
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

**debug: false** - Displays plugin actions in the console. *Not included in the minified version of tinyslider*.  
**animate: false** - Animates the element during the transition.  
**beforeanimate: null** - Callback function called before each animation begins.  
**beforeanimatewait: 0** - Time the element waits before starting the next animation.  
**callback: null** - Callback function called once each animation ends.  
**controls: false** - Sets up handlers to manually move the elements from one slide to the next.  
**duration: 1000** - Time the animation will take to complete.  
**forward: true** - Sets the direction the animation will run, set to false to run in the opposite direction.  
**infinite: false** - Sets the elements to infinitely scroll in one direction.  
**intervaltime: 4000** - Wait time between each animation.  
**pause_on_hover: true** - Pauses the animation when a hover event is fired.  

--------------------
The following options do not work with **infinite: true**.

**display: 1** - Number of elements moved at once.  
**start: 1** - Element where the carousel starts.
