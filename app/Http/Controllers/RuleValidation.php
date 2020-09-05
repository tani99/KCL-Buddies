<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection as Collection;
use App\Rule as Rule;

trait RuleValidation
{
    /**
     * @param Collection|static[] $rules All the rules to validate
     * @return array An array of Laravel validation rules for the scheme rules input.
     */
    protected final function createRulesInputValidation($rules): array
    {
        if (!isset($rules)) $rules = Rule::all();
        $inputValidation = [];
        foreach ($rules as $rule) {
            $ruleValidation = isset($rule->validation) ? json_decode($rule->validation) : null;
            if (!isset($ruleValidation)) {
                continue;
            }
            $ruleValidationArray = [];
            if (isset($ruleValidation->type)) {
                $ruleValidationArray[] = $ruleValidation->type;
            }
            if (isset($ruleValidation->min)) {
                $ruleValidationArray[] = 'min:' . $ruleValidation->min;
            }
            if (isset($ruleValidation->max)) {
                $ruleValidationArray[] = 'max:' . $ruleValidation->max;
            }
            if (isset($ruleValidation->custom)) {
                $ruleValidationArray[] = $ruleValidation->custom;
            }

            $ruleNameLc = strtolower(str_replace(' ', '_', $rule->name));
            $inputValidation[$ruleNameLc] = 'required' . (!empty($ruleValidationArray) ? '|' . implode('|', $ruleValidationArray) : '');
        }
        return $inputValidation;
    }
}