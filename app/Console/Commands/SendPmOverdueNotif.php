<?php

namespace App\Console\Commands;

use App\Models\PmSchedule;
use App\Services\FleetWhatsappService;
use Illuminate\Console\Command;

class SendPmOverdueNotif extends Command
{
    protected $signature   = 'fleet:pm-notif {--dry-run : Hanya tampilkan tanpa kirim WA}';
    protected $description = 'Kirim notifikasi WA untuk PM yang overdue atau H-2 jadwal';

    public function __construct(protected FleetWhatsappService $wa)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        PmSchedule::refreshStatuses();

        // Kirim ke yang overdue & belum dinotif
        $overdues = PmSchedule::where('status', 'overdue')
            ->where('notif_sent', false)
            ->get();

        // Kirim juga ke yang H-2 dari jadwal (reminder awal)
        $upcoming = PmSchedule::where('status', 'scheduled')
            ->whereDate('scheduled_date', now()->addDays(2)->toDateString())
            ->where('notif_sent', false)
            ->get();

        $all  = $overdues->merge($upcoming);
        $sent = 0;

        if ($all->isEmpty()) {
            $this->info('Tidak ada PM yang perlu dinotifikasi.');
            return 0;
        }

        foreach ($all as $pm) {
            $this->line("Notif → {$pm->unit_code} | {$pm->scheduled_date} | {$pm->pic_phone}");

            if (!$this->option('dry-run')) {
                $result = $this->wa->sendPmReminder($pm);
                if ($result['success']) {
                    $pm->update(['notif_sent' => true, 'notif_sent_at' => now()]);
                    $sent++;
                    $this->info("  ✓ Terkirim");
                } else {
                    $this->error("  ✗ Gagal: " . $result['response']);
                }
            } else {
                $this->warn("  [DRY RUN] tidak benar-benar dikirim");
                $sent++;
            }
        }

        $this->info("Selesai. Notifikasi dikirim: {$sent}/" . $all->count());
        return 0;
    }
}
