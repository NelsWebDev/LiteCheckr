<?php

namespace LiteCheckr;

class Field
{
    use ValidityChecks;

    private $value;
    public $rules = [];
    public $name;
    public $formObject;

    /**
     * Create a field for a given form. Field is not added into Form's field array.
     * @param Form $formObject Parent Form Object
     * @param string $fieldName name of Field
     */
    public function __construct(Form $formObject, string $fieldName)
    {
        $this->formobject = $formObject;
        $this->name = $fieldName;
    }
    /**
     * Update (optional) and return field's value
     * @param mixed|null $newValue new field value
     */
    public function value($newValue = null)
    {
        if (count(func_get_args()) === 1) {
            $this->value = $newValue;
        }
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value();
    }

    /**
     * Checks if field value's rule requirements are met. Updates rules error array accordingly.
     * @return bool Returns true if field value is valid.
     */
    public function validate(): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->validate()) {
                $this->errors[$rule->name]  = $rule->errorMessage;
            }
        }
        return (bool) empty($this->errors);
    }

    /**
     *
     * @param string $ruleName Name of rule function to test. (Example: required, standard.required, example.test)
     * @param array|string|null $arguments_or_error Optional array containing additional rule function arguments. Can also set error message instead
     * @param string|null $error_message Error message used when rule is not met.
     * @return Field
     */
    public function addRule(string $ruleName, $arguments_or_error = [], string $error_message = null): self
    {
        if ($this->ruleExists($ruleName)) {
            error_log("Rule \"{$ruleName}\" is already in use", E_USER_NOTICE);
        }

        $bulk_call = array_column(debug_backtrace(), "function");
        $bulk_call = in_array("addFields", $bulk_call);
        if (is_string($arguments_or_error) && !is_string($error_message) && !$bulk_call) {
            $error_message = $arguments_or_error;
            $arguments_or_error = [];
        }
        if (!is_array($arguments_or_error)) {
            $arguments_or_error = [];
        }

        $this->rules[$ruleName] = new Rule($this, $ruleName, $arguments_or_error);
        $rule = $this->getRule($ruleName);
        $rule->setErrorMessage($error_message);
        return $this;
    }

    /**
     * Returns the rule object of a given rule
     * @param string $ruleName
     * @return null|Rule
     */
    public function getRule(string $ruleName): ?Rule
    {
        if ($this->ruleExists($ruleName)) {
            return $this->rules[$ruleName];
        }
        error_log("Rule \"{$ruleName}\" is not in use", E_USER_NOTICE);
        return null;
    }
    /**
     * Delete previously included rule
     * @param string $ruleName rule to delete
     * @return Field Parent field object
     */
    public function deleteRule(string $ruleName): self
    {
        if (!$this->ruleExists(($ruleName))) {
            error_log("Rule \"{$ruleName}\" is not in use", E_USER_NOTICE);
        } else {
            unset($this->rules[$ruleName]);
        }
        return $this;
    }

    /**
     * Checks if rule is defined
     * @param string $ruleName rule name to check
     */
    public function ruleExists(string $ruleName): bool
    {
        return key_exists($ruleName, $this->rules);
    }

    /**
     * Shortcut combination of getRule and addRule. Adds rule if rule not in use. For more information see Fieldf.addRule
     * @param string $ruleName name of rule
     * @return Rule Rule object
     */
    public function rule(string $ruleName): Rule
    {
        if (!$this->ruleExists($ruleName)) {
            call_user_func_array(array($this, "addRule"), func_get_args());
        }
        return $this->getRule($ruleName);
    }

    /**
     * Returns array of defined rule objects
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Set the error message of a rule field. Used to access protected property.
     * @param mixed $rule_name
     * @param mixed $error_message
     * @return Field
     */
    public function setError($rule_name, $error_message): self
    {
        $this->errors[$rule_name] = $error_message;
        return $this;
    }

    /**
     * @return Form Field's Parent Form
     */
    public function form(): Form
    {
        return $this->formObject;
    }
}
