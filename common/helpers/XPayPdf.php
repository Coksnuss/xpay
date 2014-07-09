<?php
namespace common\helpers;

use Yii;

// wichtige funktionen (manuell rausgesucht)
//$pdf->SetTitle($title)
//$pdf->SetSubject($subject)($subject)
//$pdf->SetAuthor($author)
//$pdf->SetKeywords($keywords)
//$pdf->SetCreator($creator)
//$pdf->setHeaderData($ln='', $lw=0, $ht='', $hs='', $tc=array(0,0,0), $lc=array(0,0,0)
//$pdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0))
//addTTFfont()
//AddFont()
//SetFontSize()
//Output($name='doc.pdf', $dest='I') (Inline/Download/File (saving)/String/FI/FD/Email attachment)
//setHeaderFont($font)
//setFooterFont($font)
//writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
//$pdf->AddPage() // Calls Footer() and Header()
class XPayPdf extends \TCPDF
{
    const MARGIN_TOP    = 45;
    const MARGIN_RIGHT  = 15;
    const MARGIN_BOTTOM = 35;
    const MARGIN_LEFT   = 15;

    const HEADER_MARGIN = 8;
    const FOOTER_MARGIN = 27;

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->SetMargins(self::MARGIN_LEFT, self::MARGIN_TOP, self::MARGIN_RIGHT);
        $this->setHeaderMargin(self::HEADER_MARGIN);
        $this->setFooterMargin(self::FOOTER_MARGIN);
        $this->SetAutoPageBreak(true, self::MARGIN_BOTTOM);
    }

    public function Header()
    {
        $logo = Yii::getAlias('@common/images/xpay-transparent-200x132px.png');

        $this->writeHTML(
            sprintf('<img src="%s" width="100" height="66" />', $logo),
            true, false, false, false, 'C');
        $this->setY(self::HEADER_MARGIN + $this->pixelsToUnits(66 + 7));
        $this->writeHTML('<span style="font-size: 8px; color: #489B30;">xPay Paymentservices</span>', false);
        $this->writeHTML('<span style="font-size: 8px;"> &bull; Postfach 2034</span>', false);
        $this->writeHTML('<span style="font-size: 8px;"> &bull; 12345 Geldhausen</span>', false);
    }

    public function Footer()
    {
        $page = $this->getAliasNumPage().'/'.$this->getAliasNbPages();

        $this->writeHTML('<hr />');
        $this->writeHTML('<span style="font-size: 8px; text-align: center; font-weight: bold; color: #489B30;">xPay Paymentservices</span>', false);
        $this->setY($this->getY() + $this->pixelsToUnits(10));
        $this->writeHTML('<span style="font-size: 8px; text-align: center;"><span style="font-weight: bold;">Geschäftsführer</span>&nbsp;&nbsp;Markus Schanz&nbsp;&nbsp;Marc Andre Bär&nbsp;&nbsp;Johannes Wagner&nbsp;&nbsp;Rene Röpke</span>', false);
        $this->setY($this->getY() + $this->pixelsToUnits(10));
        $this->writeHTML('<span style="font-size: 8px; text-align: center;"><span style="font-weight: bold;">Kontakt</span>&nbsp;&nbsp;xpay@wsp.lab.sit.cased.de</span>', false);
        $this->setY($this->getY() + $this->pixelsToUnits(10));
        $this->writeHTML('<span style="font-size: 8px; text-align: center; font-weight: bold;">http://xpay.wsp.lab.sit.cased.de</span>', false);
        $this->setY($this->getY() + $this->pixelsToUnits(10));
        $this->writeHTML('<span style="font-size: 8px; text-align: right;">' . $page . '</span>', false);
    }

    private $account;
    private $htmlTable;
    private $oldBalance;
    private $date;
    public function startAccountStatement($account, $month = null, $year = null)
    {
        static $months = [
            1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli',
            'August', 'September', 'Oktober', 'November', 'Dezember'
        ];
        $month = $month === NULL ? intval(date('n', strtotime('-1 month'))) : intval($month);
        $year = $year === NULL ? intval(date('Y', strtotime('-1 month'))) : intval($year);

        $this->account = $account;
        $this->htmlTable = '';
        $this->oldBalance = $this->account->balance;
        $this->date = date('t.m.Y', mktime(0, 0, 0, $month, 1, $year));

        $this->htmlTable .= sprintf('<strong>Kontonummer %06u</strong><br>', $this->account->number);
        $this->htmlTable .= sprintf('<strong>Kontoauszug %s %u</strong><br>', $months[$month], $year);
        $this->htmlTable .= '<br>';

        $this->htmlTable .= '<table cellpadding="2" border="0">';
        $this->htmlTable .= '<thead>';
        $this->htmlTable .= '<tr>';
        $this->htmlTable .= '<td width="20%" style="font-weight:bold;">Buchung</td>';
        $this->htmlTable .= '<td width="60%" style="font-weight:bold;">Verwendungszweck</td>';
        $this->htmlTable .= '<td width="20%" style="font-weight:bold; text-align: right;">Betrag (EUR)</td>';
        $this->htmlTable .= '</tr>';
        $this->htmlTable .= '</thead>';
    }

    public function addAccountTransaction($transaction)
    {
        $this->oldBalance -= $transaction->amount;

        $this->htmlTable .= '<tr>';
        $this->htmlTable .= sprintf('<td width="20%%">%s</td>', date('d.m.Y', strtotime($transaction->created_at)));
        $this->htmlTable .= sprintf('<td width="60%%">%s</td>', \yii\helpers\Html::encode($transaction->description));
        $this->htmlTable .= sprintf('<td width="20%%" style="text-align: right;">%.2F</td>', $transaction->amount);
        $this->htmlTable .= '</tr>';
    }

    public function endAccountStatement()
    {
        $this->htmlTable .= '<tr>';
        $this->htmlTable .= "<td width=\"20%\">&nbsp;</td>";
        $this->htmlTable .= '<td width="60%" style="font-weight: bold;">Neuer Saldo</td>';
        $this->htmlTable .= sprintf('<td width="20%%" style="text-align: right; font-weight: bold;">%.2F</td>', $this->account->balance);
        $this->htmlTable .= '</tr>';
        $this->htmlTable .= "</table>";

        $this->htmlTable = $this->getAccountHeader() . $this->htmlTable;

        $this->AddPage();
        $this->writeHTML($this->htmlTable);
    }

    public function saveToDisk($filename, $overwrite = false)
    {
        if ($overwrite || !file_exists($filename))
            $this->Output($filename, 'F');
    }

    protected function getAccountHeader()
    {
        $oldBalance = sprintf('%.2F', $this->oldBalance);
        $html = <<<EOL
<table>
    <tr>
        <td width="15%"><span style="font-size: 10px; font-weight: bold;">Kunde</span></td>
        <td width="15%" style="text-align: right;"><span style="font-size: 10px;">{$this->account->user->first_name} {$this->account->user->last_name}</span></td>
        <td width="30%">&nbsp;</td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Datum</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">{$this->date}</span></td>
    </tr>
    <tr>
        <td width="60%" colspan="3"></td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Kontonummer</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">{$this->account->number}</span></td>
    </tr>
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr style="border-top: 1px solid #f00;">
        <td width="60%" colspan="2"></td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Alter Saldo</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">{$oldBalance} EUR</span></td>
    </tr>
    <tr>
        <td width="60%" colspan="2"></td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Neuer Saldo</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">{$this->account->balance} EUR</span></td>
    </tr>
</table>
EOL;
        return $html;
    }
}
