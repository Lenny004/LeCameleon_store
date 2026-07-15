<?php

namespace Database\Seeders;

use App\Enums\LogisticsWorkerRole;
use App\Enums\VehicleType;
use App\Enums\WarningAppliesTo;
use App\Enums\WarningSeverity;
use App\Models\DeliveryWarning;
use App\Models\DispatchSchedule;
use App\Models\LogisticsCompany;
use App\Models\LogisticsVehicle;
use App\Models\LogisticsWorker;
use App\Models\Setting;
use App\Models\ShippingZone;
use App\Models\ShippingZoneRate;
use App\Models\SvMunicipality;
use Illuminate\Database\Seeder;

class LogisticsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = SvMunicipality::query()->where('code', 'SS-C')->firstOrFail();
        $libertadSur = SvMunicipality::query()->where('code', 'LI-S')->firstOrFail();
        $santaAnaCentro = SvMunicipality::query()->where('code', 'SA-C')->firstOrFail();

        Setting::query()->updateOrCreate(
            ['key' => 'warehouse_municipality_id'],
            ['value' => ['id' => $warehouse->id, 'code' => $warehouse->code]],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'shipping_warnings'],
            ['value' => [
                'rainy_season' => 'Delivery may be delayed during heavy rain in eastern zones.',
                'holiday_peak' => 'Allow an extra business day around public holidays.',
            ]],
        );

        $metroZone = ShippingZone::query()->updateOrCreate(
            ['code' => 'ZONE-METRO'],
            [
                'name' => 'Área Metropolitana',
                'description' => 'San Salvador Centro and immediate metro municipalities.',
                'sv_municipality_id' => $warehouse->id,
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $libertadZone = ShippingZone::query()->updateOrCreate(
            ['code' => 'ZONE-LIBERTAD-SUR'],
            [
                'name' => 'La Libertad Sur',
                'description' => 'Santa Tecla and coastal west corridor.',
                'sv_municipality_id' => $libertadSur->id,
                'sort_order' => 2,
                'is_active' => true,
            ],
        );

        $occidenteZone = ShippingZone::query()->updateOrCreate(
            ['code' => 'ZONE-OCCIDENTE'],
            [
                'name' => 'Occidente',
                'description' => 'Western corridor including Santa Ana Centro.',
                'sv_municipality_id' => $santaAnaCentro->id,
                'sort_order' => 3,
                'is_active' => true,
            ],
        );

        ShippingZoneRate::query()->updateOrCreate(
            [
                'origin_zone_id' => $metroZone->id,
                'destination_zone_id' => $libertadZone->id,
            ],
            [
                'base_fee' => 3.50,
                'per_km_fee' => 0.25,
                'min_fee' => 3.00,
                'max_fee' => 8.00,
                'estimated_hours' => 2.0,
                'is_active' => true,
            ],
        );

        ShippingZoneRate::query()->updateOrCreate(
            [
                'origin_zone_id' => $metroZone->id,
                'destination_zone_id' => $occidenteZone->id,
            ],
            [
                'base_fee' => 5.00,
                'per_km_fee' => 0.35,
                'min_fee' => 5.00,
                'max_fee' => 12.00,
                'estimated_hours' => 4.0,
                'is_active' => true,
            ],
        );

        ShippingZoneRate::query()->updateOrCreate(
            [
                'origin_zone_id' => $libertadZone->id,
                'destination_zone_id' => $occidenteZone->id,
            ],
            [
                'base_fee' => 4.50,
                'per_km_fee' => 0.30,
                'min_fee' => 4.50,
                'max_fee' => 10.00,
                'estimated_hours' => 3.5,
                'is_active' => true,
            ],
        );

        $company = LogisticsCompany::query()->updateOrCreate(
            ['tax_id' => '0614-120589-102-3'],
            [
                'name' => 'Cameleon Express SV',
                'legal_name' => 'Cameleon Express de El Salvador S.A. de C.V.',
                'trade_name' => 'Cameleon Express',
                'email' => 'logistica@lecameleon.store',
                'phone' => '+503 2222-3344',
                'address_line' => 'Colonia Escalón, Calle La Mascota #123',
                'sv_municipality_id' => $warehouse->id,
                'website' => 'https://lecameleon.store',
                'contact_person' => 'María Elena Rivas',
                'is_active' => true,
                'notes' => 'Primary last-mile partner for the boutique warehouse.',
            ],
        );

        $driver = LogisticsWorker::query()->updateOrCreate(
            ['employee_code' => 'CEX-DRV-001'],
            [
                'logistics_company_id' => $company->id,
                'first_name' => 'Carlos',
                'last_name' => 'Henríquez',
                'document_id' => '04567890-1',
                'phone' => '+503 7012-3456',
                'email' => 'carlos.henriquez@lecameleon.store',
                'role' => LogisticsWorkerRole::Driver,
                'hire_date' => '2024-06-01',
                'is_active' => true,
            ],
        );

        LogisticsWorker::query()->updateOrCreate(
            ['employee_code' => 'CEX-COL-001'],
            [
                'logistics_company_id' => $company->id,
                'first_name' => 'Ana',
                'last_name' => 'Martínez',
                'document_id' => '07891234-5',
                'phone' => '+503 7890-1234',
                'email' => 'ana.martinez@lecameleon.store',
                'role' => LogisticsWorkerRole::Collector,
                'hire_date' => '2024-08-15',
                'is_active' => true,
            ],
        );

        LogisticsVehicle::query()->updateOrCreate(
            ['plate_number' => 'P123456'],
            [
                'logistics_company_id' => $company->id,
                'logistics_worker_id' => $driver->id,
                'brand' => 'Toyota',
                'model' => 'Hiace',
                'year' => 2021,
                'color' => 'Blanco',
                'vehicle_type' => VehicleType::Van,
                'capacity_kg' => 800,
                'is_active' => true,
            ],
        );

        LogisticsVehicle::query()->updateOrCreate(
            ['plate_number' => 'M789012'],
            [
                'logistics_company_id' => $company->id,
                'brand' => 'Honda',
                'model' => 'XR190',
                'year' => 2023,
                'color' => 'Rojo',
                'vehicle_type' => VehicleType::Motorcycle,
                'capacity_kg' => 25,
                'is_active' => true,
            ],
        );

        DispatchSchedule::query()->updateOrCreate(
            ['name' => 'Despacho diario AM'],
            [
                'next_dispatch_at' => now()->addDay()->setTime(9, 0),
                'cutoff_at' => now()->addDay()->setTime(7, 30),
                'is_active' => true,
                'notes' => 'Morning pickup from warehouse after order cutoff.',
            ],
        );

        DeliveryWarning::query()->updateOrCreate(
            ['code' => 'rain-delay'],
            [
                'title' => 'Posibles retrasos por lluvia',
                'body' => 'Durante la temporada lluviosa las entregas al oriente del país pueden tomar un día adicional.',
                'severity' => WarningSeverity::Warning,
                'applies_to' => WarningAppliesTo::Checkout,
                'is_active' => true,
            ],
        );

        DeliveryWarning::query()->updateOrCreate(
            ['code' => 'id-required'],
            [
                'title' => 'Identificación requerida',
                'body' => 'El mensajero puede solicitar DUI o pasaporte al momento de la entrega.',
                'severity' => WarningSeverity::Info,
                'applies_to' => WarningAppliesTo::Tracking,
                'is_active' => true,
            ],
        );
    }
}
