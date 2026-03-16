<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\ContactForm as CommonContactForm;
use common\models\Notification;
use common\utils\ModelUtils;

/**
 * Signup form
 */
class ContactForm extends Model
{
    public $topic;
    public $message;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['topic'], 'required', 'message' => Yii::t('app', 'contact_form_topic_required')],
            [['message'], 'required', 'message' => Yii::t('app', 'contact_form_message_required')],

			[['topic', 'message'], 'trim'],
            [['topic', 'message'], 'filter', 'filter' => 'strip_tags'],

            [['topic'], 'string', 'max' => 255],
            [['message'], 'string', 'max' => 1000],
        ];
    }


    /**
     * Saves the submitted form data to the database and sends an email notification to the admin.
     * 
     * @return bool whether the form was submitted successfully
     */
    public function submit() {
        if (!$this->validate()) {
            return false;
        }

        // create form record
        $formRecord = new CommonContactForm();
        $formRecord->topic = $this->topic;
        $formRecord->message = $this->message;  
        $formRecord->user_id = Yii::$app->user->id;

        if (!$formRecord->validate() || !$formRecord->save()) {
            $this->addError('general', 'Unable to save contact form data.');
            ModelUtils::addModelErrors($this, $formRecord);
            return false;
        }

        // send email
        if(!Notification::sendContactFormAdmin($formRecord)) {
            $this->addError('general', 'Unable to send contact form notification email.');
            return false;
        }

        return true;
    }

	
}
