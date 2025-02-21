<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurisdiction extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'country',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
