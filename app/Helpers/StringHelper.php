<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * Функция склонения слов
     *
     * MedString::declension($seconds, array('секунда','секунды','секунд'));
     *
     * @param int $digit
     * @param array $expr
     * @param bool $onlyword
     * @return
     */
    public static function declension(int $digit, array $expr, bool $onlyword = false): string
    {
        if (empty($expr[2])) $expr[2] = $expr[1];
        $i = preg_replace('/[^0-9]+/s', '', $digit) % 100;
        if ($onlyword) $digit = '';
        if ($i >= 5 && $i <= 20) $res = $digit . ' ' . $expr[2];
        else {
            $i %= 10;
            if ($i == 1) $res = $digit . ' ' . $expr[0];
            elseif ($i >= 2 && $i <= 4) $res = $digit . ' ' . $expr[1];
            else $res = $digit . ' ' . $expr[2];
        }
        return trim($res);
    }
}
