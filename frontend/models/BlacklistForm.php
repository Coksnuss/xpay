<?php
namespace frontend\models;

use yii\base\Model;
use Yii;

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
    
    public function setUser($user_id){
    	$this->user_id=$user_id;
    }
    
    public function save(){
    	$user = User::findOne(['id'=>$this->user_id]);
    	
    	//TODO linking Relational ActiveRecord shopBlacklist -> User und Shop
    	
    	return false;
    }
}
