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
    function getWeekOfMonth(string $date): string
    {
        // Parse the date into a Carbon instance
        $date = Carbon::parse($date);

        // Get the start of the month for the given date
        $startOfMonth = $date->copy()->startOfMonth();

        // Calculate the week number in the month (1-based index)
        $weekNumber = $startOfMonth->diffInWeeks($date) + 1;

        // Cap the week number to 4, if it's greater than 4
        $weekNumber = $weekNumber > 4 ? 4 : $weekNumber;

        // Get the month name (e.g., 'October')
        $monthName = $date->format('M y');

        // Return the week number and month (e.g., "W2 of Oct")
        return "W" . (int) $weekNumber . " $monthName";
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
