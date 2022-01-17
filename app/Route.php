<?php
namespace App;

class Route
{
    public static $route_list = [];

    public static function get($uri,$action)
    {
        self::$route_list['GET'][$uri]=$action;
    }

    public static function post($uri,$action)
    {
        self::$route_list['POST'][$uri]=$action;
    }

    public static function resolve($uri,$method)
    {
        $action = self::$route_list[$method][$uri];
        if(!$action)
            return "not found";
        $classObj = new $action[0];
        echo call_user_func_array([$classObj,$action[1]],[]);
    }
}