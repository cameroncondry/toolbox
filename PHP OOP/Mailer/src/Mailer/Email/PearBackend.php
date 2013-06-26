<?php
/**
 * @package Mailer\Email
 * @license http://www.opensource.org/licenses/mit-license.php
 * @author Cameron Condry <ccondry2@gmail.com>
 */
namespace Mailer\Email;

require 'Mail.php';
require 'Mail/mime.php';

use Mail;
use Mail_mime;

class PearBackend implements BackendInterface {

	/**
	 * Contains the mail system's headers.
	 * @var array
	 */
	private $_headers = array();

	/**
	 * Contains the result object from mailing erors. Returns TRUE on
	 * success and a result object on failure.
	 * @var bool or result object
	 */
	private $_message_result;

	/**
	 * Sets the mail system's headers.
	 * @param array $headers
	 */
	public function setHeaders($headers) {
		$this->_headers = $headers;
	}

	/**
	 * Sets the mail system's email contents.
	 * @param string $body_html
	 * @param string $body_text
	 */
	public function setMessage($body_html, $body_text) {
		$this->_getMime()->setHTMLBody($body_html);
		$this->_getMime()->setTxtBody($body_text);
	}

	/**
	 * Sets the mail system's email attachments.
	 * @param array $attachments
	 */
	public function addAttachments($attachments) {
		if (is_array($attachments) && count($attachments) > 0) {
			foreach ($attachments as $attachment) {
				$this->_getMime()->addAttachment($attachment['path'], $attachment['mime_type'], $attachment['filename']);
			}
		}
	}

	/**
	 * Sets the mail system's email html images.
	 * @param array $html_images
	 */
	public function addHtmlImages($html_images) {
		if (is_array($html_images) && count($html_images) > 0) {
			foreach ($html_images as $image) {
				$this->_getMime()->addHTMLImage($image['path'], $image['mime_type'], $image['name'], $image['is_file'], $image['content_id']);
			}
		}
	}

	/**
	 * Sends the email based on the email system's backend.
	 */
	public function sendEmail() {

		$message_body = $this->_getMime()->get();
		$headers = $this->_getMime()->headers($this->_headers);

		$mail = Mail::factory('smtp', array(
			'host' => 'localhost',
			'port' => 25,
			'auth' => FALSE,
			'username' => '',
			'password' => '',
			'localhost' => $_SERVER['SERVER_NAME']
		));

		$modified_to = (trim($this->_headers['Bcc']) != '') ? $this->_headers['To'] . ',' . $this->_headers['Bcc'] : $this->_headers['To'];

		// Do not send Bcc information through the headers
		unset($headers['Bcc']);

		$this->_message_result = $mail->send($modified_to, $headers, $message_body);
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
	 * Returns the Mail_mime object that is used for the email system's backend.
	 * @return Mail_mime object.
	 */
	private function _getMime() {
		static $returnValue = NULL;

		if (!$returnValue) {
			$returnValue = new Mail_mime(array(
				'eol' => "\n"
			));
		}

		return $returnValue;
	}
}