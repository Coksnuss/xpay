<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "transaction".
 *
 * Check the base class at common\models\base\Transaction in order to
 * see the column names and relations.
 */
class Transaction extends \common\models\base\Transaction
{
    /**
     * The available transaction types.
     */
    const TYPE_ORDER = 1;


    /**
     * @return array A list of transaction types indexed by the corresponding
     * type ID.
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_ORDER => 'order',
        ];
    }
    
    public function attributeLabels(){
    	return ['created_at' => 'Booked at']+parent::attributeLabels();
    }
    
    public $receiver;
    public $sender;
    public $time;
    
}
