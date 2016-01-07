<?php
namespace Grasshopper\util;


class Sanitizer
{
    /*
     * remove control characters from string
     *
     * @param string|array $data       Input string or array which may include control characters
     *
     * preturn string        control characters removed string
     */
    public static function removeControlChars($data)
    {
        if ( is_array($data) ){
            foreach( $data as $item ){
                self::removeControlChars($item);
            }
        }
        if ( is_string($data) ){
            return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
        }
        return $data;
    }
}