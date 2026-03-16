<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\utils\ModelUtils;
use common\models\Transaction;


/**
 * User Funds form model
 */
class UserFundsForm extends Model
{
	public $user_id = null; // User ID to add funds to
	public $funds_amount = 0;
	public $description = null;

    private $_user = null;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
		return [
				[['user_id', 'funds_amount', 'description'], 'required', 'message' => Yii::t('app', 'model_field_required')],
				
				[['user_id'], 'integer'],
				[
					['user_id'],
					'exist',
					'targetClass' => User::class,
					'targetAttribute' => ['user_id' => 'id'],
					'message' => 'User does not exist.',
				],

				[['description'], 'filter', 'filter' => function($value) { return preg_replace('/[\r\n\t]+/', ' ', $value); }],
				[['description'], 'filter', 'filter' => 'trim'],
				[['description'], 'filter', 'filter' => 'strip_tags'],
				[['description'], 'string', 'max' => 100],

				['funds_amount', 'integer', 
					'max' => Yii::$app->params['user.initialFundsMax'],
					'message' => Yii::t('app', 'model_field_funds_amount_integer'),
					'tooBig' => Yii::t('app', 'model_field_funds_amount_maximum', ['max' => Yii::$app->formatter->asCurrencyValue(Yii::$app->params['user.initialFundsMax'])]),
				],

				['funds_amount', 'validateFundsAmount'],
		];
    }


	/**
	 * Validates the funds_amount for user
	 *
	 * @param string $attribute
	 * @param array $params
	 */
	public function validateFundsAmount($attribute, $params)
	{
		$newAmount = intval($this->$attribute);
		
		// validate non-zero amount
		if ($newAmount === 0) {
			$this->addError($attribute, Yii::t('app', 'model_field_funds_amount_zero'));
			return;
		}
		
		// validate user exists
		$user = $this->getUser();
		if (!$user) {
			$this->addError('user_id', 'User not found.');
			return;
		}

		// validate funds amount
		$userBalance = $user->getFundBalance();
		if ($userBalance + $newAmount < 0) {
			$this->addError($attribute, Yii::t('app', 'model_field_funds_result_balance_negative'));
			return;
		}
	}


	/**
	 * Submit - add funds transaction to user
	 *
	 * @return bool
	 */
	public function submit() {
		if (!$this->validate()) {
			return false;
		}
		$user = $this->getUser();
		$fundsTransaction = new Transaction();
		$fundsTransaction->user_id = $user->id;
		$fundsTransaction->type = Transaction::TYPE_UPDATE_CREDIT;
		$fundsTransaction->amount = $this->funds_amount;
		$fundsTransaction->description = $this->description;
		$fundsTransaction->setActionLabel();

		if ($fundsTransaction->validate() && $fundsTransaction->save()) {
			return true;
		}

		ModelUtils::addModelErrors($this, $fundsTransaction);
		return false;
	}


    /**
     * Get user
     *
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === null) {
            $this->_user = User::findOne($this->user_id);
        }
        return $this->_user;
    }

}
