<?php
    namespace LiteCheckr\Rules;
    class Example
    {
        // first argument is value when called, subsequent ones used in callback
        public static function exampleFunction($value, $domain_name)
        {
            return true;
        }
    }