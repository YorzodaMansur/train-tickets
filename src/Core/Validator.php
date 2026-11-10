<?php
namespace App\Core;

class Validator {
    private array $errors = [];

    public function validate(array $data, array $rules): bool {
        $this->errors = [];
        foreach ($rules as $field => $fieldRules) {
            $value = trim((string)($data[$field] ?? ''));
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && $value === '') {
                    $this->errors[$field] = "Майдони «{$field}» ҳатмист.";
                    break;
                }
                if ($value !== '') {
                    if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field] = "Суроғаи почтаи электронӣ нодуруст аст.";
                    }
                    if ($rule === 'numeric' && !is_numeric($value)) {
                        $this->errors[$field] = "Ин майдон бояд танҳо адад бошад.";
                    }
                    if (str_starts_with($rule, 'min:')) {
                        $min = (int)substr($rule, 4);
                        if (mb_strlen($value, 'UTF-8') < $min) {
                            $this->errors[$field] = "Дарозии камтарин {$min} аломат аст.";
                        }
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getFirstError(): ?string {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
