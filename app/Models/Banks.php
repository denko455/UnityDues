<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banks extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'iban'];
    public $timestamps = false;
    public static function getBanks(){

        $banks = self::orderBy("id","desc")->get();
        $items = [];
        foreach ($banks as $bank) {
            $items[$bank->id] = $bank->name;
        }
        return $items;
    }
}
