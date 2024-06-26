<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Payments;
use PDF;

class PDFController extends Controller
{
    public function pdfPayments(string $filter)
    {
        $filters = json_decode(base64_decode($filter));
        $data = Payments::getPaymentData($filters);

        $pdf = PDF::loadView('raport_templates.payments_transactions', $data);
        $pdf->set_paper('a4', 'landscape');

        return $pdf->stream('', ['Attachment' => false]);

    }
}