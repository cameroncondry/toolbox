<?php
/**
 * @package Mailer\Email
 * @license http://www.opensource.org/licenses/mit-license.php
 * @author Cameron Condry <ccondry2@gmail.com>
 */
namespace Mailer\Email;

interface BackendInterface {

	/**
	 * Sets the mail system's headers.
	 * @param array $headers
	 */
	public function setHeaders($headers);

	/**
	 * Sets the mail system's email contents.
	 * @param string $body_html
	 * @param string $body_text
	 */
	public function setMessage($body_html, $body_text);

	/**
	 * Sets the mail system's email attachments.
	 * @param array $html_images
	 * @param array $attachments
	 */
	public function addAttachments($attachments);

	/**
	 * Sets the mail system's email html images.
	 * @param array $html_images
	 */
	public function addHtmlImages($html_images);

	/**
	 * Sends the email based on the email system's backend.
	 */
	public function sendEmail();

	/**
	 * Returns the results of the mailer.
	 * @return FALSE if the mailer has not been sent, TRUE on successful sending, or a Result Object
	 * containing the errors on sending failure.
	 */
	public function getResults();
}