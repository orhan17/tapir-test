<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanFilterCarsByYearAndPrice()
    {
        // Предположим, мы создаём несколько машин
        Car::factory()->create([
            'vin' => 'VIN1',
            'year' => 2010,
            'price' => 500000,
        ]);
        Car::factory()->create([
            'vin' => 'VIN2',
            'year' => 2015,
            'price' => 1500000,
        ]);
        Car::factory()->create([
            'vin' => 'VIN3',
            'year' => 2008,
            'price' => 800000,
        ]);

        // Вызываем API
        $response = $this->getJson('/api/stock?year_from=2000&year_to=2010&price_less=1000000');

        $response->assertStatus(200);

        // Проверяем, что в ответе есть VIN1 и VIN3 не подходит (год 2008 за пределами?),
        // зависит от логики (year_from=2000 => 2008 подойдёт, year_to=2010 =>
        // 2008 всё равно входит, но price_less=1000000 => 800000 < 1000000, значит VIN3 тоже подходит.
        // А VIN2 не подходит, т.к. price=1500000 > 1000000
        // Итого VIN1, VIN3 ожидаем.

        $data = $response->json('data');
        // Laravel paginated response => ['data' => [...], 'links' => [...], ...]

        $this->assertCount(2, $data);
        $vins = collect($data)->pluck('vin');
        $this->assertTrue($vins->contains('VIN1'));
        $this->assertTrue($vins->contains('VIN3'));
        $this->assertFalse($vins->contains('VIN2'));
    }
}
