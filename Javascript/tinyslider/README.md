Tiny Slider v0.7
=======
An element carousel with a small footprint.

Example
-------

```js
$(document).ready(function () {
	$('.slideshow').tinyslider({
		animate: true,
		infinite: true,
		start: function (current_slide, slides) {
			console.log(current_slide);
		},
		complete: function (current_slide, slides) {
			console.log('animation complete');
		}
	});
});
```

```css
<style type="text/css">
.buttons {
	display: block;
	float: left;
	height: 16px;
	margin: 70px 5px 0 5px;
	position: relative;
}

.disable {
	visibility: hidden;
}

.viewport img {
	display: block;
	max-height: 150px;
	max-width: 150px;
}

.viewport { 
	border: 1px solid #000;
	height: 150px;
	position: relative;
	overflow: hidden;
	width: 150px;
}

.overview {
	height: 150px;
	list-style: none;
	margin: 0;
	padding: 0;
}

.overview li {
	float: left;
	height: 150px;
	margin: 0;
	padding: 0;
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

**debug**: false           --- Enables debugging messages  
**animate**: true          --- Enables automatic transitions  
**animate_wait**: 0        --- Time to wait once an animation is issued  
**controls**: false        --- Enables controls for the slider  
**duration**: 1000         --- Time for animation to complete  
**forward**: true          --- Enables the default direction for automatic transitions  
**pause_hover**: true      --- Pauses the animation when hovering over slider  
**infinite**: false        --- Enabled infinite carousel  
**interval**: 2000         --- Time to wait between each animation  

###Available callbacks###
**start**: null            --- Function callback issued before the animation begins  
**complete**: null         --- Function callback issued after the animation ends  

###Element names###
**viewport**: 'viewport'   --- Element class on the viewport  
**overview**: 'overview'   --- Element class on the overview slides  
