<?php

if (!function_exists('camel2dashed')) {
    /**
     * Get a regular string and implode her using hyphen (-)
     *
     * Example:
     *   Get Fundacao Cearense and transform to fundacao-cearense
     *
     * @param string $value
     *
     * @return string
     */
    function camel2dashed(string $value) : string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $value));
    }
}

if (!function_exists('dashesToCamelCase')) {
    /**
     * Get a regular string and implode CamelCase using n separator
     *
     * Example:
     *   Get fundacao_cearense and transform to FundacaoCearense
     *
     * @param string $value
     * 
     * @return string
     */
    function dashesToCamelCase(string $value, $separator = '-') : string
    {
        $array = explode($separator, $value);
        $parts = array_map('ucwords', $array);
        return implode('', $parts);
    }
}


if (!function_exists('camelToSnake')) {
    /**
     * Get a CamelCase string and implode underscore
     *
     * Example:
     *   Get FundacaoCearense and transform to fundacao_cearense
     *
     * @param string $value
     * 
     * @return string
     */
    function camelToSnake(string $value) : string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }
}

if (!function_exists('getModelClassName')) {
    /**
     * Get by convention that name of the model of this controller it's same your name without the suffix controller
     *
     * Example:
     *   ProductController by convention your model is Product at namespaces App\Models\Product
     *
     * @return string
     */
    function getModelClassName($context) : string
    {
        $name = class_basename(get_class($context));

        if (strpos($name, 'Controller') !== false)
            return substr($name, 0, strpos($name, 'Controller'));
        else if (strpos($name, 'Service') !== false)
            return substr($name, 0, strpos($name, 'Service'));
        else if (strpos($name, 'Repository') !== false)
            return substr($name, 0, strpos($name, 'Repository'));
        else if (strpos($name, 'Policy') !== false)
            return substr($name, 0, strpos($name, 'Policy'));    

        else return $name;
    }
}

if (!function_exists('isCustomRoute')) {
    /**
     * Get by convention that name of the model of this controller it's same your name without the suffix controller
     *
     * Example:
     *   ProductController by convention your model is Product at namespaces App\Models\Product
     *
     * @return string
     */
    function isCustomRoute($url)
    {
        if (strpos($url, 'rest') !== false || strpos($url, 'rpc')) 
            return true;
        else
            return false;
    }
}

if (!function_exists('getModelClassNamespace')) {
    /**
     * By convention that method returns the name of the relative model
     *
     * @return string
     */
    function getModelClassNamespace($context) : string
    {
        $parts = explode('\\', get_class($context));

        return camel2dashed($parts[3]);
    }
}

if (!function_exists('getSchemaName')) {
    /**
     * By convention that method returns the name of the relative model
     *
     * @return string
     */
    function getSchemaName($context) : string
    {
        $parts = explode('\\', get_class($context));
        
        if ($parts[2] == 'Controllers')
            $schemma = (sizeof($parts) == 6)? $parts[sizeof($parts) - 2] . '\\' : '' ;
        else     
            $schemma = (sizeof($parts) == 5)? $parts[sizeof($parts) - 2] . '\\' : '' ;

        return $schemma;
    }
}

if (!function_exists('modelFactory')) {
    /**
     * Return a service object corresponding by convention
     *
     **/
    function modelFactory($context)
    {
        $class_name = 'App\\Models\\' . getSchemaName($context) . getModelClassName($context);

        if (class_exists($class_name))
            return new $class_name;
    }
}

if (!function_exists('repositoryFactory')) {
    /**
     * Return a repository object corresponding by convention
     *
     **/
    function repositoryFactory($context)
    {
        $class_name = 'App\\Repositories\\' . getSchemaName($context) . getModelClassName($context) . 'Repository';

        if (class_exists($class_name))
            return new $class_name;
    }
}

if (!function_exists('serviceFactory')) {
    /**
     * Return a service object corresponding by convention
     *
     **/
    function serviceFactory($context)
    {
        $class_name = 'App\\Services\\Rest\\' . getSchemaName($context) . getModelClassName($context) . 'Service';

        if (class_exists($class_name))
            return new $class_name;
    }
}

if (!function_exists('getViewPath')) {
    /**
     * Get by convention the view path it's the model name with your respective namespace
     *
     * @return string
     */
    function getViewPath($context) : string
    {
        return camel2dashed(getModelClassNamespace($context)) . '.' . camel2dashed(getModelClassName($context));
    }
}

if (!function_exists('compareDates')) {
    /**
     *
     *
     * @return int
     */
    function compareDates($format, $date1, $date2) : int
    {
        $_date1 = \DateTime::createFromFormat($format, $date1);
        $_date2 = \DateTime::createFromFormat($format, $date2);

        if ($_date1 == $_date2)
            return 0;
        if ($_date1 > $_date2)
            return 1;
        if ($_date1 < $_date2)
            return -1;
    }
}

if (!function_exists('toCamelCase')) {
    function toCamelCase($string)
    {
        $string = str_replace('_', ' ', $string);
        return str_replace(' ', '', lcfirst(ucwords($string)));
    }
}