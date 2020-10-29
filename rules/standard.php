<?php
    namespace LiteCheckr\Rules;
    class Standard
    {
        public static function email($value)
        {
            return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
        }



        public static function contains($value, $string_to_search)
        {
            return (strpos($string_to_search, $value) !== false);
        }

        public static function required($value)
        {
            return (!empty($value));
        }
    }