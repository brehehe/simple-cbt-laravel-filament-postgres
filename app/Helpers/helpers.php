<?php

if (!function_exists('sanitize_int')) {
    /**
     * Cleans the value by removing all illegal characters from the number
     * and converts it into an integer.
     *
     * This function is useful for sanitizing user input or data
     * that may contain unwanted characters, ensuring
     * that the returned value is a valid integer.
     *
     * @param mixed $value The value to be cleaned.
     * @return int The cleaned integer value.
     */
    function sanitize_int($value): int
    {
        // Remove all illegal characters from the number
        $sanitizedValue = filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        // Convert the cleaned value to an integer
        return intval($sanitizedValue);
    }
}

if (!function_exists('to_carbon')) {
    /**
     * Converts various date formats to Carbon instance.
     * Returns null if the conversion fails or if the date is invalid.
     *
     * This function handles multiple date formats including:
     * - Standard date strings (Y-m-d, d/m/Y, etc.)
     * - Indonesian date formats with month names
     * - Excel date serial numbers (e.g., 37338 = 23/03/2002)
     * - Existing Carbon instances
     * - DateTime objects
     *
     * @param mixed $value The date value to be converted
     * @param string $timezone The timezone to be used (default: Asia/Jakarta)
     * @return Carbon\Carbon|null Returns Carbon instance or null if conversion fails
     */
    function to_carbon($value, string $timezone = 'Asia/Jakarta'): ?Carbon\Carbon
    {
        // Handle null or empty string
        if ($value === null || $value === '') {
            return null;
        }

        // If already a Carbon instance, just set timezone
        if ($value instanceof Carbon\Carbon) {
            return $value->setTimezone($timezone);
        }

        // If DateTime instance, convert to Carbon
        if ($value instanceof DateTime) {
            return Carbon\Carbon::instance($value)->setTimezone($timezone);
        }

        try {
            // Handle Excel date serial number
            if (is_numeric($value)) {
                $excel_value = intval($value);

                // Excel date serial must be positive and reasonable
                // (between 1 = 1/1/1900 and 2958465 = 31/12/9999)
                if ($excel_value > 0 && $excel_value < 2958465) {
                    return Carbon\Carbon::createFromDate(1899, 12, 30)
                        ->addDays($excel_value)
                        ->setTimezone($timezone);
                }
            }

            // Convert to string if not already
            $date_string = is_string($value) ? $value : (string) $value;

            // Handle common Indonesian date formats
            $date_string = str_replace(
                [
                    'januari', 'februari', 'maret', 'april', 'mei', 'juni',
                    'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
                ],
                [
                    'january', 'february', 'march', 'april', 'may', 'june',
                    'july', 'august', 'september', 'october', 'november', 'december'
                ],
                strtolower($date_string)
            );

            // Parse date string to Carbon
            $carbon_date = Carbon\Carbon::parse($date_string, $timezone);

            // Validate year is reasonable (between 1900 and 2100)
            if ($carbon_date->year < 1900 || $carbon_date->year > 2100) {
                return null;
            }

            return $carbon_date;

        } catch (Exception $e) {
            return null;
        }
    }
}