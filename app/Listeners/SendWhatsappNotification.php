<?php

namespace App\Listeners;

use App\Events\ViolationLogged;
use App\Models\WaQueue;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWhatsappNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected WhatsappService $whatsappService;

    /**
     * Create the event listener.
     */
    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle the event.
     */
    public function handle(ViolationLogged $event): void
    {
        $log = $event->violationLog;
        
        // Eager load relations if not already loaded
        $log->load(['student.kelas', 'violation']);
        
        $student = $log->student;
        $violation = $log->violation;
        $kelas = $student->kelas;

        if (!$student || !$violation) {
            Log::warning("Cannot send WhatsApp notification: Missing student or violation relation in log ID {$log->id}.");
            return;
        }

        $parentPhone = $student->parent_phone;
        $parentName = $student->parent_name;
        $studentName = $student->name;
        $className = $kelas->class_name;
        $violationName = $violation->violation_name;
        $pointsAdded = $log->points_added;
        $totalPoints = $student->current_points;

        // Draft message
        $message = "🚨 *NOTIFIKASI KEDISIPLINAN SISWA* 🚨\n"
                 . "*SMK NEGERI 2 JAKARTA*\n\n"
                 . "Yth. Bapak/Ibu *{$parentName}*,\n"
                 . "Orang Tua/Wali dari siswa:\n\n"
                 . "👤 *Nama:* {$studentName}\n"
                 . "🏫 *Kelas:* {$className}\n\n"
                 . "Menginformasikan bahwa putra/putri Bapak/Ibu tercatat melakukan pelanggaran kedisiplinan:\n\n"
                 . "⚠️ *Jenis Pelanggaran:*\n"
                 . "_{$violationName}_\n"
                 . "📈 *Tambahan Poin:* +{$pointsAdded} Poin\n\n"
                 . "📊 *Akumulasi Poin Saat Ini:* *{$totalPoints} Poin*\n\n"
                 . "-----------------------------------------\n"
                 . "*Info Sanksi Akumulasi Poin:*\n"
                 . "• 10 - 25 Poin: Peringatan Lisan oleh Wali Kelas\n"
                 . "• 26 - 50 Poin: SP 1 & Pemanggilan Orang Tua\n"
                 . "• 51 - 75 Poin: SP 2 & Skorsing 3 Hari\n"
                 . "• 76 - 99 Poin: SP 3 & Skorsing 1 Minggu\n"
                 . "• ≥ 100 Poin: Dikembalikan ke Orang Tua (Drop Out)\n\n"
                 . "Mohon bimbingan dan kerja samanya untuk membina putra/putri Bapak/Ibu agar senantiasa menaati tata tertib sekolah.\n\n"
                 . "Terima kasih.\n"
                 . "_Sistem Informasi Kedisiplinan SMKN 2 Jakarta_";

        // 1. Create a queue entry
        $queueItem = WaQueue::create([
            'violation_log_id' => $log->id,
            'phone_number' => $parentPhone,
            'message_body' => $message,
            'status' => 'pending',
        ]);

        // 2. Try sending the message
        $result = $this->whatsappService->sendMessage($parentPhone, $message);

        if ($result['success']) {
            $queueItem->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } else {
            $queueItem->update([
                'status' => 'failed',
                'error_message' => $result['error'],
            ]);
        }
    }
}
