<?php

namespace App\Helpers;

class NumberToWordsHelper
{
    /**
     * Convert a number to English words.
     *
     * @param float|int $number
     * @param string $currency
     * @param bool $isNested
     * @return string|false
     */
    public static function convert($number, $currency = 'NGN', $isNested = false)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $dictionary  = [
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'forty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        $floatVal = (float)$number;
        if ($floatVal < 0) {
            return $negative . self::convert(abs($floatVal), $currency, $isNested);
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        $number = (int)$number;

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = (int)($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::convert($remainder, $currency, true);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convert($numBaseUnits, $currency, true) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert($remainder, $currency, true);
                }
                break;
        }

        if (!$isNested) {
            if (null !== $fraction && is_numeric($fraction) && (int)$fraction > 0) {
                // Only handle up to 2 decimal places for currency
                $fraction = substr($fraction, 0, 2);
                if (strlen($fraction) == 1) {
                    $fraction .= '0';
                }
                $fractionVal = (int)$fraction;
                if ($currency === 'NGN') {
                    $string .= ' Naira and ' . self::convert($fractionVal, $currency, true) . ' Kobo';
                } else {
                    $string .= ' ' . $currency . ' and ' . self::convert($fractionVal, $currency, true) . ' Cents';
                }
            } else {
                if ($currency === 'NGN') {
                    $string .= ' Naira';
                } else {
                    $string .= ' ' . $currency;
                }
            }

            // Title case and clean spacing
            $string = ucwords(trim(preg_replace('/\s+/', ' ', $string)));
            
            // Append "Only"
            $string .= ' Only';
        } else {
            // Title case for nested part
            $string = ucwords(trim(preg_replace('/\s+/', ' ', $string)));
        }

        return $string;
    }
}
