<?php

return [
    'csp_enabled' => env('SECURITY_CSP_ENABLED', env('APP_ENV') === 'production'),
    'csp_report_only' => env('SECURITY_CSP_REPORT_ONLY', false),
];
