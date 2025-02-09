<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CRMService;
use App\Models\Application;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CRMServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Можно создать тестовую запись
        $this->car = Car::factory()->create([
            'vin' => 'TESTVIN123',
        ]);
        $this->application = Application::factory()->create([
            'car_id' => $this->car->id,
            'phone'  => '+79999999999',
        ]);
    }

    public function testSendApplicationToCRMOk()
    {
        // Подделываем успешный ответ от CRM
        Http::fake([
            'https://crm.tapir.ws/api/crm' => Http::response([], 200),
        ]);

        $crm = new CRMService();
        $crm->sendApplicationToCRM($this->application);

        // Проверяем, что заявка обновилась на status=sent
        $this->assertEquals('sent', $this->application->fresh()->status);
        $this->assertNotNull($this->application->fresh()->crm_sent_at);
    }

    public function testSendApplicationToCRMFail()
    {
        // Подделываем ответ 500
        Http::fake([
            'https://crm.tapir.ws/api/crm' => Http::response([], 500),
        ]);

        // Отключаем реальную задержку sleep, чтобы тест не тормозил
        // (можно сделать mock sleep или переопределить RETRY_INTERVAL_SECONDS = 0)
        // Но для примера опустим.

        $crm = new CRMService();

        // Мы ожидаем, что сервис будет пытаться 5 минут,
        // но в тесте можем не ждать реально 5 минут :)
        // Можно занизить MAX_TIME_MINUTES до 0.01 (в сервисе),
        // или просто посмотреть, что в итоге заявка станет "error"
        // и CrmFailure будет создан.

        $crm->sendApplicationToCRM($this->application);

        // Проверяем, что статус 'error'
        $this->assertEquals('error', $this->application->fresh()->status);
    }
}
