<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property float $amount
 * @property string $action
 * @property string $description
 * @property int $type
 * @property int|null $match_result_id
 * @property int|null $user_bet_id
 * @property int $user_id
 * @property int $created_at
 *
 * @property SportMatchResult $matchResult
 * @property User $user
 * @property UserBet $userBet
 */
class Transaction extends \yii\db\ActiveRecord
{

    const TYPE_INITIAL_CREDIT = 10;
    const TYPE_UPDATE_CREDIT = 11;
    const TYPE_BET = 20;
    const TYPE_WIN = 30;
    const TYPE_REEVALUATION = 40;
    const TYPE_RETURN = 50; // In case that match is cancelled or postponed


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['match_result_id', 'user_bet_id'], 'default', 'value' => null],
            [['amount', 'action', 'type', 'user_id'], 'required'],
            [['amount'], 'number'],
            [['type', 'match_result_id', 'user_bet_id', 'user_id'], 'integer'],

            ['type', 'in', 'range' => [
                self::TYPE_INITIAL_CREDIT, 
                self::TYPE_UPDATE_CREDIT, 
                self::TYPE_BET,
                self::TYPE_WIN,
                self::TYPE_REEVALUATION,
                self::TYPE_RETURN
            ]],

            [['action', 'description'], 'string', 'max' => 255],
            [['match_result_id'], 'exist', 'skipOnError' => true, 'targetClass' => SportMatchResult::class, 'targetAttribute' => ['match_result_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['user_bet_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserBet::class, 'targetAttribute' => ['user_bet_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'Amount',
            'action' => 'Action',
            'description' => 'Description',
            'type' => 'Type',
            'match_result_id' => 'Match Result ID',
            'user_bet_id' => 'User Bet ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[MatchResult]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMatchResult()
    {
        return $this->hasOne(SportMatchResult::class, ['id' => 'match_result_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[UserBet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserBet()
    {
        return $this->hasOne(UserBet::class, ['id' => 'user_bet_id']);
    }

    public static function getLastTransactionBetReleated($userBetId){
        $query = Transaction::find()
            ->where(['user_bet_id' => $userBetId])
            ->andWhere(['type' => [self::TYPE_REEVALUATION, self::TYPE_WIN]])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1);

        return $query->one();
    }


    /**
     * Sets the default action based on the transaction type.
     *
     * @return bool True if the action was set, false otherwise.
     */
	public function setActionLabel(){
        $actions = self::getActionsLabels();
        if(isset($this->type) && array_key_exists($this->type, $actions)) {
            $this->action = $actions[$this->type];
            return true;
        }
        return false;
    }


    /**
     * Returns an array of default actions for each transaction type.
     *
     * @return array
     */
	public static function getActionsLabels(){
		return [
            self::TYPE_INITIAL_CREDIT => Yii::t('common', 'transaction_action_initial_credit'),
            self::TYPE_UPDATE_CREDIT => Yii::t('common', 'transaction_action_update_credit'),
            self::TYPE_BET => Yii::t('common', 'transaction_action_place_bet'),
            self::TYPE_WIN => Yii::t('common', 'transaction_action_win_bet'),
            self::TYPE_REEVALUATION => Yii::t('common', 'transaction_action_reevaluation_bet'),
            self::TYPE_RETURN => Yii::t('common', 'transaction_action_return_bet'),
		];
	}


    /**
     * Sets the default action based on the transaction type.
     *
     * @return bool True if the action was set, false otherwise.
     */
    public function setDescriptionLabel() {
        switch ($this->type) {
            case self::TYPE_BET:
            case self::TYPE_WIN:
            case self::TYPE_REEVALUATION:
            case self::TYPE_RETURN:
                if ($this->userBet) {
                    $this->description = $this->userBet->getDescriptionLabel();
                    return true;
                }
                break;
        }

        return false;
    }

}
