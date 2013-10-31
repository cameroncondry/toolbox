<?php
/**
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
 * Mail_mime PEAR Mailer
 *
 * Holds the underlying logic for the PEAR::Mail system, which
 * provides defaults and configurations for a mailing system.
 *
 * @author		Cameron Condry <ccondry2@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php
 * @version		1.0
 */
require('Mail.php');
require('Mail/mime.php');

/**
 * Creates the base mailer used in the system. The Mail_mime object gets passed
 * into helper functions by reference to configure the mailer.
 * @return Mail_mime
 */
function mailer_init() {
	// Supress the following error from Mail_mime:
	// PHP Warning:  Constants may only evaluate to scalar values
	// Artifact from Mail_mime not being updated for php 5.2+
	return @$mail_mime = new Mail_mime(array(
		'eol' => "\n"
	));
}

/**
 * Sets default and user defined headers used to send the message. User defined headers
 * will take precedence over the default values.
 * @param Mail_mime &$mail_mime
 * @param array $headers		Mail_mime headers
 */
function mailer_set_headers(&$mail_mime, $headers) {
	$date = date('r');

	$default_headers = array(
		'Precedence' => 'bulk',
		'User-Agent' => $_SERVER['SERVER_NAME'],
		'Message-Id' => '<' . date('YmdHi') . '.' . base64_encode(microtime()) . '@' . $_SERVER['SERVER_NAME'] . '>',
		'Date'       => $date,
		'Received'   => 'from ' . $_SERVER['REMOTE_ADDR'] . ' by ' . $_SERVER['SERVER_NAME'] . ' with ' . $_SERVER['SERVER_PROTOCOL'] . ";\n\t" . $date,
		'From'       => 'yourname@yourhost.com',
		'To'         => 'yourname@yourhost.com',
		'Bcc'        => '',
		'Subject'    => 'Request from ' . $_SERVER['SERVER_NAME']
	);

	// Initialize and merge the header information
	$mail_mime->headers(array_merge($default_headers, $headers));
}

/**
 * Set up the templates used for the emails. The templates are fully capable of employing PHP to
 * calculate and format data. Data is passed into the array with the "$view_data" array.
 * @param Mail_mime &$mail_mime
 * @param string $html_path		path to html template
 * @param string $text_path		path to text template
 * @param array	$view_data		values passed into the templates
 */
function mailer_set_template(&$mail_mime, $html_path, $text_path, $view_data = array()) {
	$template = function ($path, $view_data) {
		if (!file_exists($path)) {
			trigger_error('Template file does not exist ' . $path, E_USER_ERROR);
		}

		ob_start();
		include($path);
		$return_value = ob_get_contents();
		ob_end_clean();
		return $return_value;
	};

	$html = $template($html_path, $view_data);
	$text = $template($text_path, $view_data);

	$mail_mime->setHTMLBody($html);
	$mail_mime->setTxtBody($text);
}

/**
 * Retrieves an attachment and adds it to the message.
 * @param Mail_mime &$mail_mime
 * @param string $path		path to attachment
 * @param string $file_name	filename that displays on the recipients message
 * @param bool $mime_type	user defined mime_type
 */
function mailer_add_attachment(&$mail_mime, $path, $file_name, $mime_type = false) {

	if (!$mime_type) {
		$mime_type = _mailer_get_mimetype($path);
	}

	$mail_mime->addAttachment($path, $mime_type, $file_name);
}

/**
 * Retrieves an image for the message template. Reference the image by using
 * the following html syntax: "<img src="cid:file_name" alt="test" />"
 * @param Mail_mime &$mail_mime
 * @param string $path		path to attachment
 * @param string $file_name	cid filename
 * @param bool $mime_type	user defined mime_type
 */
function mailer_add_image(&$mail_mime, $path, $file_name, $mime_type = false) {
	if (!$mime_type) {
		$mime_type = _mailer_get_mimetype($path);
	}

	if (!in_array($mime_type, array('image/gif', 'image/png', 'image/jpeg'))) {
		trigger_error('Invalid image format "' . $mime_type . '"" from ' . $path, E_USER_ERROR);
	}

	$mail_mime->addHtmlImage($path, $mime_type, $file_name, true);
}

/**
 * Sends the message after all the parameters are set.
 * @param Mail_mime $mail_mime
 *
 * @return bool | Mail_mime		true on success, Mail_mime object on failure
 */
function mailer_send(&$mail_mime) {
	$message_body = $mail_mime->get();
	$headers      = $mail_mime->headers();

	$mail_mime = Mail::factory('smtp', array(
		'host'      => 'localhost',
		'port'      => 25,
		'auth'      => false,
		'username'  => '',
		'password'  => '',
		'localhost' => $_SERVER['SERVER_NAME']
	));

	$modified_to = (trim($headers['Bcc']) != '') ? $headers['To'] . ',' . $headers['Bcc'] : $headers['To'];

	// Do not send Bcc information through the headers
	unset($headers['Bcc']);

	return $mail_mime->send($modified_to, $headers, $message_body);
}

/**
 * Opens and analyzes the file on disk to obtain the mime_type.
 * @param string $path	path to file
 *
 * @return string		resulting mime_type of file
 */
function _mailer_get_mimetype($path) {
	$mime_type = false;

	if (file_exists($path)) {
		$finfo = finfo_open(FILEINFO_MIME);

		if ($finfo) {
			$mime_type = explode(';', finfo_file($finfo, $path));
			$mime_type = $mime_type[0];

			finfo_close($finfo);
		}
	} else {
		trigger_error('File does not exist ' . $path, E_USER_ERROR);
	}


	if (!$mime_type) {
		trigger_error('Could not detect mime type: ' . $path, E_USER_ERROR);
	}

	return $mime_type;
}