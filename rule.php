<?php
    namespace LiteCheckr;
    class Rule
    {
        public $field;
        public $name;
        private $callbackFunction;
        public $errorMessage;
        public function __construct($field_object, $rule_name, $args = [], $error_message = null)
        {

            $this->field = &$field_object;
            $this->name = $rule_name;
            $this->callbackFunction = RuleHelper::findRuleFunction($rule_name);

            if(is_string($args) && !is_string($error_message))
            {
                $error_message = $args;
                $args = [];
            }
            if(!$error_message)
                $error_message = "Failed " . $rule_name . " validation";

            $this->errorMessage = $error_message;
            $this->args = $args;
        }

        public function isMet() : bool
        {
            $full_args = array_merge([$this->field->value()], $this->args);
            return (bool) call_user_func_array($this->callbackFunction, $full_args);
        }
        public function nextRule() : ?Rule
        {
            return call_user_func_array(array($this->field, "rule"), func_get_args());
        }
        public function field()
        {
            return $this->field;
        }

        public function form()
        {
            return $this->field()->form();
        }
    }