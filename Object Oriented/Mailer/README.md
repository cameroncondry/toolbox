About
=======
Provides handling for sending emails with a minimalistic design through multiple interfaces.

Example
-------

```php
use Mailer\Email\Mailer;
use Mailer\Email\PearBackend;

$headers = array(
	'To' => 'ccondry2@gmail.com',
	'From' => 'ccondry2@gmail.com',
	'Bcc' => 'ccondry2@gmail.com',
	'Subject' => 'Test Email From ' . $config['base_host']
);

$data = array('one', 'two', 'three');

$dir = dirname(__FILE__);
$template_html = $dir . '/templates/template_html.php';
$template_text = $dir . '/templates/template_text.php';
$attachment_pdf = $dir . '/attachments/test.pdf';
$attachment_png = $dir . '/attachments/test.png';

$mailer = new Mailer(new PearBackend());
$mailer->setHeaders($headers);
$mailer->setHtmlTemplate($template_html, $data);
$mailer->setTextTemplate($template_text, $data);
$mailer->addAttachment($attachment_pdf, 'test.pdf');
$mailer->addHtmlImage($attachment_png, 'test_png');
$mailer->sendEmail();

var_dump($mailer);
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
	var_dump($data);
	echo '</pre><br />';
	?>
</p>
</body>
</html>
```
