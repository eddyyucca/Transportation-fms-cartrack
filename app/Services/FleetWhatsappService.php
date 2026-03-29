<?php

namespace App\Services;

use App\Models\PmSchedule;
use Carbon\Carbon;

class FleetWhatsappService
{
    private string $token  = 'gLwdMNznAQxXc4TuRDb9'; // ganti jika token berbeda
    private string $apiUrl = 'https://api.fonnte.com/send';

    // Default PIC sementara
    private string $defaultPhone = '081250653005';

    public function sendPmReminder(PmSchedule $pm): array
    {
        $phone   = $pm->pic_phone ?: $this->defaultPhone;
        $message = $this->buildPmMessage($pm);

        return $this->sendSingle($phone, $message);
    }

    public function buildPmMessage(PmSchedule $pm): string
    {
        $unit     = $pm->unit_code;
        $date     = Carbon::parse($pm->scheduled_date)->locale('id')->isoFormat('D MMMM YYYY');
        $picName  = $pm->pic_name ?: 'PIC Fleet';
        $status   = strtoupper($pm->status);
        $daysAgo  = Carbon::parse($pm->scheduled_date)->diffInDays(now(), false);

        if ($pm->status === 'overdue') {
            return "Yth. *{$picName}*\n\n"
                . "🚨 *PM CHECK OVERDUE — SEGERA TINDAK LANJUTI*\n\n"
                . "Unit *{$unit}* memiliki jadwal PM Check yang telah lewat:\n"
                . "📅 Jadwal: *{$date}*\n"
                . "⏰ Sudah lewat *{$daysAgo} hari*\n\n"
                . "Mohon segera lakukan PM Check atau konfirmasi ke tim Fleet.\n\n"
                . "_Pesan ini dikirim otomatis oleh Sistem Fleet Monitoring._";
        }

        return "Yth. *{$picName}*\n\n"
            . "🔔 *REMINDER PM CHECK*\n\n"
            . "Unit *{$unit}* memiliki jadwal PM Check:\n"
            . "📅 Tanggal: *{$date}*\n\n"
            . "Mohon persiapkan unit dan pastikan PM Check dilaksanakan sesuai jadwal.\n\n"
            . "_Pesan ini dikirim otomatis oleh Sistem Fleet Monitoring._";
    }

    private function sendSingle(string $phone, string $message): array
    {
        $phone = $this->formatPhone($phone);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => [
                'target'      => $phone,
                'message'     => $message,
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => ['Authorization: ' . $this->token],
        ]);
        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        return ['success' => !$error, 'response' => $error ?: $response];
    }

    private function formatPhone(string $phone): string
    {
        $p = preg_replace('/\D/', '', $phone);
        if (str_starts_with($p, '0')) {
            $p = '62' . substr($p, 1);
        }
        return $p;
    }
}
