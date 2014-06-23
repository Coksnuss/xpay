<?php
namespace common\helpers;

use Yii;

class XPayPdf extends \TCPDF
{
    const MARGIN_TOP    = 27;
    const MARGIN_RIGHT  = 15;
    const MARGIN_BOTTOM = 27;
    const MARGIN_LEFT   = 15;

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->SetMargins(self::MARGIN_LEFT, self::MARGIN_TOP, self::MARGIN_RIGHT);
        $this->setHeaderMargin(5);
        $this->setFooterMargin(self::MARGIN_BOTTOM);
        $this->SetAutoPageBreak(true, self::MARGIN_BOTTOM);
    }

    public function Header()
    {
        $logo = Yii::getAlias('@common/images/xpay-transparent-200x132px.png');

        $html = <<<EOD
<div align="center">
    <img src="$logo" width="100" height="66" />
</div>
EOD;
        $this->writeHTML($html);
    }

    public function Footer()
    {
        $page = $this->getAliasNumPage().'/'.$this->getAliasNbPages();

        $html = <<<EOD
<div style="border-top: 1px solid #000;">
    <p>Bla Bla Bla $page</p>
</div>
EOD;

        $this->writeHTML($html);
    }
}
