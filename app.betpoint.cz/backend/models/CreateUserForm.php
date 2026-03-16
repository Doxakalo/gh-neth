<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\utils\ModelUtils;
use common\models\Transaction;


/**
 * Create User form model
 *
 * @property User $user
 * 
 */
class CreateUserForm extends Model
{
    public $first_name;
    public $last_name;
    public $nickname;
    public $email;
    public $password;
	public $comment;
	public $funds_amount = 0;

	private $_user = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
				'nickname',
				'email',
				'password',
				'funds_amount',
			], 'required', 'message' => Yii::t('app', 'model_field_required')],

			[[
				'first_name',
				'last_name',
				'nickname',
				'email',
				'password',
				'comment',
			], 'trim'],

            [[
				'first_name',
				'last_name',
				'nickname',
				'password',
				'comment',
			], 'filter', 'filter' => 'strip_tags'],

            [['first_name', 'last_name', 'nickname'], 'string', 'min' => 2, 'max' => 128, 
				'tooShort' => Yii::t('app', 'model_field_too_short', ['length' => 2])
			],

			['nickname', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'model_field_nickname_unique')],

			[['comment'], 'string', 'max' => 1000],

			['funds_amount', 'integer', 
				'min' => Yii::$app->params['user.initialFundsMin'], 
				'max' => Yii::$app->params['user.initialFundsMax'],
				'message' => Yii::t('app', 'model_field_funds_amount_integer'),
				'tooSmall' => Yii::t('app', 'model_field_funds_amount_minimum', ['min' => Yii::$app->formatter->asCurrencyValue(Yii::$app->params['user.initialFundsMin'])]),
				'tooBig' => Yii::t('app', 'model_field_funds_amount_maximum', ['max' => Yii::$app->formatter->asCurrencyValue(Yii::$app->params['user.initialFundsMax'])]),
			],

			['email', 'filter', 'filter' => 'strtolower'],
            ['email', 'string', 'max' => 255],
            ['email', 'email', 'message' => Yii::t('app', 'model_field_email')],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'model_field_email_unique')],

			['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'], 
				'tooShort' => Yii::t('app', 'model_field_password_short', ['length' => Yii::$app->params['user.passwordMinLength']])],
			['password', 'match', 'pattern' => Yii::$app->params['user.passwordStrengthMatch'], 
				'message' => Yii::t('app', 'model_field_password_strength')],

        ];
    }


	/**
	 * {@inheritdoc}
	 */
	public function init() {
		parent::init();
		// set default values
		$this->funds_amount = Yii::$app->params['user.initialFunds'];
	}


    /**
     * Signup user
     *
     * @return User|false user model if successfull or false if saving fails
	 */
	public function signup() {
		if (!$this->validate()) {
			return false;
		}

		$transaction = \Yii::$app->db->beginTransaction();
		try {
			$user = $this->createUser();
			if (!$user) {
				$this->addError('general', 'Error while creating user.');
				$transaction->rollBack();
				return false;
			}

			if(!$this->addFunds($user->id)) {
				$this->addError('general', 'Error while adding funds.');
				$transaction->rollBack();
				return false;
			}

			$transaction->commit();
			$this->_user = $user;
			return true;

		} catch (\Exception $e) {
			$this->addError('general', 'An error occurred during signup.');
			$this->addError('general', $e->getMessage());

			$transaction->rollBack();
			return false;
		}
	}


	/**
	 * Create user
	 *
	 * @return User|bool user model if successfull or false if saving fails
	 */
	private function createUser() {
		$user = new User();
		$user->first_name = $this->first_name;
		$user->last_name = $this->last_name;
		$user->nickname = $this->nickname;
		$user->email = $this->email;
		$user->comment = $this->comment;
		$user->status = User::STATUS_ACTIVE;
		$user->type = User::USER_TYPE_PUBLIC;
		$user->setPassword($this->password);
		$user->generateAuthKey();
		$user->generateEmailVerificationToken();

		if ($user->validate() && $user->save()) {
			return $user;
		}

		ModelUtils::addModelErrors($this, $user);
		return false;
	}


	/**
	 * Add initial funds
	 *
	 * @return bool
	 */
	private function addFunds($userId) {
		$fundsTransaction = new Transaction();
		$fundsTransaction->user_id = $userId;
		$fundsTransaction->type = Transaction::TYPE_INITIAL_CREDIT;
		$fundsTransaction->amount = $this->funds_amount;
		$fundsTransaction->setActionLabel();
		$fundsTransaction->setDescriptionLabel();

		if ($fundsTransaction->validate() && $fundsTransaction->save()) {
			return true;
		}

		ModelUtils::addModelErrors($this, $fundsTransaction);
		return false;
	}


	public function getUser() {
		return $this->_user;
	}


}
