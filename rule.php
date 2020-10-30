<?php

namespace LiteCheckr;

class Rule
{
    use ValidityChecks;

    public $args = [];
    public $errorMessage;
    public $name;
    public $field;

    /**
     *
     * @param Field $field_object Parent Field Object
     * @param string $ruleName name of rule function to use.
     * Use dot notation for non standard rules (standard.rulefunction)
     * @param null|array $callbackArgs Optional additional arguments passed onto rules function.
     */
    public function __construct(Field $field_object, string $ruleName, ?array $callbackArgs)
    {
        $this->setArguments($callbackArgs);
        $this->name = $ruleName;
        $this->field = $field_object;
    }
    /**
     * Sets the error message of a rule
     * @param null|string $errorMessage new error message
     * @return Rule Rule object whos error message was updated.
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * Updates array of arguments that are passed onto rules function
     * @param array $arguments Non assocative array of values used as arguments following field value.
     */
    public function setArguments(array $arguments): self
    {
        $this->args = $arguments;
        return $this;
    }


    /**
     * Checks if rule requirment is met.
     * @return bool returns true if function evaluates to boolean true
     */
    public function validate(): bool
    {
        $path = RulesHelper::findRuleFunction($this->name);

        $all_arguments = array_merge([$this->field->value()], $this->getArguments());
        return  (bool) call_user_func_array($path, $all_arguments);
    }
    public function getArguments()
    {
        return $this->args;
    }
}
