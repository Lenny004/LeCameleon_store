<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\ShippingZone;

class PaymentSettingsService
{
    public function transfer(): array
    {
        $value = Setting::getValue('payments.transfer', []);

        return array_merge([
            'enabled' => false,
            'bank' => '',
            'account_holder' => '',
            'account_number' => '',
            'account_type' => 'savings',
            'instructions' => '',
        ], is_array($value) ? $value : []);
    }

    public function cod(): array
    {
        $value = Setting::getValue('payments.cod', []);

        return array_merge([
            'enabled' => false,
            'zone_ids' => [],
            'max_amount' => null,
            'note' => '',
        ], is_array($value) ? $value : []);
    }

    public function transferAvailable(): bool
    {
        $settings = $this->transfer();

        return (bool) $settings['enabled']
            && trim((string) $settings['bank']) !== ''
            && trim((string) $settings['account_holder']) !== ''
            && trim((string) $settings['account_number']) !== '';
    }

    public function codAvailable(?int $municipalityId = null, ?float $amount = null): bool
    {
        $settings = $this->cod();

        if (! (bool) $settings['enabled']) {
            return false;
        }

        $zoneIds = collect($settings['zone_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->values();
        if ($zoneIds->isNotEmpty() && $municipalityId !== null) {
            $zoneId = ShippingZone::query()->where('sv_municipality_id', $municipalityId)->value('id');
            if (! $zoneId || ! $zoneIds->contains((int) $zoneId)) {
                return false;
            }
        } elseif ($zoneIds->isNotEmpty() && $municipalityId === null) {
            return false;
        }

        $maxAmount = $settings['max_amount'];

        return $maxAmount === null || $maxAmount === '' || $amount === null || $amount <= (float) $maxAmount;
    }

    /** @return array<int, int> */
    public function codMunicipalityIds(): array
    {
        $zoneIds = collect($this->cod()['zone_ids'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->values();

        if ($zoneIds->isEmpty()) {
            return [];
        }

        return ShippingZone::query()
            ->whereIn('id', $zoneIds)
            ->whereNotNull('sv_municipality_id')
            ->pluck('sv_municipality_id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    public function codEnabled(): bool
    {
        return (bool) $this->cod()['enabled'];
    }

    public function codMaxAmount(): ?float
    {
        $maxAmount = $this->cod()['max_amount'];

        return $maxAmount === null || $maxAmount === '' ? null : (float) $maxAmount;
    }

    public function availableMethods(?int $municipalityId = null, ?float $amount = null): array
    {
        $methods = [];

        if ($this->transferAvailable()) {
            $methods['transfer'] = $this->transfer();
        }
        if ($this->codAvailable($municipalityId, $amount)) {
            $methods['cod'] = $this->cod();
        }

        return $methods;
    }

    public function instructions(string $method, float $amount, string $orderNumber): array
    {
        if ($method === 'transfer') {
            $settings = $this->transfer();

            return [
                'title' => 'Instrucciones de transferencia',
                'lines' => [
                    'Banco: '.$settings['bank'],
                    'Titular: '.$settings['account_holder'],
                    'Cuenta: '.$settings['account_number'],
                    'Tipo: '.($settings['account_type'] === 'checking' ? 'Corriente' : 'Ahorro'),
                    'Monto exacto: $'.number_format($amount, 2),
                    'Referencia: '.$orderNumber,
                ],
                'extra' => $settings['instructions'],
            ];
        }

        $settings = $this->cod();

        return [
            'title' => 'Pago contra entrega',
            'lines' => ['Ten listo el monto exacto: $'.number_format($amount, 2)],
            'extra' => $settings['note'],
        ];
    }
}
