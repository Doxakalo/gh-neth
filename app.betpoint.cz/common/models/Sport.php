<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\services\BaseSport;

/**
 * This is the model class for table "sport".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Category[] $categories
 * @property OddBetType[] $oddBetTypes
 * @property SportMatch[] $sportMatches
 */
class Sport extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sport';
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
            [['name', 'alias'], 'required'],
            [['name', 'alias'], 'string', 'max' => 128],
            [['name'], 'unique'],
            [['alias'], 'unique'],
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
            'alias' => 'Alias',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getAllSports(){
        return static::find()->asArray()->all();
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['sport_id' => 'id']);
    }

    /**
     * Gets query for [[OddBetTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOddBetTypes()
    {
        return $this->hasMany(OddBetType::class, ['sport_id' => 'id']);
    }

    /**
     * Gets query for [[SportMatches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSportMatches()
    {
        return $this->hasMany(SportMatch::class, ['extra' => 'id']);
    }


    /**
     * Get all sports with their categories.
     *
     * @return array
     */
    public static function getAllSportsWithCategories() {
        // Get sports with enabled categories
        $sports = static::find()
            ->with(['categories' => function ($query) {
                $query->andWhere(['enabled' => 1])->orderBy(['name' => SORT_ASC]);
            }])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $categoryIds = [];
        $sportInactiveStatuses = [];

        // Collect category IDs and sport inactive statuses
        foreach ($sports as $sport) {
            $inactiveStatuses = $sport->getInactiveStatuses();
            $sportInactiveStatuses[$sport->id] = $inactiveStatuses;

            foreach ($sport->categories as $category) {
                $categoryIds[] = $category->id;
            }
        }

        $matchCounts = [];
        if ($categoryIds) {
            // Build dynamic OR conditions per sport
            $orConditions = ['or'];
            foreach ($sportInactiveStatuses as $sportId => $inactiveStatuses) {
                if (empty($inactiveStatuses)) {
                    // No inactive statuses - only filter by sport ID
                    $orConditions[] = ['sport_match.sport_id' => $sportId];
                } else {
                    // Filter by sport ID and status exclusion
                    $orConditions[] = [
                        'and',
                        ['sport_match.sport_id' => $sportId],
                        ['not in', 'sport_match.status', $inactiveStatuses]
                    ];
                }
            }

            // Get match counts with dynamic filtering
            $rows = \common\models\SportMatch::find()
                ->select([
                    'category_id',
                    'count' => 'COUNT(DISTINCT sport_match.id)'
                ])
                ->innerJoin('odd', 'sport_match.id = odd.sport_match_id')
                ->where(['sport_match.category_id' => $categoryIds])
                ->andWhere(['>=', 'sport_match.match_start', strtotime('today')])
                ->andWhere($orConditions) // Apply sport-specific filters
                ->groupBy('sport_match.category_id')
                ->asArray()
                ->all();

            foreach ($rows as $row) {
                $matchCounts[$row['category_id']] = (int)$row['count'];
            }
        }

        // Build result structure
        $result = [];

        foreach ($sports as $sport) {
            // total counting of names
            $nameCounts = [];
            foreach ($sport->categories as $category) {
                if ($category->enabled) {
                    $nameCounts[$category->name] = ($nameCounts[$category->name] ?? 0) + 1;
                }
            }

            $result[] = [
                'id' => $sport->id,
                'name' => $sport->name,
                'alias' => $sport->alias,
                'categories' => array_values(array_filter(array_map(function ($category) use ($matchCounts, $nameCounts) {
                    $matchCount = $matchCounts[$category->id] ?? 0;
                    if ($matchCount === 0) {
                        return null;
                    }

                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'country_name' => $category->country_name,
                        'enabled' => $category->enabled,
                        'sport_id' => $category->sport_id,
                        'match_count' => $matchCount,
                        'twice_enabled' => ($category->enabled && ($nameCounts[$category->name] > 1)), // jen pro aktivní
                    ];
                }, $sport->categories)))
            ];
        }

        return $result;
    }


    /**
     * Returns the list of inactive statuses for Sport instance
     *
     * @param int $categoryId
     * @return array
     */
    public function getInactiveStatuses()
    {
        $sportClass = BaseSport::getSportClassByAlias($this->alias);
        if (!$sportClass) {
            return [];
        }
        $statuses = $sportClass::getInactiveStatuses();

        return $statuses;
    }

    public static function findByAlias(string $alias): ?self
    {
        return static::find()
            ->where(['alias' => $alias])
            ->one();
    }
}
