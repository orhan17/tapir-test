<?php

namespace App\Services;

use App\Models\Application;
use App\Models\CrmFailure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CRMService
{
    const CRM_URL = 'https://crm.tapir.ws/api/crm';
    const MAX_TIME_MINUTES = 5;
    const RETRY_INTERVAL_SECONDS = 10;

    public function sendApplicationToCRM(Application $application)
    {
        $startTime = now();

        while (true) {
            try {
                $response = Http::post(self::CRM_URL, [
                    'phone' => $application->phone,
                    'VIN'   => $application->car->vin,
                ]);

                if ($response->ok()) {
                    // Успешно
                    $application->update([
                        'status' => 'sent',
                        'crm_sent_at' => now(),
                    ]);

                    // Закрываем CrmFailure как resolved (если существует)
                    CrmFailure::where('application_id', $application->id)
                        ->update(['resolved' => true]);

                    return; // успех, выходим
                } else {
                    throw new \Exception("CRM responded with status " . $response->status());
                }

            } catch (\Exception $e) {
                Log::error("Error sending to CRM: " . $e->getMessage());

                $elapsed = now()->diffInSeconds($startTime);
                if ($elapsed >= self::MAX_TIME_MINUTES * 60) {
                    // Превышен лимит в 5 минут — отправить письмо админу
                    Mail::raw(
                        "Не удалось отправить заявку #{$application->id} в CRM за 5 минут.",
                        function ($msg) {
                            $msg->to('admin@admin.com')->subject('CRM sending error');
                        }
                    );

                    // Записать ошибку
                    $failure = CrmFailure::firstOrCreate(
                        ['application_id' => $application->id]
                    );
                    $failure->attempts += 1;
                    $failure->last_attempt_at = now();
                    $failure->error_message = $e->getMessage();
                    $failure->resolved = false;
                    $failure->save();

                    $application->update(['status' => 'error']);

                    return; // прекращаем попытки
                }

                // Иначе делаем паузу и повторяем
                $failure = CrmFailure::firstOrCreate(
                    ['application_id' => $application->id]
                );
                $failure->attempts += 1;
                $failure->last_attempt_at = now();
                $failure->error_message = $e->getMessage();
                $failure->save();

                sleep(self::RETRY_INTERVAL_SECONDS);
            }
        }
    }
}
