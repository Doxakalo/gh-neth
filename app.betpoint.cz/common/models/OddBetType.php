<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "odd_bet_type".
 *
 * @property int $id
 * @property int $id_vendor
 * @property string $name
 * @property int $enabled
 * @property int $sport_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Odd[] $odds
 * @property Sport $sport
 */
class OddBetType extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'odd_bet_type';
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
            [['enabled'], 'default', 'value' => 0],
            [['id_vendor', 'name', 'sport_id'], 'required'],
            [['id_vendor', 'enabled', 'sport_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['sport_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sport::class, 'targetAttribute' => ['sport_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_vendor' => 'Id Vendor',
            'name' => 'Name',
            'enabled' => 'Enabled',
            'sport_id' => 'Sport ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Odds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOdds()
    {
        return $this->hasMany(Odd::class, ['odd_bet_type_id' => 'id']);
    }

    /**
     * Gets query for [[Sport]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSport()
    {
        return $this->hasOne(Sport::class, ['id' => 'sport_id']);
    }


    public static function getOddBetTypesBySportId($sportId)
    {
        return static::find()->where(["sport_id" => $sportId])->asArray()->all();
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

    public static function getEnabledRecordsBySportId($sportId)
    {
        return static::find()->where(["sport_id" => $sportId, "enabled" => 1])->asArray()->all();
    }

    /*
     * Sets the odd bet type as disabled.
     * This method sets the enabled field to 0 and clears the alias.
     * It returns true on success.
     *
     * @return bool
     */
    public function setAsDisabled()
    {
        $this->enabled = 0;
        $this->rank = null;
        $this->alias = null;
        return true;
    }

    /*
    * Sets the odd bet type as enabled.
    * If an alias is found, it sets the enabled field to 1 and assigns the alias.
    * If no alias is found, it sets the enabled field to 0.
    *
    * @return bool
    */
    public function setAsEnabled($alias = null, $rank = 0)
    {
        // If alias is not set the bet type coundn't be enabled.
        $this->enabled = (!empty($alias) ? 1 : 0);
        $this->rank = $rank;
        $this->alias = $alias;
        
        return true;
    }
}
