<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Links;

/**
 * LinksTypesSearch represents the model behind the search form of `app\models\Links`.
 */
class LinksTypesSearch extends Links
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'limit', 'hits'], 'integer'],
            [['long_url', 'short_code', 'lifetime', 'created_dt', 'updated_dt'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Links::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'limit' => $this->limit,
            'hits' => $this->hits,
            'lifetime' => $this->lifetime,
            'created_dt' => $this->created_dt,
            'updated_dt' => $this->updated_dt,
        ]);

        $query->andFilterWhere(['like', 'long_url', $this->long_url])
            ->andFilterWhere(['like', 'short_code', $this->short_code]);

        return $dataProvider;
    }
}
