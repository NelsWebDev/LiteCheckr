<?php

namespace LiteCheckr\Rules;

class Standard
{
    public static function required($field_value): bool
    {
        return  (bool) $field_value;
    }

    public static function email($email_address): bool
    {
        return (bool) filter_var($email_address, FILTER_VALIDATE_EMAIL);
    }

    public static function includes($field_value, $needle): bool
    {
        return (strpos($field_value, $needle) !== false);
    }

    public static function contains($field_value, $needle): bool
    {
        return (strpos($field_value, $needle) !== false);
    }

    public static function minLen($field_value, $min_length): bool
    {
        return ( ( is_string($field_value) && strlen($field_value) >= $min_length) || (is_numeric($field_value) && $field_value >= $min_length) );
    }
    public static function maxLen($field_value, $max_length): bool
    {
        return ( ( is_string($field_value) && strlen($field_value) <= $max_length) || (is_numeric($field_value) && $field_value <= $max_length) );
    }
    public static function matches($field_value, $value_to_compare, $comparison_type = "strict"): bool
    {
        $comparison_type = strtolower($comparison_type);
        if ($comparison_type == "strict") {
            return ($field_value === $value_to_compare);
        } elseif (is_string($field_value) && is_string($value_to_compare) &&  in_array($value_to_compare, ["ci", "caseInsensitive", "case_insensitive"])) {
            return (strtolower($field_value) == strtolower($value_to_compare));
        } else {
            return ($field_value == $value_to_compare);
        }
    }
    public static function minMax($field_value, $min_length, $max_length): bool
    {
        return (self::minLen($$field_value, $min_length) && self::maxLen($field_value, $min_length));
    }

    public static function betweenNum($field_value, $min, $max): bool
    {
        return ($field_value >= $min && $field_value <= $max);
    }
}
