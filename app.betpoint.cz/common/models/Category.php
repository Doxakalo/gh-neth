<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property int $id_vendor
 * @property string $name
 * @property string $country_name
 * @property string|null $logo_url
 * @property int $enabled
 * @property int $active_session
 * @property int $sport_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Season[] $seasons
 * @property Sport $sport
 * @property SportMatch[] $sportMatches
 */
class Category extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
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
            [['logo_url'], 'default', 'value' => null],
            [['active_session'], 'default', 'value' => 0],
            [['id_vendor', 'name', 'country_name', 'sport_id'], 'required'],
            [['id_vendor', 'enabled', 'active_session', 'sport_id'], 'integer'],
            [['name'], 'string', 'max' => 150],
            [['country_name'], 'string', 'max' => 50],
            [['logo_url'], 'string', 'max' => 255],
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
            'country_name' => 'Country Name',
            'logo_url' => 'Logo Url',
            'enabled' => 'Enabled',
            'active_session' => 'Active Session',
            'sport_id' => 'Sport ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function batchUpdateRecordsById($data){
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

    public static function getCategoriesBySportId($sportId)
    {
        return static::find()->where(["sport_id" => $sportId])->asArray()->all();
    }


    public static function getEnablesCategoriesBySportId($sportId)
    {
        return static::find()->where(["sport_id" => $sportId, "enabled" => 1])->asArray()->all();
    }

    /**
     * Gets query for [[Seasons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeasons()
    {
        return $this->hasMany(Season::class, ['category_id' => 'id']);
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

    /**
     * Gets query for [[SportMatches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSportMatches()
    {
        return $this->hasMany(SportMatch::class, ['category_id' => 'id']);
    }

    /**
     * Sets the category as disabled.
     */
    public function setAsDisabled()
    {
        $this->enabled = 0;

        return true;
    }

    /**
     * Sets the category as enabled.
     */
    public function setAsEnabled()
    {
        // If alias is not set the bet type coundn't be enabled.
        $this->enabled = 1;

        return true;
    }
}