<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_bet".
 *
 * @property int $id
 * @property float $amount
 * @property float $odd
 * @property int $status
 * @property int $odd_id
 * @property int|null $match_result_id
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property SportMatchResult $matchResult
 * @property Odd $oddObj
 * @property Transaction[] $transactions
 * @property User $user
 */
class UserBet extends \yii\db\ActiveRecord
{

    const STATUS_PENDING = 0;
    const STATUS_WIN = 20;
    const STATUS_LOSS = 30;
    const STATUS_CANCELLED = 40;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_bet';
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
            [['match_result_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 0],
            [['amount', 'odd', 'odd_id', 'user_id'], 'required'],
            [['amount', 'odd'], 'number'],
            [['status', 'odd_id', 'match_result_id', 'user_id'], 'integer'],

            ['status', 'in', 'range' => [
                self::STATUS_PENDING, 
                self::STATUS_WIN,
                self::STATUS_LOSS,
                self::STATUS_CANCELLED,
            ]],
            ['status', 'default', 'value' => self::STATUS_PENDING],

            [['match_result_id'], 'exist', 'skipOnError' => true, 'targetClass' => SportMatchResult::class, 'targetAttribute' => ['match_result_id' => 'id']],
            [['odd_id'], 'exist', 'skipOnError' => true, 'targetClass' => Odd::class, 'targetAttribute' => ['odd_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'odd' => 'Odd',
            'status' => 'Status',
            'odd_id' => 'Odd ID',
            'match_result_id' => 'Match Result ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[Odd]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOddObj()
    {
        return $this->hasOne(Odd::class, ['id' => 'odd_id']);
    }

    /**
     * Gets query for [[Transactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['user_bet_id' => 'id']);
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
     * Gets description label for the bet.
     *
     * @return string
     */
    public function getDescriptionLabel() {
        if (
            $this->oddObj &&
            $this->oddObj->sportMatch &&
            $this->oddObj->oddBetType
        ) {
            return sprintf(
                '%s | %s - %s | Odd: %s',
                $this->oddObj->sportMatch->name,
                $this->oddObj->oddBetType->name,
                $this->oddObj->name,
                $this->odd
            );
        }
        return '';
    }

}
