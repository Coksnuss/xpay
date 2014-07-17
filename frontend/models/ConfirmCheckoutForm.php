<?php
namespace frontend\models;

use common\models\CheckoutRequest;
use common\models\Currency;
use common\models\User;

/**
 * Confirm Checkout Form
 */
class ConfirmCheckoutForm extends \yii\base\Model
{
    public $checkoutId;
    public $userId;

    protected $_checkoutRequest;
    protected $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['checkoutId', 'exist', 'targetClass' => CheckoutRequest::className(), 'targetAttribute' => 'checkout_id'],
            ['checkoutId', 'notConfirmed'],
            ['userId', 'exist', 'targetClass' => User::className(), 'targetAttribute' => 'id'],
            ['userId', 'enoughBalance'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['!checkoutId'],
            'fullcheck' => ['!userId'],
        ];
    }

    /**
     * Validates that the checkout has not been confirmed before.
     */
    public function notConfirmed($attribute, $params)
    {
        $checkout = $this->getCheckout();

        if ($checkout->account_id !== null) {
            $this->addError('checkoutId', 'Diese Zahlung wurde bereits von einem Kunden authorisiert.');
        }
    }

    /**
     * Validates that the user has enough balance for this checkout
     */
    public function enoughBalance()
    {
        $user = $this->getUser();
        $checkout = $this->getCheckout();

        if ($user->account->balance < $checkout->getAmountInPrimaryCurrency()) {
            $this->addError('userId', sprintf('Ihr Kontostand von %.2F %s ist nicht ausreichend um die Zahlung vorzunehmen (Benötigt: %.2F %s)',
                $user->account->balance, Currency::getPrimaryCurrencyCode(),
                $checkout->getAmountInPrimaryCurrency(), Currency::getPrimaryCurrencyCode()));
        }
    }

    /**
     * @return CheckoutRequest The checkout request to the checkout ID of this form.
     */
    public function getCheckout()
    {
        if ($this->_checkoutRequest === null) {
            $this->_checkoutRequest = CheckoutRequest::findByCheckoutId($this->checkoutId);
        }

        return $this->_checkoutRequest;
    }

    /**
     * @return User The user which corresponds to the user id of this form.
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne($this->userId);
        }

        return $this->_user;
    }

    /**
     * Performs the confirmation of the checkout by the user
     * @return boolean Whether the confirmation could be performed.
     */
    public function performCheckoutConfirmation()
    {
        if (!$this->hasErrors())
        {
            $this->scenario = 'fullcheck';
            if ($this->validate()) {
                $checkout = $this->getCheckout();
                $user = $this->getUser();
                $checkout->link('account', $user->account);

                return true;
            }
        }

        return false;
    }
}
