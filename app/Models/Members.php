<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'tel', 'id_number', 'no_family_members'];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'member_id');
    }

    public function getCsv($filter){

    }
}
