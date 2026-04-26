<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmCheckReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company_name',
        'week_number',
        'report_year',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function vendorReports()
    {
        return $this->hasMany(PmCheckReportVendor::class)->orderBy('sort_order')->orderBy('vendor_name');
    }
}
