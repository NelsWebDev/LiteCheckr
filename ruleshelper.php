<?php

namespace LiteCheckr;

class RulesHelper
{
    public static $rules_ns = "LiteCheckr\\Rules";
    public static $std_class = "LiteCheckr\\Rules\\Standard";
    public static function getIncludedRules(): array
    {
        return (get_class_methods(self::$std_class)) ?: [];
    }
    /**
     * Gets the full path of a rule function in the standard rule library.
     * @return null|string full path of rule function
     */
    public static function findIncludedRule($rule_name): ?string
    {
        if (in_array($rule_name, self::getIncludedRules())) {
            return self::$std_class . "::" . $rule_name;
        } else {
            return null;
        }
    }

    /**
     * Get the full path of a rule function included in the Rules namespace
     * @param string $function_name name of rule using dot notation. standard.required
     * @return null|string
     */
    public static function findExtendedFunction(string $function_name): ?string
    {
        $parts_of_func = explode('.', $function_name);
        if (count($parts_of_func) == 1) {
            goto notfound;
        }
        $local_function_name = array_pop($parts_of_func);
        $class_path = array_map("ucfirst", $parts_of_func);
        $class_path = implode("\\", $class_path);
        $file_path = __DIR__ . "/rules/";
        $file_path .= strtolower(str_replace("\\", "/", $class_path));
        $file_path .= ".php";
        if (!file_exists($file_path)) {
            error_log("Cannot find " . $file_path, E_USER_ERROR);
            die();
        }
        if (!method_exists(self::$rules_ns . "\\" . $class_path, $local_function_name)) {
            notfound:
            error_log("Rule \"{$function_name}\" not found");
            die();
        }
        $full_path = "\\" . self::$rules_ns . "\\" . $class_path . "::" . $local_function_name;
        return $full_path;
    }
    /**
     * Returns a string containg the full path of the rule function. Aborts upon failure
     * @param string $ruleName class and function in dot notation (required, standard.required, example.orgEmail )
     * @return null|string
     */
    public static function findRuleFunction(string $ruleName): ?string
    {
        if ($found = self::findIncludedRule($ruleName)) {
            return $found;
        }
        return self::findExtendedFunction($ruleName);
    }
}

RulesHelper::findRuleFunction("required");
