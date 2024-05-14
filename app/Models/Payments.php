<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'payment_item_id', 'document_date', 'document_number', 'month', 'year', 'quantity', 'value',
        'total', 'bank_id', 'currency', 'remarks', 'status', 'created_by', 'updated_by' ];

    public function member()
    {
        return $this->belongsTo(Members::class);
    }
    public function payment_item()
    {
        return $this->belongsTo(PaymentItems::class, 'payment_item_id');
    }
    public function bank()
    {
        return $this->belongsTo(Banks::class, 'bank_id');
    }

    public static function getCount($status, $userId = null){
        $count = self::where('status', $status);
        if($userId != null){
            $count->where('created_by', $userId);
        }
        return $count->count();
    }

    public static function getValueSum($status, $userId = null){

        $aSum = null;
        if($userId != null){
            $aSum = self::groupBy('currency')
                ->selectRaw('sum(value) as value, currency')        
                ->where('created_by', $userId)
                ->where('status', $status)
                ->orderBy('currency','desc')
                ->pluck('value', 'currency');
        } else {
            $aSum = self::groupBy('currency')
                ->selectRaw('sum(value) as value, currency')        
                ->where('status', $status)
                ->orderBy('currency','desc')
                ->pluck('value', 'currency');
        }

        $oFormatter = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);
        $aStr = [];
        foreach($aSum as $currency => $value){
            $aStr[] = $oFormatter->formatCurrency($value, $currency);
        }
        return implode(', ', $aStr);
    }


    public static function getIncomePayments(){
        return Payments::whereHas('payment_item', function ($query) {
            return $query->where('type', 'income');
        })
            ->where('status', 'approved');
    }

    public static function getExpensePayments(){
        return Payments::whereHas('payment_item', function ($query) {
            return $query->where('type', 'expense');
        })
            ->where('status', 'approved');
    }

    public static function getPaymentYear(){
        $data = Payments::groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year', 'year')
            ->toArray();
        $years = [];
        foreach ($data as $key => $year){
            if(self::validateDate($year))
                $years[$key] = explode('-', $year)[0];
        }
        return $years;
    }
    private static function validateDate($date)
    {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function getCsv($filter){
        $_this = new self();

        $data = $_this->select(
            'member_payments.document_number as document-number',
            DB::raw("DATE_FORMAT(member_payments.document_date, '%d.%m.%Y') as 'document-date'"),
            'payment_items.code as payment-reason',
            DB::raw("CONCAT(members.first_name, ' ', members.last_name) as 'full-name'"),
            'member_payments.total as total',
            'member_payments.remarks as remarks',
            'member_payments.status as status',
        )
            ->leftJoin('payment_items', 'payment_items.id', 'member_payments.payment_item_id')
            ->leftJoin('members', 'members.id', 'member_payments.member_id');

        $paymentItemsIds = $filter->payment_item_id;
        $status = $filter->status ?? null;
        $year = $filter->year ?? null;

        if(!empty($paymentItemsIds)){
            $data->where('member_payments.payment_item_id', $paymentItemsIds);
        }

        if(isset($status)){
            $data->where('member_payments.status', $status);
        }

        if(isset($year)){
            $data->where('member_payments.year', $year);
        }

        $dataList = $data->get('member_payments');
        $columnNames = array_keys($dataList->first()->toArray());
        foreach ($columnNames as $key=>$cName){
            $columnNames[$key] = __('filament::additional.payments.label.'.$cName );
        }
        return (object)[
            'columns' => $columnNames,
            'list' => $dataList->toArray(),
        ];
    }
}
