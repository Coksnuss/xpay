<?php
namespace frontend\models;

use yii\base\Model;
use Yii;
use yii\helpers\Html;

use common\models\Currency;
use common\models\Account;
use common\models\User;
use common\models\Shop;
use common\models\ShopBlacklist;

/**
 * Signup form
 */
class BlacklistForm extends Model
{
    public $user_id;
    public $shop1;
    public $shop2;
    public $shop3;
    
    /**
     * (non-PHPdoc)
     * @see \yii\base\Model::attributeLabels()
     */
    public function attributeLabels(){
    	$tmp = [];
    	for ($i = 1; $i <= count(Shop::find()->all()); $i++) {
    		$tmp['shop'.$i.'']=Shop::findOne(['id'=>$i])->name;
		}

    	return $tmp+parent::attributeLabels();
    }
    
    public function setUser(){
    	$this->user_id=Yii::$app->user->identity->id;
    	$user = User::findOne(['id'=>$this->user_id]);
    	 
    	$blacklistedShops = $user->blacklistedShops;
    	
    	foreach($blacklistedShops as $shop){
    		switch ($shop->id){
    			case 1: $this->shop1=1;
    					break;
    			case 2: $this->shop2=1;
    					break;
    			case 3: $this->shop3=1;
    					break;
    		}
    	}
    }
    
    public function save(){
    	$user = User::findOne(['id'=>Yii::$app->user->identity->id]);
    	$blacklistedShops = $user->blacklistedShops;
    	 
    	foreach($blacklistedShops as $shop){
    		switch ($shop->id){
    			case 1: if (!$this->shop1)
    						$user->unlink('blacklistedShops',$shop,true);
    					else
    						$this->shop1 = 0;
    					break;
    			case 2: if (!$this->shop2)
    						$user->unlink('blacklistedShops',$shop,true);
    					else
    						$this->shop2 = 0;
    					break;
    			case 3: if (!$this->shop3)
    						$user->unlink('blacklistedShops',$shop,true);
    					else
    						$this->shop3 = 0;
    					break;
    		}
    	}
    	if ($this->shop1){
    		$shop = Shop::findOne(['id'=>1]);
    		$user->link('blacklistedShops', $shop);
    	}
    	if ($this->shop2){
    		$shop = Shop::findOne(['id'=>2]);
    		$user->link('blacklistedShops', $shop);
    	}
    	if ($this->shop3){
    		$shop = Shop::findOne(['id'=>3]);
    		$user->link('blacklistedShops', $shop);
    	}
    	return true;
    }
    
    public function getStatus($id){
    	switch ($id){
    		case 1: if ($this->shop1){
		    			return Html::tag('div','BLOCKED',['class'=>'status-deactivated']);
		    		}else{
		    			return Html::tag('div','NOT BLOCKED',['class'=>'status-activated']);
		    		}
		    		break;
    		case 2: if ($this->shop2){
		    			return Html::tag('div','BLOCKED',['class'=>'status-deactivated']);
		    		}else{
		    			return Html::tag('div','NOT BLOCKED',['class'=>'status-activated']);
		    		}
		    		break;
    		case 3: if ($this->shop3){
		    			return Html::tag('div','BLOCKED',['class'=>'status-deactivated']);
		    		}else{
		    			return Html::tag('div','NOT BLOCKED',['class'=>'status-activated']);
		    		}
		    		break;
    	}
    }
}
