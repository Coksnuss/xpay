<?php
namespace backend\models;

use yii\base\Model;
use Yii;
use common\models\Currency;

/**
 * Exchange rate form
 */
class ExchangeRateForm extends Model
{
	public $rate;
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
		['rate', 'number', 'min'=>0.01],
		['rate', 'required']
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
		'rate' => '',
		];
	}
	
	public function setExchangeRate() 
	{
		$currencyModel = Currency::findOne(['iso_4217_name'=>'USD']);
		$currencyModel->eur_exchange_rate = $this->rate; 
		//save with validation
		if($currencyModel->save(true)) {
			return true;
		} else {
			$this->addError('rate', 'Your input was not a valid number.');
			return false;
		}
	}
}