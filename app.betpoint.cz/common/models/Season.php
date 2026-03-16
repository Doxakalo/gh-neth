<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "season".
 *
 * @property int $id
 * @property string $year
 * @property int|null $current
 * @property int|null $odds
 * @property int $category_enabled
 * @property int $category_id
 * @property int $sport_id
 * @property int $active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Category $category
 * @property Sport $sport
 * @property SportMatch[] $sportMatches
 */
class Season extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'season';
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
            [['active'], 'default', 'value' => 0],
            [['year', 'category_id', 'sport_id'], 'required'],
            [['current', 'odds', 'category_enabled', 'category_id', 'sport_id', 'active'], 'integer'],
            [['year'], 'string', 'max' => 10],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'year' => 'Year',
            'current' => 'Current',
            'odds' => 'Odds',
            'category_enabled' => 'Category Enabled',
            'category_id' => 'Category ID',
            'sport_id' => 'Sport ID',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
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
        return $this->hasMany(SportMatch::class, ['season_id' => 'id']);
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

    public static function insertSeasons($data)
    {
        Yii::$app->db->createCommand()->batchInsert(static::tableName(), ["id", "year", "current", "odds", "active", "category_enabled", "category_id", "sport_id", "created_at", "updated_at"], $data)->execute();
    }


    public static function getSeasonsBySportId($sportId)
    {
        return static::find()->where(["sport_id" => $sportId])->asArray()->all();
    }

    public static function getSeasonsByCategoryId($categoryId)
    {
        return static::find()->where(["category_id" => $categoryId])->asArray()->all();
    }

    public static function getOneSeasonsByCategoryId($categoryId)
    {
        return static::find()
            ->where(["category_id" => $categoryId])
            ->asArray()
            ->one();
    }
    public static function getSeasons()
    {
        return static::find()->asArray()->all();
    }

    public static function getEnabledActiveSeasonBySportId($sportId)
    {
        return static::find()->where(["sport_id" => $sportId, "active" => 1, "category_enabled" => 1])->all();
    }

    public function setAsDisabled()
    {
        $this->category_enabled = 0;

        return true;
    }

    public function setAsEnabled()
    {
        // If alias is not set the bet type coundn't be enabled.
        $this->category_enabled = 1;

        return true;
    }

}