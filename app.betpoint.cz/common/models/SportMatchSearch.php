<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SportMatch;

/**
 * SportMatchSearch represents the model behind the search form of `common\models\SportMatch`.
 */
class SportMatchSearch extends SportMatch
{
    public $sportName;
    public $categoryName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_vendor', 'match_start', 'evaluated', 'in_progress', 'extra', 'category_id', 'sport_id', 'season_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'home', 'away', 'status', 'status_name', 'detail', 'sportName', 'categoryName'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = SportMatch::find();
        $query->joinWith(['sport', 'category']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Enable sorting for related columns
        $dataProvider->sort->attributes['sportName'] = [
            'asc' => ['sport.name' => SORT_ASC],
            'desc' => ['sport.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['categoryName'] = [
            'asc' => ['category.name' => SORT_ASC],
            'desc' => ['category.name' => SORT_DESC],
        ];

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'sport_match.id' => $this->id,
            'sport_match.id_vendor' => $this->id_vendor,
            'sport_match.match_start' => $this->match_start,
            'sport_match.evaluated' => $this->evaluated,
            'sport_match.in_progress' => $this->in_progress,
            'sport_match.extra' => $this->extra,
            'sport_match.category_id' => $this->category_id,
            'sport_match.sport_id' => $this->sport_id,
            'sport_match.season_id' => $this->season_id,
            'sport_match.created_at' => $this->created_at,
            'sport_match.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'sport_match.name', $this->name])
            ->andFilterWhere(['like', 'sport_match.home', $this->home])
            ->andFilterWhere(['like', 'sport_match.away', $this->away])
            ->andFilterWhere(['like', 'sport_match.status', $this->status])
            ->andFilterWhere(['like', 'sport_match.status_name', $this->status_name])
            ->andFilterWhere(['like', 'sport_match.detail', $this->detail])
            ->andFilterWhere(['like', 'sport.name', $this->sportName])
            ->andFilterWhere(['like', 'category.name', $this->categoryName]);

        return $dataProvider;
    }
}
