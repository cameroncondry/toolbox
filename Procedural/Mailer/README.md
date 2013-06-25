About
=======
Provides handling for sending emails using procedural design.

Example
-------

```php
require 'includes/mailer.php';

$headers = array(
	'To' => 'ccondry2@gmail.com',
	'From' => 'ccondry2@gmail.com',
	'Bcc' => 'ccondry2@gmail.com',
	'Subject' => 'Test Email From ' . $config['base_host']
);

$data = array('one', 'two', 'three');

$dir = dirname(__FILE__)
$template_html = $dir . '/templates/template_html.php';
$template_text = $dir . '/templates/template_text.php';
$attachment_pdf = $dir . '/attachments/test.pdf';
$attachment_png = $dir . '/attachments/test.png';

$mailer = mailer_init();
mailer_set_headers($mailer, $headers);
mailer_set_template($mailer, $template_html, $template_text, $data);
mailer_add_attachment($mailer, $attachment_pdf, 'test.pdf');
mailer_add_image($mailer, $attachment_png, 'test_png');
$mailer_result = mailer_send($mailer);

var_dump($mailer);
var_dump($mailer_result);
```

template_html.php
---------------------

```html
<html>
<head>
	<title>Test Email</title>
	<style>
	p {
		padding: 1em;
		margin: 1em;
	}

	img {
		height: 100px;
		width: 100px;
	}
	</style>
</head>
<body>
<img src="cid:test_png" alt="Test Image" />
<p>Email template handling using pure HTML, CSS, and PHP.</p>
<p>Attached will be test.pdf initially referenced.</p>
<p>
	Custom Data:
	<?php
	echo '<pre>';
	var_dump($view_data);
	echo '</pre><br />';
	?>
</p>
</body>
</html>
```