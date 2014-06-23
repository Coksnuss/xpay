<?php
namespace api\models;

use Yii;

use common\models\Account;
use common\models\Transaction;
use common\models\CheckoutRequest;

/**
 * Do Checkout form
 */
class DoCheckoutForm extends \yii\base\Model
{
    public $checkout_id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['checkout_id', 'required'],
            ['checkout_id', 'exist', 'targetClass' => CheckoutRequest::className(), 'targetAttribute' => 'checkout_id'],
            ['checkout_id', 'checkoutIsApprovedByUser'],
        ];
    }

    /**
     * Validator to check whether the current checkout was approved by a user.
     *
     * @param string $attribute The attribute name which holds the checkout id.
     * @param array $params Not used.
     */
    public function checkoutIsApprovedByUser($attribute, $params)
    {
        $checkoutRequest = CheckoutRequest::findByCheckoutId($this->$attribute);

        if (!$checkoutRequest->isApprovedByUser()) {
            $this->addError($attribute, 'This checkout is not approved by a customer.');
        }
    }

    /**
     * Transfers money from one account to another account based on a checkout.
     *
     * This will create two transactions:
     * - The first transaction will debit the amount from the user which has
     *   approved the checkout. This account is located within our service. In
     *   case that the user does not have enough money, an error is added to the
     *   transaction which is returned at the end of this method.
     * - The second transaction will credit the amount to the receiver. The
     *   receiver account is possibly located at a different service in which
     *   case we invoke their API. If the account is located at our own service,
     *   the second transaction is also created internaly. In case of an error
     *   the transaction model is updated accordingly and returned at the end of
     *   this method.
     *
     * In any case, it is ensured that either both transactions are created or
     * none.
     *
     * @return Transaction The first created transaction, in case that it has
     * errors. Otherwise the second created transaction.
     */
    public function performTransaction()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try
        {
            $checkout = CheckoutRequest::findByCheckoutId($this->checkout_id);

            // Create first transaction (debit payer/buyer account)
            $debitTransaction = $this->performDebitTransaction($checkout);
            if ($debitTransaction->hasErrors()) {
                return $debitTransaction;
            }

            // Create second transaction (credit receiver account)
            $creditTransaction = $this->performCreditTransaction($checkout);
            if ($creditTransaction->hasErrors()) {
                $transaction->rollBack();
                return $creditTransaction;
            }

            // Delete checkout in order to avoid multiple payments
            $checkout->delete();
            $transaction->commit();

            return $creditTransaction;
        } catch (Exception $e) {
            $transaction->rollBack();

            throw $e;
        }
    }

    /**
     * Helper method for performTransaction()
     *
     * @param CheckoutRequest $checkout.
     * @return Transaction
     */
    private function performDebitTransaction($checkout)
    {
        $transaction = new Transaction();
        $transaction->generateTransactionId();
        $transaction->associated_account_number = $checkout->receiver_account_number;
        $transaction->type = $checkout->type;
        $transaction->amount = $checkout->getAmountInPrimaryCurrency() * -1;

        if (!$checkout->isPaidInPrimaryCurrency()) {
            $transaction->foreign_currency_amount = $checkout->amount * -1;
            $transaction->foreign_currency_id = $checkout->getCurrencyId();
        }

        $transaction->description = $checkout->description;

        $account = $checkout->account;
        $account->linkTransaction($transaction);

        return $transaction;
    }

    /**
     * Helper method for performTransaction()
     *
     * @param CheckoutRequest $checkout.
     * @return Transaction
     */
    private function performCreditTransaction($checkout)
    {
        if ($checkout->isInternalReceiver()) {
            $account = Account::lookup($checkout->receiver_account_number);

            $transaction = new Transaction();
            $transaction->generateTransactionId();
            $transaction->associated_account_number = $checkout->account->number;
            $transaction->type = $checkout->type;
            $transaction->amount = $checkout->getAmountInPrimaryCurrency();

            if (!$checkout->isPaidInPrimaryCurrency()) {
                $transaction->foreign_currency_amount = $checkout->amount;
                $transaction->foreign_currency_id = $checkout->getCurrencyId();
            }

            $transaction->description = $checkout->description;
            $account->linkTransaction($transaction);

            return $transaction;
        } else {
            // TODO: Make remote doTransaction call to corresponding API
            throw new \yii\base\NotSupportedException();
        }
    }
}
