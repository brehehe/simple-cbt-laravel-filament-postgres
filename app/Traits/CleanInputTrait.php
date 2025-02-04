<?php

namespace App\Traits;

trait CleanInputTrait
{
    /**
     * Clean input string by removing excessive spaces and converting special cases to null.
     * Special cases:
     * - Single hyphen "-"
     * - Empty string ""
     * - String containing only spaces
     * - String "(blank)"
     *
     * @param string|null $input
     * @return string|null
     */
    protected static function _cleanInput(
        ?string $input
    ): ?string {
        return match (true) {
            $input === null => null,
            trim($input) === '' => null,
            trim($input) === '-' => null,
            strtolower(trim($input)) === '(blank)' => null,
            default => preg_replace('/\s+/', ' ', trim($input)) ?: null
        };
    }
}