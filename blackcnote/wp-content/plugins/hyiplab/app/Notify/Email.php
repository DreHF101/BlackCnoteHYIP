<?php

namespace Hyiplab\Notify;

use Hyiplab\Notify\NotifyProcess;
use Mailjet\Client;
use Mailjet\Resources;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use SendGrid;
use SendGrid\Mail\Mail;

class Email extends NotifyProcess
{

	/**
	 * Email of receiver
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Assign value to properties
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->statusField = 'email_status';
		$this->body = 'email_body';
		$this->globalTemplate = 'hyiplab_email_template';
		$this->notifyConfig = 'hyiplab_mail_config';
	}

	/**
	 * Send notification
	 *
	 * @return void|bool
	 */
	public function send()
	{

		//get message from parent
		$message = $this->getMessage();
		if(get_option('hyiplab_email_notification') && $message) {
			//Send mail
			$mailConfig = hyiplab_to_object(get_option($this->notifyConfig));
			$methodName = $mailConfig->name;
			$method = $this->mailMethods($methodName);
			try {
				$this->$method();
				// $this->createLog('email');
			} catch (\Exception $e) {
				//notification failed
			}
		}
	}

	/**
	 * Get the method name
	 *
	 * @return string
	 */
	protected function mailMethods($name)
	{
		$methods = [
			'php' => 'sendPhpMail',
			'smtp' => 'sendSmtpMail',
			'sendgrid' => 'sendSendGridMail',
			'mailjet' => 'sendMailjetMail',
		];
		return $methods[$name];
	}

	protected function sendPhpMail()
	{
		$siteName = get_bloginfo('name');
		$mailFrom = get_option('hyiplab_email_from');
		$headers = "From: $siteName <$mailFrom> \r\n";
		$headers .= "Reply-To: $siteName <$mailFrom> \r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		@mail($this->email, $this->subject, $this->finalMessage, $headers);
	}

	protected function sendSmtpMail()
	{
		$mail = new PHPMailer(true);
		$config = hyiplab_to_object(get_option($this->notifyConfig));
		//Server settings
		$mail->isSMTP();
		$mail->Host       = $config->host;
		$mail->SMTPAuth   = true;
		$mail->Username   = $config->username;
		$mail->Password   = $config->password;
		if ($config->enc == 'ssl') {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		}
		$mail->Port       = $config->port;
		$mail->CharSet = 'UTF-8';
		//Recipients
		$mail->setFrom(get_option('hyiplab_email_from'), get_bloginfo('name'));
		$mail->addAddress($this->email, $this->receiverName);
		$mail->addReplyTo(get_option('hyiplab_email_from'), get_bloginfo('name'));
		// Content
		$mail->isHTML(true);
		$mail->Subject = $this->subject;
		$mail->Body    = $this->finalMessage;
		$mail->send();
	}

	protected function sendSendGridMail()
	{
		$config = hyiplab_to_object(get_option($this->notifyConfig));
		$sendgridMail = new Mail();
		$sendgridMail->setFrom(get_option('hyiplab_email_from'), get_bloginfo('name'));
		$sendgridMail->setSubject($this->subject);
		$sendgridMail->addTo($this->email, $this->receiverName);
		$sendgridMail->addContent("text/html", $this->finalMessage);
		$sendgrid = new SendGrid($config->appkey);
		$response = $sendgrid->send($sendgridMail);
		if ($response->statusCode() != 202) {
			throw new Exception(json_decode($response->body())->errors[0]->message);
		}
	}

	protected function sendMailjetMail()
	{
		$config = hyiplab_to_object(get_option($this->notifyConfig));
		$mj = new Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
		$body = [
			'Messages' => [
				[
					'From' => [
						'Email' => get_option('hyiplab_email_from'),
						'Name' => get_bloginfo('name'),
					],
					'To' => [
						[
							'Email' => $this->email,
							'Name' => $this->receiverName,
						]
					],
					'Subject' => $this->subject,
					'TextPart' => "",
					'HTMLPart' => $this->finalMessage,
				]
			]
		];
		$response = $mj->post(Resources::$Email, ['body' => $body]);
	}

	/**
	 * Configure some properties
	 *
	 * @return void
	 */
	protected function prevConfiguration()
	{
		if ($this->user) {
			$this->email = $this->user->email;
			$this->receiverName = $this->user->fullname;
		}
		$this->toAddress = $this->email;
	}
}
