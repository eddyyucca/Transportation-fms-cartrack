<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PmSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_code',
        'scheduled_date',
        'completed_date',
        'status',
        'pic_name',
        'pic_phone',
        'notes',
        'notif_sent',
        'notif_sent_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'notif_sent'     => 'boolean',
        'notif_sent_at'  => 'datetime',
    ];

    public function unit()
    {
        return $this->belongsTo(FleetUnit::class, 'unit_code', 'unit_code');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'scheduled' && Carbon::parse($this->scheduled_date)->isPast();
    }

    // Auto update status ke overdue jika lewat jadwal
    public static function refreshStatuses(): void
    {
        self::where('status', 'scheduled')
            ->where('scheduled_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);
    }
}
