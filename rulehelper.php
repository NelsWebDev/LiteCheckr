<?php
    namespace LiteCheckr;

    class RuleHelper
    {
        public static function getIncludedRules() : array
        {
            return (get_class_methods("ValidationLite\Rules\Standard"));
        }

        public static function findIncludedRule($rule_name) : ?String
        {
            if(in_array($rule_name, self::getIncludedRules()))
                return "ValidationLite\\Rules\\Standard" . "::" . $rule_name;
            else
                return null;
        }   

        public static function getOtherClass($function_name) : ?String
        {

                $parts_of_func = explode('.', $function_name);
                $function_name = array_pop($parts_of_func);

                $class_path = "\\" . __NAMESPACE__ . "\\Rules\\";
                $class_path .=  (implode("\\", array_map("ucfirst", $parts_of_func)));
                if(method_exists($class_path, $function_name))
                    return $class_path . "::" . $function_name;
                return null;
             
        }

        
        public static function findRuleFunction($rule_name) : ?String
        {
            if($found = self::findIncludedRule($rule_name))
                return $found;
            return self::getOtherClass($rule_name);
        }
    }
    