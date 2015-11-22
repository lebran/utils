<?php
/**
 * Created by PhpStorm.
 * User: Roma
 * Date: 12.11.2015
 * Time: 23:53
 */

namespace Lebran\Utils;

class Inflector
{
    protected static $singular_regex = [
        '/^(.*?us)$/i'                => '\\1',
        '/^(.*?[sxz])es$/i'           => '\\1',
        '/^(.*?[^aeioudgkprt]h)es$/i' => '\\1',
        '/^(.*?[^aeiou])ies$/i'       => '\\1y',
        '/^(.*?)s$/'                  => '\\1',
    ];

    protected static $plural_regex = [
        '/^(.*?[sxz])$/i'           => '\\1es',
        '/^(.*?[^aeioudgkprt]h)$/i' => '\\1es',
        '/^(.*?[^aeiou])y$/i'       => '\\1ies'
    ];

    /**
     * Gets singular form of a noun
     *
     * @param string $string Noun to get singular form of
     *
     * @return string Singular form of the noun
     */
    public static function singular($string)
    {
        foreach (static::$singular_regex as $key => $value) {
            $string = preg_replace($key, $value, $string, -1, $count);
            if ($count) {
                return $string;
            }
        }
        return $string;
    }

    /**
     * Gets plural form of a noun
     *
     * @param string $string Noun to get a plural form of
     *
     * @return string  Plural form
     */
    public static function plural($string)
    {
        foreach (static::$plural_regex as $key => $value) {
            $string = preg_replace($key, $value, $string, -1, $count);
            if ($count) {
                return $string;
            }
        }
        return $string.'s';
    }
}