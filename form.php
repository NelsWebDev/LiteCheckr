<?php

namespace LiteCheckr;

class Form
{
    use ValidityChecks;

    /**
     * @var array Array containing field objects
     */
    private $fields = [];
    /**
     * Optionally create fields assocated with the form. See addFields for details.
     * @param null|array $fields Array of fields to be created.
     */
    public function __construct(?array $fields = [])
    {
        $this->addFields($fields);
    }

    /**
     * @return array Array of Field Objects
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * Returns an associative array of form fields and their values
     * @return array Form field names and their values
     */
    public function getValues(): array
    {

        $field_names = array_keys($this->fields);
        foreach ($field_names as $field_name) {
            $values[$field_name] = $this->getField($field_name)->value();
        }
        return $values ?: [];
    }


    /**
     * Bulk update value of form fields.
     * @param array $valuesToUpdate Assocative array of field values.
     * @return Form Parent Form object
     */
    public function setValues(array $valuesToUpdate): self
    {
        foreach ($valuesToUpdate as $field_name => $field_value) {
            if (is_int($field_name)) {
                error_log("Array parsed should be associative", E_USER_ERROR);
                continue;
            }

            $this->field($field_name)->value($field_value);
        }
        return $this;
    }

    /**
     * Allows the bulk creation of field objects.
     * Can use single dimension array ['field1', 'field2']
     *
     * Advanced cusomization via [ ["name" => "field1", "required"] ]
     * @param array $fields array of fields to create
     * @return Form
     */
    public function addFields(array $fields): self
    {
        if ($fields) {
            foreach ($fields as $field_index => $field) {
                if (is_string($field)) {
                    $field = ['name' => $field];
                }
                if (!$field || !is_int($field_index)) {
                    error_log("Invalid fields array");
                    continue;
                }
                if (!key_exists("name", $field) || !is_string($field['name'])) {
                    error_log("Field name is required!", E_USER_ERROR);
                    continue;
                }
                $settings = [
                    $field['name'],
                    $field['rules']  ?: [],
                ];
                if (key_exists("errorMessage", $field)) {
                    $settings[] = $field['errorMessage'];
                }

                call_user_func_array(array($this, "addField"), $settings);
            }
        }
        return $this;
    }

    /**
     * Hybrid of getField and addField.
     *
     * Creates field if not in use (can use same getField arguments)
     * @param string $fieldName field object to obtain
     */
    public function field(string $fieldName)
    {
        if (!$this->fieldExists($fieldName)) {
            call_user_func_array(array($this, "addField"), func_get_args());
        }
        return $this->getField($fieldName);
    }

    /**
     * Check if field object exists in Form array
     * @return bool returns true if found, false if not
     */
    public function fieldExists(string $fieldName): bool
    {
        return key_exists($fieldName, $this->fields);
    }

    /**
     * Creates a new field object for form.
     *
     * Name is required
     *
     * Additional fields passed into Field.addRule function
     * @param string $fieldName
     * @return Field Newly created field object
     */
    public function addField(string $fieldName): Field
    {


        if ($this->fieldExists($fieldName)) {
            error_log("Field already in use", E_USER_ERROR);
        }


        $other_args = func_get_args();
        array_shift($other_args);
        $field_object = new Field($this, $fieldName);

        if (count($other_args) === 1 && is_array($other_args[0])) {
            // options array of multiple rules
            foreach ($other_args[0] as $argument_index => $argument_array) {
                if (is_string($argument_array)) {
                    $argument_array = ["rule" => $argument_array];
                }
                if (!is_int($argument_index) || !is_array($argument_array) || !isset($argument_array['rule']) || !is_string($argument_array['rule'])) {
                    error_log("Invalid options for  {$fieldName} field ");
                    continue;
                }
                if (!key_exists('args', $argument_array)) {
                    $argument_array['$argument_array'] = [];
                }
                if (!key_exists('errorMessage', $argument_array)) {
                    $argument_array['errorMessage'] = null;
                }

                $field_object->addRule($argument_array['rule'], $argument_array['args'], $argument_array['errorMessage']);
            }
        } elseif (count($other_args) >= 1 && is_string($other_args[0])) {
            // assumed that only one rule is required, and settings included here
            call_user_func_array(array($field_object, "addRule"), $other_args);
        }
        $this->fields[$fieldName] = $field_object;
        return $field_object;
    }

    /**
     * @param string $fieldName name of field object to obtain
     * @return Field Field Name's field object
     */
    public function getField(string $fieldName): Field
    {
        return $this->fields[$fieldName];
    }

    /**
     * Checks if all rule requirements are met for this form, updating error array accordingly.
     *
     * @return bool field validation status.
     */
    public function validate(): bool
    {
        foreach ($this->fields as $field) {
            if (!$field->validate()) {
                $this->errors[$field->name] = $field->errors;
            }
        }
        return empty($this->errors);
    }
}
