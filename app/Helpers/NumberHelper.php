<?php

if (!function_exists('short_amount')) {
    function short_amount($number)
    {
        if ($number < 1000) {
            return number_format($number);
        }

        if ($number < 1000000) {
            return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'k';
        }

        if ($number < 1000000000) {
            return rtrim(rtrim(number_format($number / 1000000, 1), '0'), '.') . 'm';
        }

        return rtrim(rtrim(number_format($number / 1000000000, 1), '0'), '.') . 'b';
    }
}
