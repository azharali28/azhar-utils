<?php
namespace AzharUtils;

use Exception;

class Validator {

    private array $post;
    private array $rules = [];
    private array $errors = [];

    public function __construct(array $data) {
        $this->post = $data;
    }

    public function setRules(array $rules): void {
        $this->rules = $rules;
    }

    public function validateAll() {
        foreach (array_keys($this->rules) as $field) {
            $this->validate($field);
        }
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function isValid(): bool {
        if (!empty($this->getErrors())) {
            return false;
        }
        return true;
    }
    
    private function validate($fieldName) {
        $value = trim($this->post[$fieldName] ?? "");
        $rules = $this->rules[$fieldName];

        if ($value === "" && !isset($rules['required'])) {
            return;
        }

        foreach ($rules as $rule => $ruleValue) {
            switch ($rule) {
                case 'required':
                    if ($value === "") {
                        $this->errors[$fieldName] = "Field '$fieldName' is required";
                        return;
                    }
                    break;

                case 'int':
                case 'integer':
                case 'numeric':
                    if (!is_numeric($value)) {
                        $this->errors[$fieldName] = "The field '$fieldName' must be numeric (0-9)";
                        return;
                    }
                    break;

                case 'min':
                    if (strlen($value) < $ruleValue) {
                        $this->errors[$fieldName] = "The field '$fieldName' must be at least $ruleValue characters";
                        return;
                    }
                    break;

                    case 'max':
                        if (strlen($value) > $ruleValue) {
                            $this->errors[$fieldName] = "The field '$fieldName' must not exceed $ruleValue characters";
                            return;
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->errors[$fieldName] = "The field '$fieldName' format is incorrect. Emails must contain an '@'";
                            return;
                        }                        
                        break;
    
                    case 'url':
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $this->errors[$fieldName] = "The field '$fieldName' must be a URL";
                            return;
                        }                        
                        break;
    
                    case 'match':
                        if ($this->post[$fieldName] != $this->post[$ruleValue]) {
                            $this->errors[$fieldName] = "'$fieldName' must match '$ruleValue'";
                            return;
                        }                        
                        break;
    
                    case 'regex':
                        if (!preg_match($ruleValue, $value)) {
                            $this->errors[$fieldName] = "The field '$fieldName' is not valid";
                            return;
                        }                        
                        break;
    
                    default:
                        throw new Exception("Validation rule '$rule' is not defined");
                    break;
            }
        }
    }
}
?>