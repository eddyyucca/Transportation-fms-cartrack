<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FleetUnitDailyMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_code',
        'report_date',
        'idle_hours',
        'hm_start',
        'hm_end',
        'hm_usage',
        'distance_km',
        'ua_percent',
        'standby_hours',
        'fuel_consumption',
        'notes',
    ];

    protected $casts = [
        'report_date'      => 'date',
        'idle_hours'       => 'decimal:2',
        'hm_start'         => 'decimal:2',
        'hm_end'           => 'decimal:2',
        'hm_usage'         => 'decimal:2',
        'distance_km'      => 'decimal:2',
        'ua_percent'       => 'decimal:2',
        'standby_hours'    => 'decimal:2',
        'fuel_consumption' => 'decimal:2',
    ];

    public function unit()
    {
        return $this->belongsTo(FleetUnit::class, 'unit_code', 'unit_code');
    }
}
