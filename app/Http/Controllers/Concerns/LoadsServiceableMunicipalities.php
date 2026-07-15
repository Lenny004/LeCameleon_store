<?php

namespace App\Http\Controllers\Concerns;

use App\Models\SvDepartment;
use Illuminate\Support\Collection;

/**
 * Loads active SV departments with their serviceable municipalities for storefront selects.
 */
trait LoadsServiceableMunicipalities
{
  /**
   * @return Collection<int, SvDepartment>
   */
  protected function serviceableDepartmentsWithMunicipalities(): Collection
  {
    return SvDepartment::query()
      ->where('is_active', true)
      ->with([
        'municipalities' => fn ($query) => $query
          ->where('is_active', true)
          ->orderBy('name'),
      ])
      ->orderBy('name')
      ->get()
      ->filter(fn (SvDepartment $department) => $department->municipalities->isNotEmpty())
      ->values();
  }
}
