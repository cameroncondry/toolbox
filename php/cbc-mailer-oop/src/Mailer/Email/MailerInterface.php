<?php
/**
 * @package Mailer\Email
 * @license http://www.opensource.org/licenses/mit-license.php
 * @author Cameron Condry <ccondry2@gmail.com>
 */
namespace Mailer\Email;

interface MailerInterface {

	/**
	 * Set required email headers, must include 'To' and 'From' to validate sending.
	 * @param array $headers
	 */
	public function setHeaders($headers);

	/**
	 * Returns all the current headers.
	 * @return array of headers currently set
	 */
	public function getHeaders();

	/**
	 * Set required html template based on a template file. Can also pass extra
	 * data into the template through the $data variable.
	 * @param string $path
	 * @param array $data
	 */
	public function setHtmlTemplate($path, $data = false);

	/**
	 * Set required text template based on a template file. Can also pass extra
	 * data into the template through the $data variable.
	 * @param string $path
	 * @param array $data
	 */
	public function setTextTemplate($path, $data = false);

	/**
	 * Adds an attachment to the email. Detects the mimetype if not provided.
	 * @param string $path
	 * @param string $filename
	 * @param string $mime_type
	 */
	public function addAttachment($path, $filename, $mime_type = false);

	/**
	 * Adds an image to the email that is referenced by the content id. Detects the mimetype if not provided.
	 * Example: <img src="cid:some_id" alt="some_text" />
	 * @param string $path
	 * @param string $content_id
	 * @param string $mime_type
	 */
	public function addHtmlImage($path, $content_id, $mime_type = false);

	/**
	 * Validates and formats the email addresses before sending the email using
	 * the designated backend mailer.
	 */
	public function sendEmail();

	/**
	 * Returns the results of the mailer.
	 * @return false if the mailer has not been sent, TRUE on successful sending, or a Result Object
	 * containing the errors on sending failure.
	 */
	public function getResults();
}