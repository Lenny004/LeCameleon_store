<?php

namespace Database\Seeders;

use App\Models\SvDepartment;
use App\Models\SvDistrict;
use App\Models\SvMunicipality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ElSalvadorGeoSeeder extends Seeder
{
    public function run(): void
    {
        $data = require __DIR__.'/data/el_salvador_geo.php';
        $departmentIds = [];

        foreach ($data['departments'] as $department) {
            $record = SvDepartment::query()->updateOrCreate(
                ['code' => $department['code']],
                [
                    'name' => $department['name'],
                    'slug' => $department['slug'],
                    'is_active' => true,
                ],
            );

            $departmentIds[$department['code']] = $record->id;
        }

        $municipalityIds = [];

        foreach ($data['municipalities'] as $municipality) {
            $record = SvMunicipality::query()->updateOrCreate(
                ['code' => $municipality['code']],
                [
                    'sv_department_id' => $departmentIds[$municipality['dept']],
                    'name' => $municipality['name'],
                    'slug' => Str::slug($municipality['name']),
                    'region_label' => $municipality['region'],
                    'base_shipping_cost' => $municipality['cost'],
                    'latitude' => $municipality['lat'],
                    'longitude' => $municipality['lng'],
                    'is_active' => true,
                ],
            );

            $municipalityIds[$municipality['code']] = $record->id;
        }

        foreach ($data['district_samples'] as $municipalityCode => $districtNames) {
            $municipalityId = $municipalityIds[$municipalityCode] ?? null;

            if ($municipalityId === null) {
                continue;
            }

            foreach ($districtNames as $districtName) {
                SvDistrict::query()->updateOrCreate(
                    [
                        'sv_municipality_id' => $municipalityId,
                        'slug' => Str::slug($districtName),
                    ],
                    [
                        'name' => $districtName,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
