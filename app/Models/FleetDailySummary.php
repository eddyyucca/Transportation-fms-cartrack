<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FleetDailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'total_vehicles',
        'total_trips',
        'total_distance_km',
        'total_engine_on_min',
        'total_driving_min',
        'total_idle_min',
        'total_harsh_acceleration',
        'total_harsh_braking',
        'total_harsh_cornering',
        'total_threshold_speeding',
        'total_road_speeding',
        'total_harsh_events',
        'total_speeding_events',
        'avg_idle_ratio',
        'avg_utilization_ratio',
        'avg_pa_score',
        'avg_safety_score',
        'avg_performance_score',
        'raw_meta',
    ];

    protected $casts = [
        'report_date' => 'date',
        'raw_meta' => 'array',
        'total_distance_km' => 'decimal:2',
        'total_engine_on_min' => 'decimal:2',
        'total_driving_min' => 'decimal:2',
        'total_idle_min' => 'decimal:2',
        'avg_idle_ratio' => 'decimal:2',
        'avg_utilization_ratio' => 'decimal:2',
        'avg_pa_score' => 'decimal:2',
        'avg_safety_score' => 'decimal:2',
        'avg_performance_score' => 'decimal:2',
    ];

    public function vehicleStats()
    {
        return $this->hasMany(FleetVehicleDailyStat::class, 'report_date', 'report_date');
    }
}
