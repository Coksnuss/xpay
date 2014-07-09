<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountStatementSearch;

/**
 * AccountStatementSearch represents the model behind the search form about `common\models\AccountStatement`.
 */
class UserAccountStatementSearch extends \common\models\AccountStatementSearch
{
	/**
	 * 
	 * @param unknown $params
	 */
	public function search($params)
	{
		$dataProvider = parent::search($params);
		
		$dataProvider->query->andFilterWhere(['account_id'=>Yii::$app->user->identity->id]);
    	
		return $dataProvider;
	}
}
