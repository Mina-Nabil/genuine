<?php

use App\Models\Customers\Customer;
use App\Models\Payments\CustomerPayment;
use Carbon\Carbon;

/**
 * Get the week of the month for a given date.
 *
 * @param string $date
 * @return string
 */
if (!function_exists('getWeekOfMonth')) {
    function getWeekOfMonth(string|Carbon $date): string
    {
        // Parse the date into a Carbon instance
        if (!is_a($date, Carbon::class))
            $date = Carbon::parse($date);

        if ($date->day >= 1 && $date->day <= 7)
            $week = 1;
        if ($date->day >= 8 && $date->day <= 14)
            $week = 2;
        if ($date->day >= 15 && $date->day <= 21)
            $week = 3;
        else
            $week = 4;
        // Get the month name (e.g., 'October')
        $monthName = $date->format('M y');

        // Return the week number and month (e.g., "W2 of Oct")
        return "W" . (int) $week . " $monthName";
    }
}
/**
 * Get the week of the month for a given date.
 *
 * @param string $date
 * @return string
 */
if (!function_exists('joined')) {
    function joined($query, $table)
    {
        $joins = $query->getQuery()->joins;
        if ($joins == null) {
            return false;
        }
        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Get the arabic name of each method
 *
 * @param string $date
 * @return string
 */
if (!function_exists('paymentMethodInArabic')) {
    function paymentMethodInArabic($method)
    {
        switch ($method) {
            case CustomerPayment::PYMT_CASH:
                return "كاش";
            case CustomerPayment::PYMT_WALLET:
                return "محفظه";
            case CustomerPayment::PYMT_BANK_TRANSFER:
                return "تحويل بنكي";
        }
    }
}
