<?php
namespace common\helpers;

class Util
{
    public static function pdfA4LetterTemplate()
    {
        $pdf = new XPayPdf();
        $pdf->AddPage();
        $pdf->writeHTML("<h1>Transaktionen</h1>");

        $html = "<table>";
        $html .= "<tr>";
        $html .= "<th>Blabla #1</th>";
        $html .= "<th>Blabla #2</th>";
        $html .= "<th>Blabla #3</th>";
        $html .= "<th>Blabla #4</th>";
        $html .= "<th>Blabla #5</th>";
        $html .= "</tr>";

        for ($i = 0; $i < 80; ++$i)
        {
            $html .= "<tr>";
            $html .= "<td>Eins</td>";
            $html .= "<td>Zwo</td>";
            $html .= "<td colspan=\"2\">Drei & Vier</td>";
            $html .= "<td>Fünf</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";

        $pdf->writeHTML($html);

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

        return $pdf;
    }
}
