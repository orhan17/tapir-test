<?php

namespace App\Services;

use App\Models\Car;
use Illuminate\Support\Facades\Http;

class ImportService
{
    public function importNewCars()
    {
        // 1. Скачиваем JSON
        $jsonUrl = 'https://tapir.ws/files/new_cars.json';
        $response = Http::get($jsonUrl);

        if ($response->failed()) {
            throw new \Exception("Error downloading new cars JSON from {$jsonUrl}");
        }

        $carsData = $response->json(); // Массив объектов JSON

        // 2. Обрабатываем
        foreach ($carsData as $carData) {
            Car::updateOrCreate(
                ['vin' => $carData['vin']], // уникальный VIN
                [
                    'brand'   => $carData['brand'] ?? '',
                    'model'   => $carData['model'] ?? '',
                    // 'year' => ?? – так как в JSON нет года, вы можете:
                    //  - сохранять null
                    //  - или ставить какую-то "заглушку" (например, 2023)
                    'year'    => 2023,
                    'price'   => $carData['price'] ?? 0,
                    'mileage' => 0,         // для новых авто
                    'is_new'  => true,
                ]
            );
        }
    }


    public function importUsedCars()
    {
        // 1. Скачиваем XML
        $xmlUrl = 'https://tapir.ws/files/used_cars.xml';
        $response = Http::get($xmlUrl);

        if ($response->failed()) {
            throw new \Exception("Error downloading used cars XML from {$xmlUrl}");
        }

        // 2. Парсим XML
        $xml = simplexml_load_string($response->body());

        // Внутри <vehicles> лежат теги <vehicle>, а не <car>
        foreach ($xml->vehicle as $carItem) {
            // Обратите внимание, что названия полей:
            // <brand>, <model>, <vin>, <price>, <year>, <mileage>
            $brand   = (string) $carItem->brand;
            $model   = (string) $carItem->model;
            $vin     = (string) $carItem->vin;
            $price   = (float)  $carItem->price;
            $year    = (int)    $carItem->year;
            $mileage = (int)    $carItem->mileage;

            // 3. Сохраняем
            Car::updateOrCreate(
                ['vin' => $vin],
                [
                    'brand'   => $brand,
                    'model'   => $model,
                    'year'    => $year,
                    'price'   => $price,
                    'mileage' => $mileage,
                    'is_new'  => false, // т.к. это б/у
                ]
            );
        }
    }

}

