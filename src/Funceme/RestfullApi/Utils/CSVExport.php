<?php

namespace Funceme\RestfullApi\Utils;

class CSVExport
{
    public static function toCSV($collection) 
    {
        $keys = [];
        $list = [];

        foreach ($collection as $item) {
            $keys = array_unique(array_merge($keys, CSVExport::getKeys($item->toArray())));
        }

        $list[] = implode(';', $keys);

        foreach ($collection as $item) {

            $item = $item->toArray();

            $_item = [];

            foreach ($keys as $path) {

                $parts = explode('.', $path);

                if (sizeof($parts) == 1)
                    $value = array_key_exists($path, $item)? $item[$path] : null;
                else {
                    $value = array_reduce($parts, function ($o, $p) {                     
                        return (array_key_exists($p, $o))? $o[$p] : null; 
                    }, $item);
                }
                
                if (is_object($value)) {
                    if (get_class($value) == "Phaza\LaravelPostgis\Geometries\Point")
                        $value = $value->toPair();
                    else     
                        $value = '[object] ' . get_class($value);

                }

                $_item[$path] = $value;
            }
            $list[] = implode(';', array_values($_item));
        }
        return implode("\n", $list);        
    }

    private static function getKeys($item, $prefix = null) 
    {        
        $funcPrefix = function($value) use($prefix) {
            return $prefix . '.' . $value;
        };
        $keys = array_keys($item);

        foreach ($keys as $key) {
            if (is_array($item[$key])) {    
                $keys = array_merge($keys, CSVExport::getKeys($item[$key], $key));

                $funcFilter = function($value) use($key) {
                    return $key != $value;
                };

                $keys = array_filter($keys, $funcFilter);
            }
        }

        if ($prefix)
            return array_map($funcPrefix, array_keys($item));

        return $keys;
    }
}