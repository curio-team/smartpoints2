<?php

if (! function_exists('locale_number_format')) {
    /**
     * Format a number according to the current locale settings.
     *
     * @param float|int $number The number to format.
     * @param int $precision The number of decimal places to include.
     * @return string The formatted number.
     */
    function locale_number_format($number, $precision = 0)
    {
        // $locale = localeconv();
        // $decimalPoint = $locale['decimal_point'];
        // $thousandsSep = $locale['thousands_sep'];
        // TODO: Get the above to work based on the current locale settings.
        $decimalPoint = ',';
        $thousandsSep = '.';

        $formattedNumber = number_format($number, $precision, $decimalPoint, $thousandsSep);

        return $formattedNumber;
    }
}
