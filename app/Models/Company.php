<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'jurisdiction_id',
    ];

    protected $approvable_relationships = [
        'department_id'   => 'App\Models\Department',
        'jurisdiction_id' => 'App\Models\Jurisdiction',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }
}
