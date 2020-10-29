<?php
    namespace LiteCheckr;
    require "bootstrap.php";
    class Form

    {
        private $fields = [];
        public function __construct($fields) 
        {
            $passed_fields = [];
            foreach(func_get_args() as $field)
            {
                if(is_array($field))
                    call_user_func_array(array($this, "field"), $field);
                elseif(is_string($field))
                    $this->field($field);
            }
        }

        public function field($field_name) : ?Field
        {
            if($field = $this->getField($field_name))
                return $field;
            return call_user_func_array(array($this, "addField"), func_get_args());
        }

        public function addField($field_name, $rules = []) : ?Field
        {
            if(!isset($this->fields[$field_name]))
            {
                $field_object = new Field($this, $field_name, $rules);
                $this->fields[$field_name] = $field_object;
                return $this->getField($field_name);
            }
                
            else
                die("field already in use");
        }

        public function getField($field_name) : ?Field
        {
            return $this->fields[$field_name] ?: null;
        }

        public function getFields() : ?array
        {
            return $this->fields;
        }

        public function valid() :bool
        {
            return (bool) !$this->getErrors();
        }



        public function getErrors() : array
        {
            $all_errors = [];
            foreach($this->getFields() as $field_name => $field_object)
            {
                if($errors = $field_object->getErrors() )
                {
                    $all_errors[$field_name] = ($errors);
                }
            }
            return $all_errors;
        }

        public function setValues(array $values_array) : Form
        {
            foreach($values_array as $field_name => $field_value)
            {
                if(!is_string($field_name))
                    die("field names must be string");
                $field_object = $this->field($field_name);
                $field_object->value($field_value);
            }
            return $this;
        }


        public function invalid()
        {
            return !call_user_func_array(array($this, "valid"), func_get_args());
        }

        public function isValid() 
        {

            return call_user_func_array(array($this, "valid"), func_get_args());
        }

        public function getAllValues()
        {
            $fields = [];
            foreach($this->getFields() as $field_name => $field_object)
            {
                $fields[$field_name] = $field_object->value();
            }
            return $fields;
        }

    }
