<?php

declare(strict_types=1);

if (!function_exists('fixtures_path')) {
    function fixtures_path(string $relativePath): string
    {
        return base_path('tests/Fixtures/' . ltrim($relativePath, '/'));
    }
}

if (!function_exists('encodeLikeLaravel')) {
    function encodeLikeLaravel(string $toBeEncoded): string
    {
        return htmlspecialchars($toBeEncoded, ENT_QUOTES | ENT_HTML401);
    }
}
