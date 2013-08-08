Section Navigation [Work in Progress]
=======
Animates a group of <section> tags and animates them based on navigation links.

The goal is to emulate page navigation without needing to load new pages, as well as being backwards compatible in the event javascript is disabled. To see a full example, navigate to http://cameroncondry.com for updates to this and other projects.

Example
-------

```js
$(document).ready(function () {
	$('nav a.section').navigation({
		duration: 750,
		duration_offset: 0.95,
		marginLeft: -325
	});
});
```

```html
<nav>
	<ol>
		<li><a href="#about" class="section">About</a></li>
		<li><a href="#project" class="section">Projects</a></li>
		<li><a href="#contact" class="section">Contact</a></li>
	</ol>
</nav>

<section id="about">
	<article>
		<h2>Introduction</h2>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo, neque.</p>
	</article>
	<article>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, officiis dolorum enim nostrum magni possimus.</p>
	</article>
</section>

<section id="project">
	<article>
		<h2>Projects</h2>
		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, iusto, eveniet velit explicabo optio autem quasi fugiat nam beatae placeat pariatur nobis laboriosam quas debitis corporis in vitae ipsa vel.</p>
	</article>
</section>

<section id="contact">
	<article>
		<!-- Contact Form -->
	</article>
</section>
```
Customizable Options
--------------------

**debug: false** - Displays plugin actions in the console.  
**duration: 1000** - Total duration for the animation.  
**duration_offset: 0.7** - Offset modifier when multiple sections are animated.  
**marginLeft: -1000** - Distance sections will travel.  
