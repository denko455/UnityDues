<?php


namespace App\Http\Controllers;


use App\Models\Members;
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

    public function pdfProjectPayments(string $id)
    {
        $data = Payments::getProjectPaymentData($id);

        $pdf = PDF::loadView('raport_templates.project_payments_transactions', $data);
        $pdf->set_paper('a4', 'landscape');

        return $pdf->stream('', ['Attachment' => false]);
    }
    public function pdfMemberPayments(string $id)
    {
        $data = Payments::getMemberPaymentData($id);

        $pdf = PDF::loadView('raport_templates.member_payments_transactions', $data);
        $pdf->set_paper('a4', 'landscape');

        return $pdf->stream('', ['Attachment' => false]);
    }
    public function pdfMembers(string $filter)
    { 
        $filters = json_decode(base64_decode($filter));
        $data = Members::getMembers($filters);
        $pdf = PDF::loadView('raport_templates.members_list', $data);
        $pdf->set_paper('a4', 'landscape');

        return $pdf->stream('', ['Attachment' => false]);
    }
}