<?php
/**
 * Created by PhpStorm.
 * User: kristjan
 * Date: 9/18/14
 * Time: 8:25 AM
 */

namespace Application\Util;


class StringUtil {
    private static function replaceNewlines($string, $delimiter = '-') {

        return str_replace(array (PHP_EOL, "\r"), $delimiter, $string);
    }

    public static function urlify($string, array $allowedCharacters = array (), $delimiter = '-') {
        $urlValue = strtolower(self::urlifyPreserveCase($string, $allowedCharacters, $delimiter));

        return $urlValue;
    }

    public static function urlifyPreserveCase($string, array $allowedCharacters = array (), $delimiter = '-') {
        $urlValue = trim(preg_replace(array('~[^0-9a-z' . implode('', $allowedCharacters) . ']~i', '~' . $delimiter . '+~'), $delimiter, preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'))), $delimiter);

        return $urlValue;
    }

    /* NOTE: Make sure the route match supports the same symbols that are defined here */
    public static function urlifyUtf8($string, array $allowedCharacters = array(), $delimiter = ' ') {
        $urlValue = $string;
        $urlValue = str_replace('/', '', $urlValue);
        $urlValue = self::replaceNewlines($urlValue, $delimiter);
        $urlValue = preg_replace(array('~[?%]~iu', '~[^\x{0000}-\x{FFFF}a-zA-Z0-9.,\~!*()\'_' . implode('', $allowedCharacters) . '-]~iu', '~' . $delimiter . '+~'), $delimiter, $urlValue);
        $urlValue = trim($urlValue);

        return $urlValue;
    }

    public static function urlifyUtf8Clean($string, array $allowedCharacters = array(), $delimiter = ' ') {
        $urlValue = $string;
        $urlValue = str_replace('/', '', $urlValue);
        $urlValue = self::replaceNewlines($urlValue, $delimiter);
        $urlValue = preg_replace(array (
            '~[?% ,.!]~iu',
            '~[^\x{00A1}-\x{FFFF}a-z0-9_-]~iu',
            '~' . $delimiter . '+~'
        ), $delimiter, $urlValue);

        $urlValue = trim($urlValue);

        return $urlValue;
    }

    public static function __callStatic($name, $parameters) {

        throw new \RuntimeException(sprintf('Tried to call %1$s from StringUtil. Probable cause might be malformed Slug annotation.', $name));
    }

    /**
     * Finds a substring with minimum length $length while preserving whole words, adds "..." to the end.
     * @param string $str
     * @param int $length
     * @param int $minword
     */
    public static function subString($str, $length, $minword = 3, $encoding = null) {
        $sub = '';
        $len = 0;

        mb_internal_encoding(mb_detect_encoding($str));
        if ($encoding != null) {
            mb_internal_encoding($encoding);
        }

        foreach (explode(' ', $str) as $word) {
            $part = (($sub != '') ? ' ' : '') . $word;

            if (
                mb_strlen($word) > $minword &&
                (mb_strlen($sub) + mb_strlen($part)) >= $length
            ) {
                break;
            }
            $sub .= $part;
            $len += mb_strlen($part);
        }
        return $sub . (($len < mb_strlen($str)) ? '...' : '');
    }

    /**
     * Finds a substring with exact length $length, adds "..." to the end.
     * Uses mb_* functions and probably needs to have mb.internal_encoding set to UTF-8. Because other htmlentities and htmlspecialchars may fail otherwise.
     * http://insomanic.me.uk/post/191397106/php-htmlspecialchars-htmlentities-invalid
     * @param string $str
     * @param int $length
     */
    public static function subStringBrutal($str, $length, $encoding = null){
        $sub = '';
        $len = 0;

        mb_internal_encoding(mb_detect_encoding($str));
        if ($encoding != null) {
            mb_internal_encoding($encoding);
        }

        if(mb_strlen($str) > $length) {
            $sub = mb_substr($str, 0, $length-3) . '...';
        } else {
            $sub = $str;
        }

        return $sub;
    }

    /**
     * Finds the substring of the $string that has at most $length letters with the $suffix appended (if needed)
     * Allows for custom $delimiter and $suffix.
     * Uses mb_ereg_* functions that have less complexity than the explode and foreach functions used in other subString function.
     * @param string $string
     * @param int $length
     * @param string $delimiter
     * @param string $suffix
     * @return string
     */
    public static function subStringCustom($string, $length, $delimiter = ' ', $suffix = '...') {

        mb_internal_encoding(mb_detect_encoding($string)); //Detect current string encoding

        if (mb_strlen($string) <= $length) { //String is already shorter than the length, return the string

            return $string;
        }
        $length -= mb_strlen($suffix); //We subtract the suffix's length from our goal

        //We find matches of $delimiter that have at most the $length characters before it
        $pattern = '.{0,' . $length . '}' . $delimiter;
        mb_ereg_search_init ($string, $pattern);
        $latestMatchPosition = mb_ereg_search_pos();

        return mb_substr($string, 0, end($latestMatchPosition) - 1) . $suffix; //Append suffix and return result
    }

    /**
     * Returns a random string of the given $length
     * @param int $length
     * @return string
     */
    public static function getRandomString($length) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
            'abcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * Limits string to number of lines, adds "..." to the end
     *
     * @param string $string
     * @param int $limit
     */
    public static function stringLinesLimiter($string, $linesLimit, $removeEmptyLines = false, $stringLengthLimit = 189, $lineStringLengthLimit = 63) {
        $lines = explode(PHP_EOL, $string);
        if ($removeEmptyLines) {
            for ($i = 0; $i < count($lines); $i++) {
                $element = trim(self::replaceNewlines($lines[$i], ''));
                if ($element == '') {
                    unset($lines[$i]);
                }
            }
        }

        $stringLinesArray = array();
        $count = $linesLimit;
        $charactersLeft = $stringLengthLimit;

        foreach ($lines as &$line) {
            $line = self::replaceNewlines($line, '');
            if (strlen($line) >= ($count * $lineStringLengthLimit)) {
                $stringLinesArray[] = self::subString($line, $count * $lineStringLengthLimit);
                break;
            }
            $count = $count - 1;
            if (count($lines) > $linesLimit && $count < 1) {
                $stringLinesArray[] = $line . '...';
            } else {
                $stringLinesArray[] = $line;
            }
            if ($count < 1) {
                break;
            }
        }

        $string = implode(PHP_EOL, $stringLinesArray);

        return $string;
    }

    public static function removeEmptyLinesFromText($text, $onlyStartEnd = true) {
        if ($onlyStartEnd) {
            // Regular trim does not help, preg_replace for trimming unicode whitespace
            // http://stackoverflow.com/questions/4166896/trim-unicode-whitespace-in-php-5-2
            $text = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u','', $text);
        } else {
            $lines = explode(PHP_EOL, $text);

            for ($i = 0; $i < count($lines); $i++) {
                $element = trim(self::replaceNewlines($lines[$i], ''));
                if ($element == '') {
                    unset($lines[$i]);
                }
            }

            $text = implode(PHP_EOL, $lines);
        }

        return $text;
    }

    public static function removeEmptyLinesFromTextStartEnd($text) {

        return self::removeEmptyLinesFromText($text, true);
    }

    public static function traversableToString($traversableOrString, $delimiter = ', ', $parseFunction = null) {
        if ($traversableOrString == null || is_string($traversableOrString)) {

            $traversableOrString = array ($traversableOrString);
        }
        if (!is_array($traversableOrString)) {
            $traversableOrString = $traversableOrString->toArray();
        }
        if ($parseFunction != null) {
            $traversableOrString = array_map($parseFunction, $traversableOrString);
        }

        return implode(', ', $traversableOrString);
    }
} 