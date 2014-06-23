<?php

namespace common\models\base;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Transaction;

/**
 * TransactionSearch represents the model behind the search form about `common\models\Transaction`.
 */
class TransactionSearch extends Transaction
{
    public function rules()
    {
        return [
            [['id', 'account_id', 'associated_account_number', 'type', 'foreign_currency_id'], 'integer'],
            [['transaction_id', 'uuid', 'description', 'created_at', 'updated_at'], 'safe'],
            [['amount', 'foreign_currency_amount'], 'number'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Transaction::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'account_id' => $this->account_id,
            'associated_account_number' => $this->associated_account_number,
            'type' => $this->type,
            'amount' => $this->amount,
            'foreign_currency_amount' => $this->amount,
            'foreign_currency_id' => $this->foreign_currency_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'transaction_id', $this->transaction_id])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
