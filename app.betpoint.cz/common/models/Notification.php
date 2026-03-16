<?php

namespace common\models;

use Yii;
use common\utils\mail\MailUtils;

/**
 * Notification utility
 */
class Notification {
	
    /**
     * Send test message
	 * 
	 * @param string $toEmail
     * @return bool whether the email was sent
     */
	public static function sendTest($toEmail)
    {
		// parse view+layout
		$html = Yii::$app->view->render('@common/mail/test-message');
		$htmlClean = MailUtils::removeScriptTags($html);
		$htmlInline = MailUtils::inlineHtmlCss($htmlClean, Yii::getAlias('@common/mail/css/mail.css'));
		$text = MailUtils::toPlainText($htmlClean);
		
		// compile and send message
		$message = [
			'subject' => 'Test message',
			'html' => $htmlInline,
			'text' => $text,
			'images' => [],
			'to' => [$toEmail],
		];

		return self::send($message);
    }
	

    /**
     * Send contact form notification to admin
	 * 
     * @param common\models\ContactForm $contactForm
     * @return bool whether the email was sent
     */
	public static function sendContactFormAdmin($contactForm)
    {
		// parse view+layout
		$html = Yii::$app->view->render('@common/mail/contact-form-admin', ['contactForm' => $contactForm]);
		$htmlClean = MailUtils::removeScriptTags($html);
		$htmlInline = MailUtils::inlineHtmlCss($htmlClean, Yii::getAlias('@common/mail/css/mail.css'));
		$text = MailUtils::toPlainText($htmlClean);
		
		// compile and send message
		$message = [
			'subject' => Yii::t('common', 'email_contact_form_subject'),
			'html' => $htmlInline,
			'text' => $text,
			'images' => [],
			'to' => Yii::$app->params['admin.contactFormRecipients'],
		];

		return self::send($message);
    }
	

    /**
     * Send complaint form to admin
	 * 
	 * @param common\models\UserBet $userBet
	 * @param string $message
     * @return bool whether the email was sent
     */
	public static function sendComplaintFormAdmin($userBet, $message)
    {
		// parse view+layout
		$html = Yii::$app->view->render('@common/mail/complaint-form-admin', ['userBet' => $userBet, 'message' => $message]);
		$htmlClean = MailUtils::removeScriptTags($html);
		$htmlInline = MailUtils::inlineHtmlCss($htmlClean, Yii::getAlias('@common/mail/css/mail.css'));
		$text = MailUtils::toPlainText($htmlClean);
		
		// compile and send message
		$message = [
			'subject' => Yii::t('common', 'email_complaint_form_subject'),
			'html' => $htmlInline,
			'text' => $text,
			'images' => [],
			'to' => Yii::$app->params['admin.complaintFormRecipients'],
		];

		return self::send($message);
    }


	/**
	 * Send message
	 * 
	 * @param array $message
	 * @return bool whether the email was sent
	 */
	private static function send($message) {
		// Use default mailer
		return self::sendMessage($message);
	}


	/**
	 * Send message using default mailer
	 * 
	 * @param array $message
	 * @return bool whether the email was sent
	 */
	private static function sendMessage($message) {
		return Yii::$app->mailer->compose()
			->setFrom([Yii::$app->params['adminSender'] => Yii::$app->params['adminSenderName']])
			->setReplyTo(Yii::$app->params['adminReplyTo'])
			->setTo($message['to'])
			->setSubject($message['subject'])
			->setHtmlBody($message['html'])
			->setTextBody($message['text'])
			->send();
	}
	
}
