<?php
namespace frontend\models;

use yii\base\Model;
use Yii;

use common\models\Currency;
use common\models\Account;
use common\models\User;

/**
 * Signup form
 */
class DeleteForm extends Model
{
    public $iban;
    public $bic;

    /**
     * Simulates transaction of remaining amount to another bank account.
     *
     * @return true
     */
    public function delete()
    {
        return true;
    }
}
