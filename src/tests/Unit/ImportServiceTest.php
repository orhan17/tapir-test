<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ImportService;
use App\Models\Car;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testImportNewCarsSuccess()
    {
        Http::fake([
            'https://tapir.ws/files/new_cars.json' => Http::response([
                [
                    'brand' => 'Toyota',
                    'model' => 'Camry',
                    'vin'   => 'N234567890',
                    'price' => 25000
                ],
                [
                    'brand' => 'BMW',
                    'model' => '320i',
                    'vin'   => 'N345678901',
                    'price' => 35000
                ],
            ], 200),
        ]);

        $service = new ImportService();
        $service->importNewCars();

        $this->assertDatabaseHas('cars', [
            'vin' => 'N234567890',
            'brand' => 'Toyota',
            'model' => 'Camry',
            'is_new' => true,
        ]);
        $this->assertDatabaseHas('cars', [
            'vin' => 'N345678901',
            'brand' => 'BMW',
            'model' => '320i',
            'is_new' => true,
        ]);

        $count = Car::count();
        $this->assertEquals(2, $count);
    }

    public function testImportNewCarsFail()
    {
        Http::fake([
            'https://tapir.ws/files/new_cars.json' => Http::response(null, 500),
        ]);

        $this->expectException(\Exception::class);

        $service = new ImportService();
        $service->importNewCars();

        // Ожидаем, что будет выброшено исключение
    }
}
