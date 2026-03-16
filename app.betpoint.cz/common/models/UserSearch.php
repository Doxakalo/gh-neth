<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{

    public $full_name;
    public $fundBalance;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'type', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'last_name', 'full_name', 'nickname', 'auth_key', 'password_hash', 'password_reset_token', 'verification_token', 'email'], 'safe'],
            [['fundBalance'], 'number'],
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
        $query = User::find();

        // Join with transaction table, calculate sum, and group by user
        $query->select([
                'user.*',
                'fundBalance' => 'COALESCE(SUM(transaction.amount), 0)'
            ])
            ->leftJoin('transaction', 'user.id = transaction.user_id')
            ->groupBy('user.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Add fundBalance virtual attribute to the sort configuration.
        $dataProvider->sort->attributes['fundBalance'] = [
            'asc' => ['fundBalance' => SORT_ASC],
            'desc' => ['fundBalance' => SORT_DESC],
        ];

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions (prefixed with 'user' to resolve ambiguity)
        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.status' => $this->status,
            'user.type' => $this->type, // This is the ambiguous column, now fixed.
            'user.created_at' => $this->created_at,
            'user.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'user.first_name', $this->first_name])
            ->andFilterWhere(['like', 'user.last_name', $this->last_name])
            ->andFilterWhere(['like', 'user.nickname', $this->nickname])
            ->andFilterWhere(['like', 'user.auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'user.password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'user.password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'user.verification_token', $this->verification_token])
            ->andFilterWhere(['like', 'user.email', $this->email]);

        // Search by full name - concatenating first and last name
        if (!empty($this->full_name)) {
            $query->andWhere([
                'or',
                ['like', 'user.first_name', $this->full_name],
                ['like', 'user.last_name', $this->full_name],
                ['like', "CONCAT(first_name, ' ', last_name)", $this->full_name],
            ]);
        }

        // Filter by the virtual fundBalance attribute using HAVING
        if ($this->fundBalance !== null && $this->fundBalance !== '') {
            $query->andHaving(['=', 'fundBalance', $this->fundBalance]);
        }

        return $dataProvider;
    }
}
