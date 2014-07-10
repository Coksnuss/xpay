<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "account_statement".
 *
 * Check the base class at common\models\base\AccountStatement in order to
 * see the column names and relations.
 */
class AccountStatement extends \common\models\base\AccountStatement
{
    /**
     * @return string The absolute path to the PDF file for this account
     * statement.
     */
    public function getFilePath()
    {
        list($month, $year) = explode('.', date('m.Y', strtotime($this->date)));
        return self::generateFilePath($this->account->number, $month, $year);
    }

    /**
     * Generates the absolute path to the account statement PDF for this account
     * for a given month and year.
     *
     * @param integer $month The month
     * @param integer $year The year
     * @return string The filename.
     */
    private static function generateFilePath($accountNumber, $month, $year)
    {
        return
            Yii::getAlias('@console/pdf/') .
            sprintf('account_statement_%04d_%02d_%06d.pdf', $year, $month, $accountNumber);
    }
}
