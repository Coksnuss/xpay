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

    public function generateAccountStatement($account)
    {
        $this->AddPage();
        $this->printAccountHeader($account);


        $this->writeHTML("<strong>Kontonummer 123456</strong>");
        $this->writeHTML("<strong>Kontoauszug Juni 2014</strong>");
        $this->writeHTML("<br>");

        $html = '<table cellpadding="2" border="0">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-weight:bold;">Buchung</td>';
        $html .= '<td width="60%" style="font-weight:bold;">Verwendungszweck</td>';
        $html .= '<td width="20%" style="font-weight:bold; text-align: right;">Betrag (EUR)</td>';
        $html .= '</tr>';
        $html .= '</thead>';

        for ($i = 0; $i < 80; ++$i)
        {
            $day = intval($i / (($i / 31) + 1)) + 1;
            if (strlen($day) === 1) $day = '0' . $day;
            $value = (mt_rand(0, 1) ? '-' : '+') . mt_rand(0, 999) . ',' . sprintf('%02d', mt_rand(0,99));

            $html .= '<tr>';
            $html .= "<td width=\"20%\">$day.06.2014</td>";
            $html .= '<td width="60%">fjaklfj d sjsdlfk sdjklf jsdklfjslkfjs</td>';
            $html .= "<td width=\"20%\" style=\"text-align: right;\">$value</td>";
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= "<td  width=\"20%\">&nbsp;</td>";
        $html .= '<td  width="60%" style="font-weight: bold;">Neuer Saldo</td>';
        $html .= "<td  width=\"20%\" style=\"text-align: right; font-weight: bold;\">1234,34</td>";
        $html .= '</tr>';
        $html .= "</table>";

        $this->writeHTML($html);
    }

    protected function printAccountHeader($account)
    {
        $html = <<<EOL
<table>
    <tr>
        <td width="15%"><span style="font-size: 10px; font-weight: bold;">Kunde</span></td>
        <td width="15%" style="text-align: right;"><span style="font-size: 10px;">{$account->user->first_name} {$account->user->last_name}</span></td>
        <td width="30%">&nbsp;</td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Datum</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">31.06.2014</span></td>
    </tr>
    <tr>
        <td width="60%" colspan="3"></td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Kontonummer</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">{$account->number}</span></td>
    </tr>
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr style="border-top: 1px solid #f00;">
        <td width="60%" colspan="2"></td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Alter Saldo</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">300,99 EUR</span></td>
    </tr>
    <tr>
        <td width="60%" colspan="2"></td>
        <td width="20%"><span style="font-size: 10px; font-weight: bold;">Neuer Saldo</span></td>
        <td width="20%" style="text-align: right;"><span style="font-size: 10px;">{$account->balance} EUR</span></td>
    </tr>
</table>
EOL;
        $this->writeHTML($html);
    }
}
