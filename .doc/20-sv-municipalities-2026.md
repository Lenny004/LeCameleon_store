# El Salvador municipalities (2026 baseline)

Post-reform territorial structure used by `ElSalvadorGeoSeeder`: **14 departments**, **44 municipalities**.

Source of truth: `database/seeders/data/el_salvador_geo.php`

| Code | Department | Municipality | Region |
|------|------------|----------------|--------|
| AH-N | Ahuachapán | Ahuachapán Norte | Norte |
| AH-C | Ahuachapán | Ahuachapán Centro | Centro |
| AH-S | Ahuachapán | Ahuachapán Sur | Sur |
| SA-N | Santa Ana | Santa Ana Norte | Norte |
| SA-C | Santa Ana | Santa Ana Centro | Centro |
| SA-E | Santa Ana | Santa Ana Este | Este |
| SA-O | Santa Ana | Santa Ana Oeste | Oeste |
| SO-N | Sonsonate | Sonsonate Norte | Norte |
| SO-C | Sonsonate | Sonsonate Centro | Centro |
| SO-E | Sonsonate | Sonsonate Este | Este |
| SO-O | Sonsonate | Sonsonate Oeste | Oeste |
| CH-N | Chalatenango | Chalatenango Norte | Norte |
| CH-C | Chalatenango | Chalatenango Centro | Centro |
| CH-S | Chalatenango | Chalatenango Sur | Sur |
| LI-N | La Libertad | La Libertad Norte | Norte |
| LI-C | La Libertad | La Libertad Centro | Centro |
| LI-O | La Libertad | La Libertad Oeste | Oeste |
| LI-E | La Libertad | La Libertad Este | Este |
| LI-CO | La Libertad | La Libertad Costa | Costa |
| LI-S | La Libertad | La Libertad Sur | Sur |
| SS-N | San Salvador | San Salvador Norte | Norte |
| SS-O | San Salvador | San Salvador Oeste | Oeste |
| SS-E | San Salvador | San Salvador Este | Este |
| SS-C | San Salvador | San Salvador Centro | Centro |
| SS-S | San Salvador | San Salvador Sur | Sur |
| CU-N | Cuscatlán | Cuscatlán Norte | Norte |
| CU-S | Cuscatlán | Cuscatlán Sur | Sur |
| PA-O | La Paz | La Paz Oeste | Oeste |
| PA-C | La Paz | La Paz Centro | Centro |
| PA-E | La Paz | La Paz Este | Este |
| CA-E | Cabañas | Cabañas Este | Este |
| CA-O | Cabañas | Cabañas Oeste | Oeste |
| SV-N | San Vicente | San Vicente Norte | Norte |
| SV-S | San Vicente | San Vicente Sur | Sur |
| US-N | Usulután | Usulután Norte | Norte |
| US-E | Usulután | Usulután Este | Este |
| US-O | Usulután | Usulután Oeste | Oeste |
| SM-N | San Miguel | San Miguel Norte | Norte |
| SM-C | San Miguel | San Miguel Centro | Centro |
| SM-O | San Miguel | San Miguel Oeste | Oeste |
| MO-N | Morazán | Morazán Norte | Norte |
| MO-S | Morazán | Morazán Sur | Sur |
| UN-N | La Unión | La Unión Norte | Norte |
| UN-S | La Unión | La Unión Sur | Sur |

**Warehouse default:** `SS-C` (San Salvador Centro) — set via `LogisticsDemoSeeder` → `settings.warehouse_municipality_id`.

**Sample districts** (nested under former municipalities, not full 262): `SS-C`, `SS-E`, `LI-S` — see seeder `district_samples`.
