<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentItems extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'to_members', 'to_payments', 'description'];
    
    public $timestamps = false;

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }

    public function getMembersPaymentItemsTextList()
    {
        $paymentItems = $this
            ->where('to_members', 1)
            ->get();
        $items = [];
        foreach ($paymentItems as $pItems) {
            $items[$pItems->id] =  $pItems->name;
        }
        return $items;
    }

    public function getPaymentItemsTextList()
    {
        $paymentItems = $this
            ->where('to_payments', 1)
            ->get();
        $items = [];
        foreach ($paymentItems as $pItems) {
            $items[$pItems->id] = $pItems->name;
        }
        return $items;
    }

}
