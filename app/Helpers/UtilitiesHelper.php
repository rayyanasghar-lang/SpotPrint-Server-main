<?php

namespace App\Helpers;


class UtilitiesHelper
{
    private static $number_of_encode_cycles = 2;

    public static function encodeBase64($string)
    {
        for ($i = 0; $i < self::$number_of_encode_cycles; $i++) {
            $string = base64_encode($string);
        }

        return $string;
    }

    public static function decodeBase64($encodedString)
    {
        for ($i = 0; $i < self::$number_of_encode_cycles; $i++) {
            $encodedString = base64_decode($encodedString);
        }

        return $encodedString;
    }
}
