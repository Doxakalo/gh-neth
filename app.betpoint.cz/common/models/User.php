<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $nickname
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string|null $verification_token
 * @property string $email
 * @property string $password write-only password
 * @property int $status
 * @property string|null $comment 
 * @property int $type
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $last_active_at 
 *
 * @property ContactForm[] $contactForms
 * @property SportMatchResult[] $sportMatchResults
 * @property Transaction[] $transactions
 * @property UserBet[] $userBets
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const USER_TYPE_PUBLIC = 0;
    const USER_TYPE_ADMIN = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'integer'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            
            ['type', 'integer'],
            ['type', 'in', 'range' => [self::USER_TYPE_PUBLIC, self::USER_TYPE_ADMIN]],
            ['type', 'default', 'value' => self::USER_TYPE_PUBLIC],

            [['nickname', 'auth_key', 'password_hash', 'email'], 'required'],
            [['first_name', 'last_name', 'nickname'], 'string', 'max' => 128],

            [['nickname'], 'unique'],

            [['auth_key'], 'string', 'max' => 32],
            [['password_reset_token', 'verification_token'], 'default', 'value' => null],
            [['password_hash', 'password_reset_token', 'verification_token', 'email'], 'string', 'max' => 255],
            
            [['email'], 'email'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            
            [['comment'], 'string', 'max' => 1000],

            [[
				'first_name',
				'last_name',
				'nickname',
			], 'filter', 'filter' => 'strip_tags'],

            [[
				'first_name',
				'last_name',
				'nickname',
				'comment',
			], 'trim'],

            [['last_active_at'], 'integer'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'nickname' => 'Nickname',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'verification_token' => 'Verification Token',
            'email' => 'Email',
            'status' => 'Status',
            'comment' => 'Comment', 
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_active_at' => 'Last Active At',
            'full_name' => 'Full Name'
        ];
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @param int $userType
     * @return static|null
     */
    public static function findByEmail($email, $userType = null)
    {
        $condition = [
            'email' => $email,
            'status' => self::STATUS_ACTIVE,
        ];
        if ($userType !== null) {
            $condition['type'] = $userType;
        }
        return static::findOne($condition);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


    /**
     * Gets query for [[ContactForms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContactForms()
    {
        return $this->hasMany(ContactForm::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[SportMatchResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSportMatchResults()
    {
        return $this->hasMany(SportMatchResult::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Transactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UserBets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserBets()
    {
        return $this->hasMany(UserBet::class, ['user_id' => 'id']);
    }


    /**
     * Get user bets with detailed information
     *
     * @return \yii\db\ActiveQuery
     */
     public function getUserBetsWithDetail()
    {
        $query = $this->getUserBets()
            ->select([
                'id',
                'amount',
                'odd as odd_value',
                'status',
                'odd_id',
                'created_at',
                new \yii\db\Expression("SUBSTRING(SHA2(CAST(id AS CHAR), 256), 1, 5) AS id_hash"),
            ])
            ->with([
                'oddObj' => function($oddQuery) {
                    $oddQuery->select(['id', 'odd', 'name', 'sport_match_id', 'odd_bet_type_id'])
                        ->with([
                            'oddBetType' => function($oddBetTypeQuery) {
                                $oddBetTypeQuery->select(['id', 'name']);
                            },
                            'sportMatch' => function($matchQuery) {
                                $matchQuery->select([
                                    'id',
                                    'name',
                                    'sport_id',
                                    'category_id',
                                    'home',
                                    'away',
                                    'match_start',
                                    // přidáme subselecty pro evaluated a result
                                    new \yii\db\Expression("
                                        (SELECT CASE WHEN evaluated = 1 THEN TRUE ELSE FALSE END
                                        FROM sport_match_result
                                        WHERE sport_match_result.sport_match_id = sport_match.id
                                        LIMIT 1
                                        ) AS evaluated
                                    "),
                                    new \yii\db\Expression("
                                        (SELECT CASE WHEN evaluated = 1 THEN result ELSE 0 END
                                        FROM sport_match_result
                                        WHERE sport_match_result.sport_match_id = sport_match.id
                                        LIMIT 1
                                        ) AS result
                                    "),
                                ])
                                ->with([
                                    'sport' => function($sportQuery) {
                                        $sportQuery->select(['id', 'name']);
                                    },
                                    'category' => function($categoryQuery) {
                                        $categoryQuery->select([
                                            'id',
                                            'name',
                                            'country_name',
                                            new \yii\db\Expression("(SELECT CASE WHEN COUNT(*) > 1 THEN TRUE ELSE FALSE END
                                                FROM category AS c2
                                                WHERE c2.name = category.name 
                                                AND c2.sport_id = category.sport_id
                                                AND c2.enabled = 1
                                            ) AS twice_enabled")
                                        ]);
                                    },
                                ]);
                            },
                        ]);
                },
            ]);

        return $query;
    }



    /**
     * Get user transaction list with detailed information
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserTransactionsWithDetail()
    {
        $query = $this->getTransactions()
            ->select([
                'id',
                'amount',
                'type',
                'action',
                'description',
                'user_bet_id',
                'created_at',
                new \yii\db\Expression("SUBSTRING(SHA2(CAST(user_bet_id AS CHAR), 256), 1, 5) AS user_bet_id_hash"),
            ]);

        return $query;
    }


    /**
     * Get user profile information
     *
     * @return array
     */
    public function getProfile() {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'created_at' => $this->created_at,
        ];
    }


    /**
     * Get user wallet
     *
     * @return array
     */
    public function getWallet() {
        return [
            'balance' => $this->getFundBalance(),
        ];
    }


    /**
     * Get the user's total fund balance based on transactions sum
     *
     * @return float
     */
    public function getFundBalance()
    {
        return (float) $this->getTransactions()->sum('amount');
    }


    /**
     * Check if user is of type 'public user'
     */
    public function isPublicUser(){
       return $this->type === User::USER_TYPE_PUBLIC;
    }


	/**
     * Get friendly name of the record for admin CRUD interface
	 * 
	 * @return string
	 */
	public function getFriendlyName(){
        $fullName = trim($this->first_name . ' ' . $this->last_name);
        if ($fullName !== '') {
            return sprintf('%s (%s)', $this->nickname, $fullName);
        }
        return $this->nickname;
	}


	/**
	 * Get current user
     * 
     * @return User|null
	 */
	public static function getCurrent()	{
		if (!Yii::$app->user->isGuest) {
			return self::find()->where(['id' => Yii::$app->user->id])->one();
		}
		return NULL;
	}	


	public function getStatusLabel(){
		$labels = self::getStatusLabels(false);
		if(array_key_exists($this->status, $labels)) {
			return $labels[$this->status];
		}
		return sprintf('Unknown (%d)', $this->status);
	}	


	public static function getStatusLabels(){
		return [
			self::STATUS_ACTIVE => Yii::t('app', 'user_status_active'),
			self::STATUS_INACTIVE => Yii::t('app', 'user_status_inactive'),
			self::STATUS_DELETED => Yii::t('app', 'user_status_deleted'),
		];
	}


    /**
     * Updates the user's last activity time
     *
     * @return bool whether the save was successful
     */
    public function updateLastActive() {
        $this->last_active_at = time();
        return $this->save(false, ['last_active_at']);
    }

}
