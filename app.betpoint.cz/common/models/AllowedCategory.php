<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "allowed_categories".
 *
 * @property int $id
 * @property int|null $id_vendor
 * @property int $sport_id
 * @property string $sport
 * @property string|null $country_name
 */
class AllowedCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'allowed_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sport_id', 'sport'], 'required'],
            [['id_vendor', 'sport_id'], 'integer'],
            [['country_name'], 'string'],
            [['sport'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_vendor' => 'Vendor ID',
            'sport_id' => 'Sport ID',
            'sport' => 'Sport Name',
            'country_name' => 'Country Name',
        ];
    }

    /**
     * Get categories by sport_id
     *
     * @param int $sportId
     * @return Category[]
     */
    public static function getCategoriesBySportId($sportId)
    {
        return static::find()->where(['sport_id' => $sportId])->all();
    }
}
