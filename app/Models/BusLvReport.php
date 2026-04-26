<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusLvReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company_name',
        'week_number',
        'report_year',
        'start_date',
        'end_date',
        'bus_unit_count_daily',
        'bus_co_daily',
        'bus_ci_daily',
        'bus_capacity_daily',
        'bus_extra_daily',
        'lv_unit_count_daily',
        'lv_co_daily',
        'lv_ci_daily',
        'lv_capacity_daily',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'bus_unit_count_daily' => 'array',
        'bus_co_daily' => 'array',
        'bus_ci_daily' => 'array',
        'bus_capacity_daily' => 'array',
        'bus_extra_daily' => 'array',
        'lv_unit_count_daily' => 'array',
        'lv_co_daily' => 'array',
        'lv_ci_daily' => 'array',
        'lv_capacity_daily' => 'array',
        'notes' => 'array',
    ];
}
