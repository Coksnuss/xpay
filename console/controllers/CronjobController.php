<?php
namespace console\controllers;

use Yii;
use yii\db\Expression;
use yii\helpers\Console;

use common\models\Account;
use common\models\AccountStatement;
use common\models\CheckoutRequest;
use common\helpers\XPayPdf;

/**
 * Controller which contains several cronjobs.
 */
class CronjobController extends \yii\console\Controller
{
    /**
     * Shows the help for this command.
     */
    public function actionIndex()
    {
        $this->run('/help', ['cronjob']);
    }

    /**
     * Generates the account statements for the last month.
     *
     * Account statements are generated as (PDF files) for every available user
     * and saved to disk.
     * This is supposed to be executed only once a month (at the first day).
     *
     * @param boolean $force Whether to force generation, even if this is not the first day of a month.
     */
    public function actionGenerate($force = false)
    {
        if (date('j') != 1 && !$force) {
            $this->stderr(
                'This should be only executed at the first day of new month.' . PHP_EOL
                . 'If you are certain in what you do, use the force option!' . PHP_EOL,
                Console::FG_RED, Console::BOLD);
            return;
        }

        // Eager loading transactions with only those of the last month
        $accounts = Account::find()->joinWith([
            'transactions' => function ($query) {
                //$query->from('{{transaction}} {{transaction}}');
                $query->onCondition(
                    ['and',
                        'YEAR({{transaction}}.[[created_at]]) = :year',
                        'MONTH({{transaction}}.[[created_at]]) = :month',
                    ], [
                        ':year' => date('Y', strtotime('-1 month')),
                        ':month' => date('n', strtotime('-1 month')),
                    ]);
                $query->orderBy(['{{transaction}}.[[created_at]]' => SORT_ASC]);
            },
        ])->orderBy(['{{account}}.[[id]]' => SORT_ASC]);

        $month = intval(date('n', strtotime('-1 month')));
        $year = intval(date('Y', strtotime('-1 month')));

        // Batch processing, avoid memory limit errors
        foreach ($accounts->each(10) as $account)
        {
            try
            {
                $this->stdout(sprintf('Generate account statement PDF file for account number %06d' . PHP_EOL, $account->number));
                $date = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));

                $accountStatement = AccountStatement::findOne([
                    'account_id' => $account->id,
                    'date' => $date,
                ]);

                if ($accountStatement === null) {
                    $accountStatement = new AccountStatement;
                    $accountStatement->date = $date;
                    $accountStatement->email_notification_send = 0;
                    $account->link('accountStatements', $accountStatement);
                }

                $pdf = new XPayPdf();
                $pdf->startAccountStatement($account, $month, $year);
                foreach ($account->transactions as $transaction)
                {
                    $pdf->addAccountTransaction($transaction);
                }
                $pdf->endAccountStatement();
                $pdf->saveToDisk($accountStatement->filePath);
            } catch(\Exception $e) {
                $this->stderr($e->getMessage() . PHP_EOL);
            }
        }
    }

    /**
     * Removes old checkout requests.
     *
     * This removes all checkout requests that were not updated within the last
     * 24 hours.
     */
    public function actionCleanCheckoutRequests()
    {
        //$requests = CheckoutRequest::findAll(['updated_at' => ['and', 'idd=2']]);
        $requests = CheckoutRequest::find()
            ->where(['and', 'updated_at < :yesterday'], [':yesterday' => date('Y-m-d H:i:s', strtotime('-1 day'))])
            ->all();

        foreach ($requests as $request)
        {
            $this->stdout(sprintf('Delete checkout request from %s (Last Update: %s)...', $request->created_at, $request->updated_at));
            if ($request->delete()) {
                $this->stdout('OK' . PHP_EOL);
            } else {
                $This->stdout('Failed' . PHP_EOL);
            }
        }
    }
}
