<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active' ];

    public function payments()
    {
        return $this->hasMany(Payments::class, 'project_id');
    }
}
