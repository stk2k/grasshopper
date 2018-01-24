<?php
namespace Grasshopper\debug;

class CurlDebug
{
    /**
     * Print cUrl options
     *
     * @param array $options
     */
    public static function printOptions( $options )
    {
        if ( $options instanceof \Traversable || is_array($options) ){
            foreach($options as $key => $value){
                $key = self::getOptionName($key);
                switch(gettype($value)){
                case 'array':
                    echo "$key => " . print_r($value,true) . PHP_EOL;
                    break;
                case 'object':
                    echo "$key => class " . get_class($value) . PHP_EOL;
                    break;
                case 'function':
                    try{
                        $func = new \ReflectionFunction($value);
                        echo "$key => function " . $func->getName(). PHP_EOL;
                    }
                    catch(\ReflectionException $e){
                        echo "$key => unknown function" . PHP_EOL;
                    }
                    break;
                default:
                    echo "$key => $value" . PHP_EOL;
                    break;
                }
            }
        };
    }

    /**
     * Get string for option key
     *
     * @param integer $key
     *
     * @return string
     */
    public static function getOptionName( $key )
    {
        $arr = get_defined_constants(true);
        $curl_vars = isset($arr['curl']) ? $arr['curl'] : null;
        if ( !is_array($curl_vars) ){
            return '';
        }
        foreach($curl_vars as $k => $v){
            if (strpos($k, 'CURLOPT_') === false){
                unset($curl_vars[$k]);
            }
        }
        $curl_vars = array_flip($curl_vars);

        return isset($curl_vars[$key]) ? $curl_vars[$key] : '';
    }
}