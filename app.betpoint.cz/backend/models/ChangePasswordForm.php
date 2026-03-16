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
class ChangePasswordForm extends Model
{
	public $user_id = null; // User ID to change password for
    public $password;

	private $_user = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

			[['user_id', 'password'], 'required', 'message' => Yii::t('app', 'model_field_required')],

			[['user_id'], 'integer'],
			[
				['user_id'],
				'exist',
				'targetClass' => User::class,
				'targetAttribute' => ['user_id' => 'id'],
				'message' => 'User does not exist.',
			],

            [[ 'password'], 'filter', 'filter' => 'strip_tags'],

			['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'], 
				'tooShort' => Yii::t('app', 'model_field_password_short', ['length' => Yii::$app->params['user.passwordMinLength']])],
			['password', 'match', 'pattern' => Yii::$app->params['user.passwordStrengthMatch'], 
				'message' => Yii::t('app', 'model_field_password_strength')],

        ];
    }


	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'user_id' => Yii::t('app', 'User ID'),
			'password' => Yii::t('app', 'New Password'),
		];
	}


	/**
	 * Update user password
	 *
	 * @return User|bool user model if successfull or false if saving fails
	 */
	public function submit() {
		if (!$this->validate()) {
			return false;
		}
		$user = $this->getUser();
		$user->setPassword($this->password);
		$user->generateAuthKey();
		$user->generateEmailVerificationToken();

		if ($user->validate() && $user->save()) {
			return true;
		}

		ModelUtils::addModelErrors($this, $user);
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
