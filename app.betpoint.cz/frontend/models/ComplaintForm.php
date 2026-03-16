<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Notification;
use common\models\User;

/**
 * Signup form
 */
class ComplaintForm extends Model
{
    public $bet_id;
    public $message;

    private $_user;
    private $_userBet;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bet_id'], 'integer'],
            [['bet_id'], 'required'],
            [['bet_id'], 'validateUserBet'],

            [['message'], 'required', 'message' => Yii::t('app', 'complaint_form_message_required')],
			[['message'], 'trim'],
            [['message'], 'filter', 'filter' => 'strip_tags'],
            [['message'], 'string', 'max' => 1000],
        ];
    }


    /**
     * Validates user bet
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUserBet($attribute, $params)
    {
        if (!$this->getUserBet()) {
            $this->addError($attribute, Yii::t('app', 'Bet does not exist or not owned by user.'));
        }
    }


    /**
     * Send complaint form notification to admin
     * 
     * @return bool whether the form was submitted successfully
     */
    public function submit() {
        if (!$this->validate()) {
            return false;
        }

        // send email
        if(!Notification::sendComplaintFormAdmin($this->_userBet, $this->message)) {
            $this->addError('general', 'Unable to send complaint form notification email.');
            return false;
        }

        return true;
    }

    
    /**
     * Get current user
     *
     * @return User|null
     */
    protected function getUser() {
        if ($this->_user === null) {
            $this->_user = User::getCurrent();
        }

        return $this->_user;
    }
    
    /**
     * Get UserBet referenced by bet_id
     *
     * @return UserBet|null
     */
    protected function getUserBet() {
        if ($this->_userBet === null) {
            $user = $this->getUser();
            $this->_userBet = $user ? $user->getUserBets()->andWhere(['id' => $this->bet_id])->one() : null;
        }

        return $this->_userBet;
    }
	
}
