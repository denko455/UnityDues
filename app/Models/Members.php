<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'tel', 'id_number', 'no_family_members', 'residence_id'];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'member_id');
    }

    public function residence()
    {
        return $this->belongsTo(Residences::class, 'residence_id');
    }
    public static function getMembers($filters = []){
        $data = [];
        $query = self::query();
        foreach($filters->rows as $key => $filter){
            $value = $filter->value ?? null;
            $values = $filter->values ?? null;
            if(!empty($value)){
                $query->where($key, $value);
            } else if(!empty($values)){
                $query->whereIn($key, $values);
            }
        }
        $queryCount = clone $query;
        $group = $queryCount->select(\DB::raw('residences.name, count(*) as residence_count'))
        ->join('residences','residences.id', 'members.residence_id')
        ->groupBy("residences.name");
        $query->orderBy('first_name', 'asc');
        $data['list'] = $query->get();
        $data['residences'] = $group->get();
        return $data;
    }
}
