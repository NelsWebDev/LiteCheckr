<?php
    namespace LiteCheckr;

    class CustomRule extends Rule
    {
        public function __construct( Field $field_object, string $rule_name, 
                                    callable $rule_callback, $args = [], 
                                    $error_message = null )
        {
            $this->field = $field_object;
            $this->name = $rule_name;

            if(is_string($args) && !is_string($error_message))
            {
                $error_message = $args;
                $args = [];
            }
            if(!$error_message)
                $error_message = "Failed " . $rule_name . " validation";

            $this->errorMessage = $error_message;
            $this->args = $args;
            $this->callbackFunction = $rule_callback;
        }
        public function isMet() : bool
        {
            $full_args = array_merge([$this->field->value()], $this->args);
            return (bool) call_user_func_array($this->callbackFunction, $full_args);
        }
    }