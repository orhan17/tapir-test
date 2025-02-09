<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportService;

class ImportUsedCarsCommand extends Command
{
    protected $signature = 'import:used-cars';
    protected $description = 'Import used cars from XML';

    public function handle(ImportService $service)
    {
        $this->info("Importing used cars...");
        try {
            $service->importUsedCars();
            $this->info("Used cars imported successfully.");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
