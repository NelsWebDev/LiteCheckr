<?php

namespace LiteCheckr;

trait ValidityChecks
{
    public $errors = [];
    /**
     * Check if object passes all error validation
     * @return bool
     */
    public function isValid(): bool
    {
        return  $this->valid();
    }
    /**
     * inverse alias of validate
     * @return bool returns true if validate returns valse
     */
    public function invalid(): bool
    {
        return !$this->valid();
    }

    /**
     * alias of validate
     * @return bool returns true is validation passed
     */
    public function valid(): bool
    {
        return (bool) $this->validate();
    }

    /**
     * Return array of errors
     * @return array
     */
    public function getErrors(): array
    {
        $this->validate();
        return $this->errors;
    }

    abstract public function validate();
}
