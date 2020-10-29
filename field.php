<?php
    namespace LiteCheckr;
    class Field
    {
        public $form;
        public $name;
        private $rules = [];
        public function __construct(&$form_object, $field_name, $field_rules = [] )
        {
            $this->form = $form_object;
            $this->name = $field_name;

            if($field_rules && is_array($field_rules) )
            {
               
                foreach($field_rules as $rule)
                {
                    if(is_string($rule))
                    {
                        call_user_func_array(array($this, "addRule"), [$rule]);
                    }
                    else
                        call_user_func_array(array($this, "addRule"), $rule);
                }
            }
            
        }

        public function form()
        {
            return $this->form;
        }

        public function sibling($sibling_name)
        {
            return $this->form->getField($sibling_name);
        }

        public function addRule($rule_name, $args = [] , $error_message = null) : ?Rule
        {
            if(!isset($this->rules[$rule_name]))
            {
                $rule_object = new Rule($this, $rule_name, $args, $error_message);
                $this->rules[$rule_name] = $rule_object;
                return $this->getRule($rule_name);
            }
                
            else
                die("rule already in use");
        }

        public function getRule($rule_name) : ?Rule 
        {
            return $this->rules[$rule_name] ?: null;
        }

        public function rule($rule_name) : ?Rule
        {
            $rule = $this->getRule($rule_name);
            if($rule)
                return $rule;
            else
                return call_user_func_array(array($this, "addRule"), func_get_args());
        }

        public function value($new_value = null) 
        {
            if(count(func_get_args()) === 1)
                $this->value = $new_value;
            return $this->value;
        }

        public function valid()
        {
            return (bool) !$this->getErrors();
        }
        public function getErrors()
        {
            $errors = [];
            foreach($this->rules as $rule_name => $rule)
            {
                if(!$rule->isMet() )
                    $errors[$rule_name] = $rule->errorMessage;
            }
            return $errors;
        }

        public function invalid()
        {
            return !call_user_func_array(array($this, "valid"), func_get_args());
        }

        public function isValid() 
        {
            return call_user_func_array(array($this, "valid"), func_get_args());
        }

        public function addCustomRule(string $rule_name, callable $rule_callback, $args = [], $error_message = null )
        {
            $this->rules[$rule_name]  = new CustomRule($this, $rule_name, $rule_callback, $args, $error_message);
            return $this->rules[$rule_name];
        }
    }
