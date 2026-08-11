<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | "fake" runs the whole pipeline against a deterministic stand-in, so the
    | app is usable — and the test suite is fast and free — with no API key.
    | Set LLM_DRIVER=anthropic once a key is present.
    |
    */

    'driver' => env('LLM_DRIVER', 'fake'),

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),

        'model' => env('ANTHROPIC_MODEL', 'claude-opus-5'),

        // Storefront copy is short and well-specified; low effort keeps spend
        // and latency down without a quality cost worth paying for here.
        'effort' => env('ANTHROPIC_EFFORT', 'low'),
    ],

];
