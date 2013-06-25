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
 * @package Mailer\Email
 * @author Cameron Condry <ccondry2@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php
 * @version 1.0
 */
namespace Mailer\Email;

/**
 * Provides the ability to send email through a designated mailer.
 * @package Mailer\Email
 * @author Cameron Condry <ccondry@tls.net>
 */
class Mailer implements MailerInterface {

	/**
	 * Contains the mailer object that sends the actual email.
	 * @var object
	 */
	protected $mailer_method;

	/**
	 * Contains the mail system's headers.
	 * @var array
	 */
	private $_headers = array();

	/**
	 * Contains all the email attachments.
	 * @var array
	 */
	private $_attachments = array();

	/**
	 * Contains all the email images.
	 * @var array
	 */
	private $_html_images = array();

	/**
	 * Contains the email html template.
	 * @var string
	 */
	private $_template_html;

	/**
	 * Contains the email text template.
	 * @var string
	 */
	private $_template_text;

	/**
	 * Contains the result object from mailing erors. Returns TRUE on
	 * success and a result object on failure.
	 * @var bool or result object
	 */
	private $_message_result;

	/**
	 * Initializes required email headers and the mailer object that will be used.
	 *
	 * Example Initialization:
	 * $mailer = new Mailer(new PearMailer);
	 *
	 * @param object $mailer_method
	 */
	public function __construct($mailer_method) {

		$this->mailer_method = $mailer_method;

		$date = date('r');

		// Initialize all the required email headers
		$this->_headers = array(
			'Precedence' => 'bulk',
			'User-Agent' => $_SERVER['SERVER_NAME'],
			'Message-Id' => '<' . date('YmdHi') . '.' . base64_encode(microtime()) . '@' . $_SERVER['SERVER_NAME'] . '>',
			'Date' => $date,
			'Received' => 'from ' . $_SERVER['REMOTE_ADDR'] . ' by ' . $_SERVER['SERVER_NAME'] . ' with ' . $_SERVER['SERVER_PROTOCOL'] . ";\n\t" . $date,
			'Subject' => 'Message From http://' . $_SERVER['SERVER_NAME'],
			'To' => '',
			'From' => '',
			'Bcc' => ''
		);
	}

	/**
	 * Set required email headers, must include 'To' and 'From' to validate sending.
	 * @param array $headers
	 */
	public function setHeaders($headers) {
		$this->_headers = array_merge($this->_headers, $headers);
	}

	/**
	 * Returns all the current headers.
	 * @return array of headers currently set
	 */
	public function getHeaders() {
		return $this->_headers;
	}

	/**
	 * Set required html template based on a template file. Can also pass extra
	 * data into the template through the $data variable.
	 * @param string $path
	 * @param array $data
	 */
	public function setHtmlTemplate($path, $data = FALSE) {
		$this->_template_html = $this->_populateTemplate($path, $data);
	}

	/**
	 * Returns the current html template.
	 * @return NULL if no template has been set or a string containing the template.
	 */
	public function getHtmlTemplate() {
		return $this->_template_html;
	}

	/**
	 * Set required text template based on a template file. Can also pass extra
	 * data into the template through the $data variable.
	 * @param string $path
	 * @param array $data
	 */
	public function setTextTemplate($path, $data = FALSE) {
		$this->_template_text = $this->_populateTemplate($path, $data);
	}

	/**
	 * Returns the current text template.
	 * @return NULL if no template has been set or a string containing the template.
	 */
	public function getTextTemplate() {
		return $this->_template_text;
	}

	/**
	 * Adds an attachment to the email. Detects the mimetype if not provided.
	 * @param string $path
	 * @param string $filename
	 * @param string $mime_type
	 */
	public function addAttachment($path, $filename, $mime_type = FALSE) {

		if (!$mime_type) {
			$mime_type = $this->_getMimetype($path);

			if (!$mime_type) {
				throw new Exception\MimetypeNotFoundException('Could not detect mime type of ' . $path);
			}
		}

		$this->_attachments[] = array(
			'path' => $path,
			'mime_type' => $mime_type,
			'filename' => $filename
		);
	}

	/**
	 * Returns the array of all the attachments.
	 * @return array of attachments
	 */
	public function getAttachments() {
		return $this->_attachments;
	}

