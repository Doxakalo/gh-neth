<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\services\BaseSport;


/**
 * This is the model class for table "sport_match_result".
 *
 * @property int $id
 * @property string|null $result_vendor JSON, data from API
 * @property string $result JSON, {"home": <home_score>, "away": <away_score>}
 * @property int|null $evaluated
 * @property int $sport_match_id
 * @property int|null $user_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property SportMatch $sportMatch
 * @property Transaction[] $transactions
 * @property User $user
 * @property UserBet[] $userBets
 */
class SportMatchResult extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sport_match_result';
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
            [['result_vendor', 'user_id'], 'default', 'value' => null],
            [['evaluated'], 'default', 'value' => 0],
            [['result_vendor', 'result'], 'safe'],
            [['result', 'sport_match_id'], 'required'],
            [['evaluated', 'sport_match_id', 'user_id'], 'integer'],
            [['sport_match_id'], 'exist', 'skipOnError' => true, 'targetClass' => SportMatch::class, 'targetAttribute' => ['sport_match_id' => 'id']],
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
            'result_vendor' => 'Result Vendor',
            'result' => 'Result',
            'evaluated' => 'Evaluated',
            'sport_match_id' => 'Sport Match ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[SportMatch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSportMatch()
    {
        return $this->hasOne(SportMatch::class, ['id' => 'sport_match_id']);
    }

    /**
     * Gets query for [[Transactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['match_result_id' => 'id']);
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
     * Gets query for [[UserBets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserBets()
    {
        return $this->hasMany(UserBet::class, ['match_result_id' => 'id']);
    }


    /**
     * Sets the result values for home and away teams
     *
     * @param int $home Home team score
     * @param int $away Away team score
     */
    public function setResultValues(int $home, int $away){
        $data = [
            BaseSport::TEAM_HOME_ALIAS => $home,
            BaseSport::TEAM_AWAY_ALIAS => $away,
        ];
        $this->result = $data;
    }


    /**
     * Get the result values as array
     *
     * @return array
     */
    public function getResultValues(){
        // passs the result array directly since the data is stored in JSON field
        return $this->result; 
    }


    public static function getNotEvaluatedMatchResults($asArray = false)
    {
        $query = static::find()->where(["evaluated" => 0]);
           //->andWhere(['exists', Odd::find()->where('odd.sport_match_id = sport_match_result.sport_match_id')]);

        $query->with([
            'sportMatch',
            'sportMatch.odds',
            'sportMatch.odds.oddBetType',
            'sportMatch.odds.userBets'
        ]);
        if ($asArray) {
            $query->asArray();
        }
        return $query->all();
    }
}
