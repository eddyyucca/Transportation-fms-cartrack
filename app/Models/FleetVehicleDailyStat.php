<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FleetVehicleDailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'registration',
        'total_trips',
        'distance_km',
        'total_engine_on_min',
        'total_driving_min',
        'total_idle_min',
        'harsh_acceleration',
        'harsh_braking',
        'harsh_cornering',
        'total_harsh_events',
        'threshold_speeding',
        'road_speeding',
        'total_speeding_events',
        'idle_ratio',
        'utilization_ratio',
        'pa_score',
        'safety_score',
        'performance_score',
        'status',
    ];

    protected $casts = [
        'report_date' => 'date',
        'distance_km' => 'decimal:2',
        'total_engine_on_min' => 'decimal:2',
        'total_driving_min' => 'decimal:2',
        'total_idle_min' => 'decimal:2',
        'idle_ratio' => 'decimal:2',
        'utilization_ratio' => 'decimal:2',
        'pa_score' => 'decimal:2',
        'safety_score' => 'decimal:2',
        'performance_score' => 'decimal:2',
    ];
}
