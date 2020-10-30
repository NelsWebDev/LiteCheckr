<?php

namespace LiteCheckr;

use ReflectionException;
use ReflectionClass;
use ReflectionReference;

class RulesHelper
{
    public const RULES_NAMESPACE = "LiteCheckr\\Rules";
    public const STD_CLASS = "LiteCheckr\\Rules\\Standard";
    
    public static function getIncludedRules(): array
    {
        return (get_class_methods(self::STD_CLASS)) ?: [];
    }
    /**
     * Get the full path of a rule function included in the Rules namespace
     * @param string $function_name name of rule using dot notation. standard.required
     * @return null|string
     */
    public static function ruleToFunc($rule): ?String
    {
        $parts_of_function = explode(".", $rule);

        if (count($parts_of_function) == 1) {
            $function_name = $rule;
            $class_path = self::STD_CLASS;
            $function_path = $class_path . "::" . $function_name;
        } else {
            $function_name = $parts_of_function[array_key_last($parts_of_function)];

            $parts_of_class = array_slice($parts_of_function, 0, -1);
            $parts_of_class = array_map("ucfirst", $parts_of_class);
            $class_path = self::RULES_NAMESPACE . "\\" . implode("\\", $parts_of_class);

            $function_path = $class_path . "::" . $function_name;
        }

        if (method_exists($class_path, $function_name))
            return $function_path;
        else
            return null;
    }
}
