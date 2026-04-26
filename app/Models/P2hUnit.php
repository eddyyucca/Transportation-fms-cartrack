<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2hUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor',
        'unit_code',
        'head',
        'department',
        'plate_no',
        'pic_name',
        'model_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function checklists()
    {
        return $this->hasMany(P2hChecklist::class);
    }
}
