<?php

/**
 * El Salvador departments and 44 municipalities (2024 municipal reform, valid 2026).
 * Names in Spanish; codes are stable identifiers for seeding and APIs.
 */
return [
    'departments' => [
        ['code' => 'AH', 'name' => 'Ahuachapán', 'slug' => 'ahuachapan'],
        ['code' => 'SA', 'name' => 'Santa Ana', 'slug' => 'santa-ana'],
        ['code' => 'SO', 'name' => 'Sonsonate', 'slug' => 'sonsonate'],
        ['code' => 'CH', 'name' => 'Chalatenango', 'slug' => 'chalatenango'],
        ['code' => 'LI', 'name' => 'La Libertad', 'slug' => 'la-libertad'],
        ['code' => 'SS', 'name' => 'San Salvador', 'slug' => 'san-salvador'],
        ['code' => 'CU', 'name' => 'Cuscatlán', 'slug' => 'cuscatlan'],
        ['code' => 'PA', 'name' => 'La Paz', 'slug' => 'la-paz'],
        ['code' => 'CA', 'name' => 'Cabañas', 'slug' => 'cabanas'],
        ['code' => 'SV', 'name' => 'San Vicente', 'slug' => 'san-vicente'],
        ['code' => 'US', 'name' => 'Usulután', 'slug' => 'usulutan'],
        ['code' => 'SM', 'name' => 'San Miguel', 'slug' => 'san-miguel'],
        ['code' => 'MO', 'name' => 'Morazán', 'slug' => 'morazan'],
        ['code' => 'UN', 'name' => 'La Unión', 'slug' => 'la-union'],
    ],

    'municipalities' => [
        // Ahuachapán
        ['dept' => 'AH', 'code' => 'AH-N', 'name' => 'Ahuachapán Norte', 'region' => 'Norte', 'cost' => 7.50, 'lat' => 13.9767, 'lng' => -89.8433],
        ['dept' => 'AH', 'code' => 'AH-C', 'name' => 'Ahuachapán Centro', 'region' => 'Centro', 'cost' => 7.00, 'lat' => 13.9203, 'lng' => -89.8450],
        ['dept' => 'AH', 'code' => 'AH-S', 'name' => 'Ahuachapán Sur', 'region' => 'Sur', 'cost' => 8.00, 'lat' => 13.7833, 'lng' => -89.8500],

        // Santa Ana
        ['dept' => 'SA', 'code' => 'SA-N', 'name' => 'Santa Ana Norte', 'region' => 'Norte', 'cost' => 6.50, 'lat' => 14.3333, 'lng' => -89.4500],
        ['dept' => 'SA', 'code' => 'SA-C', 'name' => 'Santa Ana Centro', 'region' => 'Centro', 'cost' => 6.00, 'lat' => 13.9942, 'lng' => -89.5597],
        ['dept' => 'SA', 'code' => 'SA-E', 'name' => 'Santa Ana Este', 'region' => 'Este', 'cost' => 6.50, 'lat' => 13.8667, 'lng' => -89.5000],
        ['dept' => 'SA', 'code' => 'SA-O', 'name' => 'Santa Ana Oeste', 'region' => 'Oeste', 'cost' => 7.00, 'lat' => 13.9167, 'lng' => -89.6833],

        // Sonsonate
        ['dept' => 'SO', 'code' => 'SO-N', 'name' => 'Sonsonate Norte', 'region' => 'Norte', 'cost' => 6.00, 'lat' => 13.8500, 'lng' => -89.7500],
        ['dept' => 'SO', 'code' => 'SO-C', 'name' => 'Sonsonate Centro', 'region' => 'Centro', 'cost' => 5.50, 'lat' => 13.7200, 'lng' => -89.7244],
        ['dept' => 'SO', 'code' => 'SO-E', 'name' => 'Sonsonate Este', 'region' => 'Este', 'cost' => 5.50, 'lat' => 13.4833, 'lng' => -89.5333],
        ['dept' => 'SO', 'code' => 'SO-O', 'name' => 'Sonsonate Oeste', 'region' => 'Oeste', 'cost' => 6.50, 'lat' => 13.5833, 'lng' => -89.8333],

        // Chalatenango
        ['dept' => 'CH', 'code' => 'CH-N', 'name' => 'Chalatenango Norte', 'region' => 'Norte', 'cost' => 9.00, 'lat' => 14.3333, 'lng' => -89.1667],
        ['dept' => 'CH', 'code' => 'CH-C', 'name' => 'Chalatenango Centro', 'region' => 'Centro', 'cost' => 8.50, 'lat' => 14.0333, 'lng' => -88.9333],
        ['dept' => 'CH', 'code' => 'CH-S', 'name' => 'Chalatenango Sur', 'region' => 'Sur', 'cost' => 8.00, 'lat' => 14.0333, 'lng' => -88.9333],

        // La Libertad
        ['dept' => 'LI', 'code' => 'LI-N', 'name' => 'La Libertad Norte', 'region' => 'Norte', 'cost' => 4.00, 'lat' => 13.8333, 'lng' => -89.1833],
        ['dept' => 'LI', 'code' => 'LI-C', 'name' => 'La Libertad Centro', 'region' => 'Centro', 'cost' => 4.50, 'lat' => 13.7500, 'lng' => -89.3667],
        ['dept' => 'LI', 'code' => 'LI-O', 'name' => 'La Libertad Oeste', 'region' => 'Oeste', 'cost' => 4.50, 'lat' => 13.7000, 'lng' => -89.3667],
        ['dept' => 'LI', 'code' => 'LI-E', 'name' => 'La Libertad Este', 'region' => 'Este', 'cost' => 3.50, 'lat' => 13.6769, 'lng' => -89.2797],
        ['dept' => 'LI', 'code' => 'LI-CO', 'name' => 'La Libertad Costa', 'region' => 'Costa', 'cost' => 4.00, 'lat' => 13.4833, 'lng' => -89.3167],
        ['dept' => 'LI', 'code' => 'LI-S', 'name' => 'La Libertad Sur', 'region' => 'Sur', 'cost' => 3.00, 'lat' => 13.6769, 'lng' => -89.2797],

        // San Salvador
        ['dept' => 'SS', 'code' => 'SS-N', 'name' => 'San Salvador Norte', 'region' => 'Norte', 'cost' => 3.50, 'lat' => 13.8167, 'lng' => -89.1833],
        ['dept' => 'SS', 'code' => 'SS-O', 'name' => 'San Salvador Oeste', 'region' => 'Oeste', 'cost' => 3.50, 'lat' => 13.8000, 'lng' => -89.2000],
        ['dept' => 'SS', 'code' => 'SS-E', 'name' => 'San Salvador Este', 'region' => 'Este', 'cost' => 3.00, 'lat' => 13.7083, 'lng' => -89.1500],
        ['dept' => 'SS', 'code' => 'SS-C', 'name' => 'San Salvador Centro', 'region' => 'Centro', 'cost' => 2.50, 'lat' => 13.6929, 'lng' => -89.2182],
        ['dept' => 'SS', 'code' => 'SS-S', 'name' => 'San Salvador Sur', 'region' => 'Sur', 'cost' => 3.50, 'lat' => 13.6333, 'lng' => -89.1833],

        // Cuscatlán
        ['dept' => 'CU', 'code' => 'CU-N', 'name' => 'Cuscatlán Norte', 'region' => 'Norte', 'cost' => 5.00, 'lat' => 13.9333, 'lng' => -89.1500],
        ['dept' => 'CU', 'code' => 'CU-S', 'name' => 'Cuscatlán Sur', 'region' => 'Sur', 'cost' => 4.50, 'lat' => 13.7167, 'lng' => -88.9333],

        // La Paz
        ['dept' => 'PA', 'code' => 'PA-O', 'name' => 'La Paz Oeste', 'region' => 'Oeste', 'cost' => 5.00, 'lat' => 13.4833, 'lng' => -89.0833],
        ['dept' => 'PA', 'code' => 'PA-C', 'name' => 'La Paz Centro', 'region' => 'Centro', 'cost' => 5.50, 'lat' => 13.5000, 'lng' => -88.8667],
        ['dept' => 'PA', 'code' => 'PA-E', 'name' => 'La Paz Este', 'region' => 'Este', 'cost' => 5.50, 'lat' => 13.5000, 'lng' => -88.8667],

        // Cabañas
        ['dept' => 'CA', 'code' => 'CA-E', 'name' => 'Cabañas Este', 'region' => 'Este', 'cost' => 8.00, 'lat' => 13.8667, 'lng' => -88.6333],
        ['dept' => 'CA', 'code' => 'CA-O', 'name' => 'Cabañas Oeste', 'region' => 'Oeste', 'cost' => 8.50, 'lat' => 13.7667, 'lng' => -88.9833],

        // San Vicente
        ['dept' => 'SV', 'code' => 'SV-N', 'name' => 'San Vicente Norte', 'region' => 'Norte', 'cost' => 6.00, 'lat' => 13.6500, 'lng' => -88.7833],
        ['dept' => 'SV', 'code' => 'SV-S', 'name' => 'San Vicente Sur', 'region' => 'Sur', 'cost' => 5.50, 'lat' => 13.6431, 'lng' => -88.7839],

        // Usulután
        ['dept' => 'US', 'code' => 'US-N', 'name' => 'Usulután Norte', 'region' => 'Norte', 'cost' => 8.00, 'lat' => 13.4833, 'lng' => -88.4667],
        ['dept' => 'US', 'code' => 'US-E', 'name' => 'Usulután Este', 'region' => 'Este', 'cost' => 7.50, 'lat' => 13.3500, 'lng' => -88.4500],
        ['dept' => 'US', 'code' => 'US-O', 'name' => 'Usulután Oeste', 'region' => 'Oeste', 'cost' => 8.50, 'lat' => 13.3167, 'lng' => -88.5333],

        // San Miguel
        ['dept' => 'SM', 'code' => 'SM-N', 'name' => 'San Miguel Norte', 'region' => 'Norte', 'cost' => 9.00, 'lat' => 13.5500, 'lng' => -88.1833],
        ['dept' => 'SM', 'code' => 'SM-C', 'name' => 'San Miguel Centro', 'region' => 'Centro', 'cost' => 8.00, 'lat' => 13.4833, 'lng' => -88.1833],
        ['dept' => 'SM', 'code' => 'SM-O', 'name' => 'San Miguel Oeste', 'region' => 'Oeste', 'cost' => 9.00, 'lat' => 13.4167, 'lng' => -88.2500],

        // Morazán
        ['dept' => 'MO', 'code' => 'MO-N', 'name' => 'Morazán Norte', 'region' => 'Norte', 'cost' => 12.00, 'lat' => 13.9500, 'lng' => -88.1333],
        ['dept' => 'MO', 'code' => 'MO-S', 'name' => 'Morazán Sur', 'region' => 'Sur', 'cost' => 10.00, 'lat' => 13.4833, 'lng' => -88.1000],

        // La Unión
        ['dept' => 'UN', 'code' => 'UN-N', 'name' => 'La Unión Norte', 'region' => 'Norte', 'cost' => 14.00, 'lat' => 13.4833, 'lng' => -87.8833],
        ['dept' => 'UN', 'code' => 'UN-S', 'name' => 'La Unión Sur', 'region' => 'Sur', 'cost' => 12.00, 'lat' => 13.3369, 'lng' => -87.8439],
    ],

    // Sample districts (former municipalities) — not the full 262.
    'district_samples' => [
        'SS-C' => ['San Salvador', 'Mejicanos', 'Cuscatancingo', 'Ciudad Delgado', 'Ayutuxtepeque'],
        'SS-E' => ['Soyapango', 'Ilopango', 'San Martín', 'Tonacatepeque'],
        'LI-S' => ['Santa Tecla', 'Comasagua'],
    ],
];
