<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "odd".
 *
 * @property int $id
 * @property string $name
 * @property float $odd_raw
 * @property float $odd
 * @property int $odd_bet_type_id_vendor
 * @property int $sport_match_id
 * @property int $updated_at
 * @property int $created_at
 *
 * @property OddBetType $oddBetTypeIdVendor
 * @property SportMatch $sportMatch
 * @property UserBet[] $userBets
 */
class Odd extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'odd';
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
            [['name', 'odd_raw', 'odd', 'sport_match_id', 'odd_bet_type_id'], 'required'],
            [['odd_raw', 'odd'], 'number'],
            [['sport_match_id', 'odd_bet_type_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['odd_bet_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => OddBetType::class, 'targetAttribute' => ['odd_bet_type_id' => 'id']],
            [['sport_match_id'], 'exist', 'skipOnError' => true, 'targetClass' => SportMatch::class, 'targetAttribute' => ['sport_match_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'odd_raw' => 'Odd Raw',
            'odd' => 'Odd',
            'sport_match_id' => 'Sport Match ID',
            'odd_bet_type_id' => 'Odd Bet Type ID',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[OddBetType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOddBetType()
    {
        return $this->hasOne(OddBetType::class, ['id' => 'odd_bet_type_id']);
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
     * Gets query for [[UserBets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserBets()
    {
        return $this->hasMany(UserBet::class, ['odd_id' => 'id']);
    }
    


    public static function getOddsByMatchIds($ids, $asArray = false)
    {
        $query = static::find()->where(["sport_match_id" => $ids]);
        if ($asArray) {
            $query->asArray();
        }
        return $query->all();
    }

    public static function batchUpdateRecordsById($data)
    {
        $db = Yii::$app->db;
        $sqlQueryString = "";
        foreach ($data as $row) {
            $id = $row["id"];
            unset($row["id"]);
            $sql = $db->createCommand()->update(static::tableName(), $row, ["id" => $id]);
            $sqlQueryString .= $sql->getRawSql() . ";";
        }

        $db->createCommand($sqlQueryString)->execute();
    }
}
