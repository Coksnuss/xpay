<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Transaction;
/**
 * TransactionSearch represents the model behind the search form about `common\models\Transaction`.
 */
class OverviewTransactionSearch extends \common\models\TransactionSearch
{
	
	public function scenarios(){
		return parent::scenarios();
		
	}
	
	public function search($params,$type=null)
    {
		$query = Transaction::find();
    	
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			]);
    	
    	$query->andFilterWhere(['account_id'=>Yii::$app->user->identity->id]);
    	
    	if (!($this->load($params) && $this->validate())) {
    		return $dataProvider;
    	}
    	
    	$query->andFilterWhere([
    			'id' => $this->id,
    			'account_id' => $this->account_id,
    			'type' => $this->type,
    			'foreign_currency_id' => $this->foreign_currency_id,
    			'updated_at' => $this->updated_at,
    			]);
    	
    	$query->andFilterWhere(['like', 'transaction_id', $this->transaction_id]);
    	$query->andFilterWhere(['like', 'uuid', $this->uuid]);
    	$query->andFilterWhere(['like', 'description', $this->description]);
    	$query->andFilterWhere(['like','created_at',$this->created_at]);
    	$query->andFilterWhere(['like','foreign_currency_amount',$this->amount]);
    	$query->andFilterWhere(['like','associated_account_number',$this->associated_account_number]);
    	 
        return $dataProvider;
    }
}
