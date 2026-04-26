<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2hChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'p2h_unit_id',
        'checklist_date',
        'kilometer',
        'safe_to_use',
        'maintenance_required',
        'created_by_name',
        'source_type',
        'source_filename',
    ];

    protected $casts = [
        'checklist_date' => 'date',
        'safe_to_use' => 'boolean',
        'maintenance_required' => 'boolean',
        'kilometer' => 'integer',
    ];

    public function unit()
    {
        return $this->belongsTo(P2hUnit::class, 'p2h_unit_id');
    }
}
