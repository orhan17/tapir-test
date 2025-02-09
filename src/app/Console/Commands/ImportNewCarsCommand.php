<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportService;

class ImportNewCarsCommand extends Command
{
    protected $signature = 'import:new-cars';
    protected $description = 'Import new cars from JSON';

    public function handle(ImportService $service)
    {
        $this->info("Importing new cars...");
        try {
            $service->importNewCars();
            $this->info("New cars imported successfully.");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
