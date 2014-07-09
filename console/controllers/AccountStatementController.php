<?php
namespace console\controllers;

use Yii;
use yii\db\Expression;
use yii\helpers\Console;

use common\models\Account;
use common\helpers\XPayPdf;

/**
 * Generates account statements for the last month.
 */
class AccountStatementController extends \yii\console\Controller
{
    public $defaultAction = 'generate';

    /**
     * Generates the account statements (PDF files) for the last month, for every available user.
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
                $file = $account->generateAccountStatementFilePath($month, $year);

                $pdf = new XPayPdf();
                $pdf->startAccountStatement($account, $month, $year);
                foreach ($account->transactions as $transaction)
                {
                    $pdf->addAccountTransaction($transaction);
                }
                $pdf->endAccountStatement();
                $pdf->saveToDisk($file);
            } catch(\Exception $e) {
                $this->stderr($e->getMessage() . PHP_EOL);
            }
        }
    }
}
