<?php

namespace common\models;

use common\services\BaseSport;
use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "sport_match".
 *
 * @property int $id
 * @property int $id_vendor
 * @property string $name
 * @property int|null $match_start
 * @property string $home
 * @property string $away
 * @property int $evaluated
 * @property int $in_progress 
 * @property string $status
 * @property string $status_name
 * @property string $detail
 * @property int $extra
 * @property int $category_id
 * @property int $sport_id
 * @property int $season_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Category $category
 * @property Odd[] $odds
 * @property Season $season
 * @property Sport $sport
 * @property SportMatchResult[] $sportMatchResults
 */
class SportMatch extends \yii\db\ActiveRecord
{

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
    public static function tableName()
    {
        return 'sport_match';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['match_start'], 'default', 'value' => null],
            [['in_progress'], 'default', 'value' => 0],
            [['id_vendor', 'name', 'home', 'away', 'status', 'status_name', 'detail', 'extra', 'category_id', 'sport_id', 'season_id'], 'required'],
            [['id_vendor', 'match_start', 'evaluated', 'in_progress', 'extra', 'category_id', 'sport_id', 'season_id'], 'integer'],
            [['detail'], 'safe'],
            [['name', 'home', 'away'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 5],
            [['status_name'], 'string', 'max' => 100],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season::class, 'targetAttribute' => ['season_id' => 'id']],
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
            'match_start' => 'Match Start',
            'home' => 'Home',
            'away' => 'Away',
            'evaluated' => 'Evaluated',
            'in_progress' => 'In Progress',
            'status' => 'Status',
            'status_name' => 'Status Name',
            'detail' => 'Detail',
            'extra' => 'Extra',
            'category_id' => 'Category ID',
            'sport_id' => 'Sport ID',
            'season_id' => 'Season ID',
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
     * Gets query for [[Odds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOdds()
    {
        return $this->hasMany(Odd::class, ['sport_match_id' => 'id']);
    }

    /**
     * Gets query for [[Season]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSeason()
    {
        return $this->hasOne(Season::class, ['id' => 'season_id']);
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
     * Gets query for [[SportMatchResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSportMatchResults()
    {
        return $this->hasMany(SportMatchResult::class, ['sport_match_id' => 'id']);
    }


    /* 
     * Cancels all user bets for this match.
     * This method finds the match by its ID, retrieves all user bets that are pending,
     * and processes them by creating return transactions and updating the bet status to cancelled.
     * Match will be set as evaluated after processing all bets.
     */
    public function cancelUserBets(){

        $query = static::find()->where(["id" => $this->id]);

        $query->with([
            'odds.userBets' => function ($query) {
                $query->andWhere(['status' => UserBet::STATUS_PENDING]);
            }
        ]);

        $match = $query->one();

        $transaction = \Yii::$app->db->beginTransaction();

        foreach($match->odds as $odd){
            foreach($odd->userBets as $userBet){

                $returnTransaction = new Transaction();
                $returnTransaction->user_id = $userBet->user_id;
                $returnTransaction->type = Transaction::TYPE_RETURN;
                $returnTransaction->amount = $userBet->amount;
                $returnTransaction->user_bet_id = $userBet->id;
                $returnTransaction->match_result_id = null;
                $returnTransaction->setActionLabel();
                $returnTransaction->setDescriptionLabel();

                if (!($returnTransaction->validate() && $returnTransaction->save())) {
                    $transaction->rollBack();
                    throw new Exception(json_encode($returnTransaction->getErrors()));
                }

                $userBet->status = UserBet::STATUS_CANCELLED;

                if (!($userBet->validate() && $userBet->save())) {
                    $transaction->rollBack();
                    throw new Exception(json_encode($userBet->getErrors()));
                }
            }
        }

        $match->evaluated = 1;
        if (!($match->validate() && $match->save())) {
            $transaction->rollBack();
            throw new Exception(json_encode($match->getErrors()));
        }

        $transaction->commit();
    }

    /**
     * Returns the list of matches by sport ID.
     *
     * @param int $sportId
     * @param bool $asArray Whether to return results as an array
     * @return SportMatch[]|array
     */
    public static function getBetMatchesIdBySportIdGroupByVendorId($sportId, $asArray = false)
    {
        
        $query = static::find()->where(["sport_id" => $sportId])->andWhere(['>', 'match_start', time()])->select(["id", "id_vendor"])->indexBy("id_vendor");
        if ($asArray) {
            $query->asArray();
        }
        return $query->all();
    }

    /*     
     * Returns the list of future matches by sport ID, grouped by vendor ID.
     * This method filters matches that are scheduled to start in the future and are associated with active seasons.
     *
     * @param int $sportId
     * @param bool $asArray Whether to return results as an array
     * @return SportMatch[]|array
     */
    public static function getFutureMatchesIdBySportIdGroupByVendorIdActiveSeasons($sportId, $asArray = false)
    {
        $query = static::find()->where(["sport_id" => $sportId])->andWhere(['>', 'match_start', time()])->select(["id", "id_vendor"])
            ->with(['season' => function ($query) {
                $query->andWhere(['active' => 1]);
            }])
            ->with(['category' => function ($query) {
                $query->andWhere(['enabled' => 1]);
            }])
            ->indexBy("id_vendor");

        if ($asArray) {
            $query->asArray();
        }
        return $query->all();
    }



    /**
     * Returns the list of matches by category ID.
     *
     * @param int $categoryId
     * @param bool $asArray Whether to return results as an array
     * @return SportMatch[]|array
     */
    public static function getMatchesByCategoryId($categoryId, $asArray = false)
    {
        $query = static::find()->where(["category_id" => $categoryId]);
        if ($asArray) {
            $query->asArray();
        }
        return $query->all();
    }

    /* 
     * Returns the list of matches by sport ID.
     *
     * @param int $sportId
     * @param bool $asArray Whether to return results as an array
     * @return SportMatch[]|array
     */
    public static function getSportMatchesBySportIds($sportId, $sportIds ,$asArray = false)
    {
        $query = static::find()->where(["sport_id" => $sportId, "id_vendor" => $sportIds]);
        if ($asArray) {
            $query->asArray();
        }
        return $query->all();
    }

    /**
     * Returns the list of future matches with their odds and bet types.
     * This method preloads odds and bet types to optimize performance.
     * 
     * @param int $categoryId
     * @return array
     */
    public static function getFutureMatchesWithOdds($categoryId)
    {
        $inactiveStatuses = self::getInactiveStatusesByCategoryId($categoryId);
        $now = time();

        // Get matches with preloaded odds and bet types
        $matches = SportMatch::find()
            ->where(['category_id' => $categoryId])
            ->andWhere(['>=', 'match_start', strtotime('today')])
            ->andWhere(['not in', 'status', $inactiveStatuses])
            ->andWhere(['exists', (new \yii\db\Query())
                ->select('*')
                ->from('odd')
                ->where('sport_match.id = odd.sport_match_id')
            ])
            ->with([
                'odds.oddBetType' => function ($query) {
                    $query->andWhere(['enabled' => 1]);
                }
            ])
            ->orderBy(['match_start' => SORT_ASC])
            ->all();
            
        // Organize data efficiently
        $matchesArr = [];
        foreach ($matches as $match) {
            $oddGroups = [];
            $groupedOdds = [];
            
            // Group odds by bet type ID
            foreach ($match->odds as $odd) {
                $typeId = $odd->oddBetType->id;
                $groupedOdds[$typeId][] = [
                    'id' => $odd->id,
                    'name' => $odd->name,
                    'value' => $odd->odd,
                    'updated_at' => $odd->updated_at,
                ];
            }
            
            // Build odd_groups structure
            foreach ($match->getRelatedRecords()['odds'] as $odd) {
                $typeId = $odd->oddBetType->id;
                
                // Ensure each bet type is only added once
                if (!isset($oddGroups[$typeId])) {
                    $oddGroups[$typeId] = [
                        'id' => $typeId,
                        'name' => $odd->oddBetType->name,
                        'alias' => $odd->oddBetType->alias,
                        'rank' => $odd->oddBetType->rank,
                        'odds' => $groupedOdds[$typeId] ?? []
                    ];
                }
            }
            
            // Convert to sequential array
            $oddGroups = array_values($oddGroups);
            // Sort oddGroups by 'rank' ascending
            usort($oddGroups, function($a, $b) {
                return $a['rank'] <=> $b['rank'];
            });

            // check progress also by match_start in case in_progress is not yet set by cron
            $matchInProgress = intval($match->in_progress) === 1 || $match->match_start <= $now;
            
            $matchesArr[] = [
                'id' => $match->id,
                'sport_id' => $match->sport_id,
                'category_id' => $match->category_id,
                'name' => $match->name,
                'home' => $match->home,
                'away' => $match->away,
                'match_start' => $match->match_start,
                'in_progress' => $matchInProgress,
                'odd_groups' => $oddGroups
            ];
        }
        
        return $matchesArr;
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


    /**
     * Returns the list of inactive statuses for a given category ID.
     * This method retrieves the sport by category and then gets the inactive statuses for that sport.
     *
     * @param int $categoryId
     * @return array
     */
    public static function getInactiveStatusesByCategoryId($categoryId)
    {
        // get sport by category
        $category = Category::find()
            ->with('sport')
            ->where(['id' => $categoryId])->one();

        if (!$category || !$category->sport) {
            return [];
        }

        // get inactive statuses for the sport
        $sportClass = BaseSport::getSportClassByAlias($category->sport->alias);
        if (!$sportClass) {
            return [];
        }
        $statuses = $sportClass::getInactiveStatuses();

        return $statuses;
    }


    /**
     * Returns the current match result for this sport match.
     * It retrieves the latest result based on the created_at timestamp.
     *
     * @return SportMatchResult|null
     */
    public function getCurrentSportMatchResult() {
        return $this->getSportMatchResults()
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }


    /**
     * Get friendly name of the record for admin CRUD interface
     * 
     * @return string
     */
	public function getFriendlyName(){
        return $this->name;
	}


    /**
     * Check if the match is currently in progress.
     * 
     * @return bool
     */
    public function isInProgress() {
        return intval($this->in_progress) === 1 || ($this->match_start <= time() && $this->isActive());
    }


    /**
     * Check if the match is active based on its status.
     * 
     * @return bool
     */
    public function isActive(){
        // get inactive statuses for the sport
        $sportClass = BaseSport::getSportClassByAlias($this->sport->alias);
        if (!$sportClass) {
            return false; 
        }
        $statuses = $sportClass::getInactiveStatuses();
        if(in_array($this->status, $statuses)){
            return false;
        }
        return true;
    }

    public static function clearAndValidateDetail($detail)
    {
        // získáme volající informace
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1] ?? null; // volající

        $callerInfo = [
            'class' => $caller['class'] ?? 'global',
            'method' => $caller['function'] ?? 'global',
            'file' => $caller['file'] ?? 'unknown',
            'line' => $caller['line'] ?? 'unknown',
        ];

        if (is_array($detail)) {
            return $detail;
        } else {
            $decoded = json_decode($detail, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Yii::error([
                    "message" => "DETAIL - ERROR: Invalid JSON format (1)",
                    "data" => $detail,
                    "called_from" => $callerInfo,
                ], 'data-sync');

                return $decoded;
            } else {
                Yii::error([
                    "message" => "DETAIL - ERROR: Invalid JSON format (2)",
                    "data" => $detail,
                    "called_from" => $callerInfo,
                ], 'data-sync');
                return [];
            }
        }
    }
}
