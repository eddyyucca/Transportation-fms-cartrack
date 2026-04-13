<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FleetUnit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_code',
        'vendor',
        'department',
        'brand',
        'type_model',
        'registration',
        'is_monitored',
        'initial_hm',
    ];

    protected $casts = [
        'is_monitored' => 'boolean',
        'initial_hm'   => 'decimal:2',
    ];

    public function metrics()
    {
        return $this->hasMany(FleetUnitDailyMetric::class, 'unit_code', 'unit_code');
    }

    public function pmSchedules()
    {
        return $this->hasMany(PmSchedule::class, 'unit_code', 'unit_code');
    }

    public function latestMetric()
    {
        return $this->hasOne(FleetUnitDailyMetric::class, 'unit_code', 'unit_code')->latestOfMany('report_date');
    }
}
