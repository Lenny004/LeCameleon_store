<?php

namespace Tests\Concerns;

use Database\Seeders\ElSalvadorGeoSeeder;
use Database\Seeders\LogisticsDemoSeeder;

trait SeedsSvLogistics
{
    protected function seedSvGeo(): void
    {
        $this->seed(ElSalvadorGeoSeeder::class);
    }

    protected function seedSvLogisticsDemo(): void
    {
        $this->seedSvGeo();
        $this->seed(LogisticsDemoSeeder::class);
    }
}
