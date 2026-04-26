<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmCheckReportVendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'pm_check_report_id',
        'vendor_name',
        'theme',
        'total_units',
        'plan_units',
        'actual_units',
        'daily_actual',
        'sort_order',
    ];

    protected $casts = [
        'daily_actual' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(PmCheckReport::class, 'pm_check_report_id');
    }

    public function getAchievementRatioAttribute(): ?float
    {
        if ((int) $this->plan_units <= 0) {
            return null;
        }

        return round(((int) $this->actual_units / (int) $this->plan_units), 4);
    }
}
