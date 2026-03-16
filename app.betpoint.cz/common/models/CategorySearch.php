<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Category;
use common\models\Sport;

class CategorySearch extends Category
{
    public $categoryName;
    public $sportName;
    public $vendor_id;

    public function rules()
    {
        return [
            [['id', 'sport_id', 'id_vendor', 'enabled', 'active_session', 'created_at', 'updated_at'], 'integer'],
            [['name', 'country_name', 'sportName', 'enabled'], 'safe'], 
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null)
    {
        $query = Category::find();
        $query->joinWith(['sport']); 

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['categoryName'] = [
            'asc' => ['category.name' => SORT_ASC],
            'desc' => ['category.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sportName'] = [
            'asc' => ['sport.name' => SORT_ASC],
            'desc' => ['sport.name' => SORT_DESC],
        ];

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'sport_id' => $this->sport_id,
            'enabled' => $this->enabled,
            'active_session' => $this->active_session,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id_vendor' => $this->id_vendor,
        ]);

        $query->andFilterWhere(['like', 'category.name', $this->name])
              ->andFilterWhere(['like', 'category.country_name', $this->country_name])
              ->andFilterWhere(['like', 'category.sport_id', $this->sportName])
              ->andFilterWhere(['like', 'category.id_vendor', $this->id_vendor]);  

        return $dataProvider;
    }
}