	/**
	 * Adds an image to the email that is referenced by the content id. Detects the mimetype if not provided.
	 * Example: <img src="cid:some_id" alt="some_text" />
	 * @param string $path
	 * @param string $content_id
	 * @param string $mime_type
	 */
	public function addHtmlImage($path, $content_id, $mime_type = FALSE) {

		if (!$mime_type) {
			$mime_type = $this->_getMimetype($path);

			if (!$mime_type) {
				throw new Exception\MimetypeNotFoundException('Could not detect mime type of ' . $path);
			}
		}

		if (!in_array($mime_type, array( 'image/gif', 'image/png', 'image/jpeg' ))) {
			throw new Exception\InvalidImageException('Invalid image format "' . $mime_type . '"" from ' . $path);
		}

		$this->_html_images[] = array(
			'path' => $path,
			'mime_type' => $mime_type,
			'name' => array_pop(explode('/', $path)),
			'is_file' => TRUE,
			'content_id' => $content_id
		);
	}

	/**
	 * Returns the array of all the images.
	 * @return array of images
	 */
	public function getHtmlImages() {
		return $this->_html_images;
	}

	/**
	 * Validates and formats the email addresses before sending the email using
	 * the designated backend mailer.
	 */
	public function sendEmail() {

		// Separate the emails by ",", ";", or "|", then remove any empty values with array_filter
		$trim_explode = function($string) {
			return array_filter(array_map('trim', preg_split('/,|\||;/', $string)));
		};

		$to = $trim_explode($this->_headers['To']);
		$from = $trim_explode($this->_headers['From']);
		$bcc = $trim_explode($this->_headers['Bcc']);

		if ($to == '' || $from == '') {
			throw new Exception\NoRecipientsException('Must have a "To" and "From" email addresses');
		}

		// Verify all information before creating the Mail_mime object
		$this->_verifyEmail( array( $to, $from, $bcc ) );

		// Reset the 'To', 'From', and 'Bcc' headers, then send the mail
		$this->_headers['To'] = implode(',', $to);
		$this->_headers['From'] = implode(',', $from);
		$this->_headers['Bcc'] = implode(',', $bcc);

		// Send the email using the indicated mailer method
		$this->mailer_method->setHeaders($this->_headers);
		$this->mailer_method->setMessage($this->_template_html, $this->_template_text);
		$this->mailer_method->addAttachments($this->_attachments);
		$this->mailer_method->addHtmlImages($this->_html_images);

		$this->mailer_method->sendEmail();
		$this->_message_result = $this->mailer_method->getResults();
	}

	/**
	 * Returns the results of the mailer.
	 * @return FALSE if the mailer has not been sent, TRUE on successful sending, or a Result Object
	 * containing the errors on sending failure.
	 */
	public function getResults() {
		if ($this->_message_result) {
			return $this->_message_result;
		} else {
			return FALSE;
		}
	}

	/**
	 * Creates a template from a file that is used for the email.
	 * @param string $path
	 * @param array $data
	 * @return Returns the template from the indicated path.
	 */
	private function _populateTemplate($path, $data) {

		if (!file_exists($path)) {
			throw new Exception\TemplatePathException('Template file does not exist ' . $path);
		}

		ob_start();
		include($path);
		$return_value = ob_get_contents();
		ob_end_clean();

		return $return_value;
	}

	/**
	 * Detects the mimetype of a file and returns the detected mimetype.
	 * @param string $path
	 * @return Returns the mimetype of a file.
	 */
	private function _getMimetype($path) {

		$return_value = FALSE;

		if (file_exists($path)) {
			$finfo = finfo_open(FILEINFO_MIME);

			if ($finfo) {
				$mime_type = explode(';', finfo_file($finfo, $path));
				$return_value = $mime_type[0];

				finfo_close($finfo);
			}
		} else {
			throw new Exception\MimePathException('File does not exist ' . $path);
		}

		return $return_value;
	}

	/**
	 * Validates an array of emails, throwing an exception in the event of an invalid email.
	 * @param array $email_addresses
	 */
	private function _verifyEmail($email_addresses) {

		if (is_array($email_addresses) && count($email_addresses) > 0) {

			// Iterate over the 'To', 'From', and 'Bcc' emails to validate all emails
			foreach ($email_addresses as $emails) {
				foreach ($emails as $email) {

					// Validate the following type of emails: "Firstname Lastname" <example@email.com>
					$count_open_caret = substr_count($email, '<');
					$count_closed_caret= substr_count($email, '>');

					if ($count_open_caret == 1 && $count_closed_caret == 1) {
						preg_match('/<(.*)>/', $email, $email);
						$email = $email[1];
					}

					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						throw new Exception\InvalidEmailException('"' . $email . '" is not a valid email');
					}
				}
			}

		} else {
			throw new Exception\NoEmailException('No email address provided');
		}
	}
}