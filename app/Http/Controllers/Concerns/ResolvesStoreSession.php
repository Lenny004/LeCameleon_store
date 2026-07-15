<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait ResolvesStoreSession
{
    protected function sessionId(Request $request, string $configKey): ?string
    {
        $key = config("store.{$configKey}");

        if (! $request->session()->has($key)) {
            return null;
        }

        return (string) $request->session()->get($key);
    }

    protected function ensureSessionId(Request $request, string $configKey): string
    {
        $key = config("store.{$configKey}");
        $sessionId = $this->sessionId($request, $configKey);

        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
            $request->session()->put($key, $sessionId);
        }

        return $sessionId;
    }
}
