<?php
namespace api\models;

use yii\helpers\ArrayHelper;

use common\models\Account;
use common\models\Transaction;

class GoliathNationalTransaction extends \api\models\RemoteTransaction
{
    const TYPE_TRANSACTION = 0;
    const TYPE_BACKPOST    = 1;
    const TYPE_CREDIT      = 2;

    public $transaction_id;
    public $sender_name;
    public $receiver_name;
    public $api_token = 'a382699c1d6f41b60fe6db9403d848811b5328bbb61df2248cecb23f3d902777';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['sender_name', 'receiver_name'], 'string', 'min' => 3],
            ['transaction_id', 'string', 'length' => 32],
            ['receiver_account_number', 'isGoliathAccountNumber'],
            ['type', 'in', 'range' => [self::TYPE_TRANSACTION, self::TYPE_BACKPOST, self::TYPE_CREDIT]],
        ]);
    }

    public function isGoliathAccountNumber($attribute, $params)
    {
        if (Account::getPaymentSystemIdByAccountNumber($this->$attribute) !== 5) {
            $this->addError($attribute, 'Receiver account number does not belong to Goliath National Payment.');
        }
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'receiverID'    => 'receiver_account_number',
            'senderID'      => 'sender_account_number',
            'amount'        => 'amount',
            'currency'      => 'currency',
            'purpose'       => 'description',
            'type'          => 'type',
            'transactionID' => 'transaction_id',
            'senderName'    => 'sender_name',
            'receiverName'  => 'receiver_name',
            'accessToken'   => 'api_token',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function byCheckout($checkout)
    {
        $model = parent::byCheckout($checkout);
        if ($checkout->type == Transaction::TYPE_REDEMPTION) {
            $model->transaction_id = $checkout->reference;
        }
        $model->sender_name = $checkout->account->user->name;

        return $model;
    }

    /**
     * @inheritdoc
     */
    public static function translateType($checkoutType)
    {
        switch ($checkoutType)
        {
            default: // TODO: Throw exception?
            case Transaction::TYPE_ORDER: return self::TYPE_TRANSACTION;
            case Transaction::TYPE_REDEMPTION: return self::TYPE_CREDIT;
        }
    }

    /**
     * @inheritdoc
     */
    public function performTransaction()
    {
        $postdata = array_filter($this->toArray(), function ($e) { return $e !== null; });
        $postdata['amount'] = 0;
        $postdata['purpose'] = 'TEST';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($curl, CURLOPT_URL, 'https://gnp.wsp.lab.sit.cased.de/api/dotransaction');
        $response = curl_exec($curl);

        // TODO:
        var_dump($response);
        die;
        throw new \yii\web\NotFoundHttpException();
    }
}
