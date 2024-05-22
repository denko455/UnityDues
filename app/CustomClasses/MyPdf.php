<?php 

namespace App\CustomClasses;

use TCPDF;

class MyPdf extends TCPDF {
     //Page header
     public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function getPaymentsReport($data) {
        $htmlTitle = view('raport_templates.transactions_title_page');
        $htmlPayments = view('raport_templates.transactions', $data);
        $pdf = $this;
        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // Set default monospaced font
        // $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // $pdf->setFontSubsetting(false);

        $pdf->SetFont('Helvetica', '', 10);

        $pdf->AddPage();
        $pdf->writeHTML($htmlTitle, true, false, true, false, '');
        
        $pdf->AddPage();
        $pdf->setPrintFooter(true);
        // $pdf->writeHTML($htmlPayments, true, false, true, false, '');
        $pdf->writeHTMLCell(0, 0, null, null, $htmlPayments, 0, 0, 0, true, '', true);

    }
}