<?php

namespace Hyiplab\BackOffice\Validator;

use Hyiplab\BackOffice\Request;

class Validator{

    private static $rules = [
        'required',
        'min',
        'max',
        'email',
        'confirmed',
        'integer',
        'numeric',
        'gt',
        'lt',
        'gte',
        'lte',
        'file',
        'image',
        'mimes',
        'size',
        'in',
        'required_if',
        'regex',
        'array',
        'url'

    ];

    private static $fieldName;
    private static $inputValue;
    private static $customMessages = [];
    private static $validationRules = [];

    public static function make($validations,$customMessages)
    {
        self::$customMessages = $customMessages;
        foreach ($validations as $validationKey => $rules) {
            if (is_string($rules)) {
                $rules = self::makeArray($rules);
            }
            foreach ($rules as $ruleKey => $rule) {
                $mainRule = self::getMainRule($rule);
                $ruleParam = self::getRuleParam($rule);
                self::validateRule($mainRule);
                $request = new Request();
                self::$fieldName = $validationKey;
                self::$inputValue = $request->$validationKey ?? '';
                self::requireValidation($mainRule);
                if (self::$inputValue) {
                    self::minValidation($mainRule,$ruleParam);
                    self::maxValidation($mainRule,$ruleParam);
                    self::passwordConfirmValidation($mainRule);
                    self::emailValidation($mainRule);
                    self::integerValidation($mainRule);
                    self::numericValidation($mainRule);
                    self::gtValidation($mainRule,$ruleParam);
                    self::gteValidation($mainRule,$ruleParam);
                    self::ltValidation($mainRule,$ruleParam);
                    self::lteValidation($mainRule,$ruleParam);
                    self::fileValidation($mainRule);
                    self::imageValidation($mainRule);
                    self::mimesValidation($mainRule,$ruleParam);
                    self::sizeValidation($mainRule,$ruleParam);
                    self::inValidation($mainRule,$ruleParam);
                    self::regexValidation($mainRule,$ruleParam);
                    self::urlValidation($mainRule);
                }
                self::requiredIfValidation($mainRule,$ruleParam);
                
            }
        }
        return [
            'errors'=>self::$validationRules
        ];
    }

    private static function requireValidation($mainRule){
        $hasValue = false;
        if (self::$inputValue) {
            $hasValue = true;
        }
        if (isset($_FILES[self::$fieldName]) && $_FILES[self::$fieldName] && !$_FILES[self::$fieldName]['tmp_name']) {
            $hasValue = false;
        }
        if ($mainRule == 'required' && !$hasValue) {
            $validateRuleKey = self::$fieldName.'.'.$mainRule;
            self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is required');
        }
    }

    private static function minValidation($mainRule,$ruleParam){
        if ($mainRule == 'min') {
            if (!$ruleParam) {
                throw new \Exception("Limit parameter is missing here"); 
            }
            if (strlen(self::$inputValue) < $ruleParam) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be greater than '.$ruleParam.' characters');
            }
        }
    }

    private static function maxValidation($mainRule,$ruleParam){
        if ($mainRule == 'max') {
            if (!$ruleParam) {
                throw new \Exception("Limit parameter is missing here"); 
            }
            if (strlen(self::$inputValue) > $ruleParam) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be less than '.$ruleParam.' characters');
            }
        }
    }

    private static function emailValidation($mainRule){
        if ($mainRule == 'email') {
            if (!sanitize_email(self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be a valid email');
            }
        }
    }

    private static function passwordConfirmValidation($mainRule){
        if ($mainRule == 'confirmed') {
            $password = self::$inputValue;
            $confirmation = @hyiplab_request()->password_confirmation;
            $validateRuleKey = self::$fieldName.'.'.$mainRule;
            if (!$confirmation) {
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,'Password confirmation field is required');
            }
            if ($confirmation && $password != $confirmation) {
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,'Password confirmation doesn\'t match');
            }
        }
    }

    private static function integerValidation($mainRule){
        if ($mainRule == 'integer') {
            if (!filter_var(self::$inputValue, FILTER_VALIDATE_INT)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be an integer value');
            }
        }
    }

    private static function numericValidation($mainRule){
        if ($mainRule == 'numeric') {
            if (!is_numeric(self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be a numeric value');
            }
        }
    }

    private static function gtValidation($mainRule,$ruleParam){
        if ($mainRule == 'gt') {
            if (!$ruleParam) {
                throw new \Exception("Limit parameter is missing here"); 
            }
            if ($mainRule >= intval(self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be grater than '.$ruleParam);
            }
        }
    }

    private static function gteValidation($mainRule,$ruleParam){
        if ($mainRule == 'gte') {
            if (!$ruleParam) {
                throw new \Exception("Limit parameter is missing here"); 
            }
            if ($mainRule > intval(self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be grater than or equal '.$ruleParam);
            }
        }
    }

    private static function ltValidation($mainRule,$ruleParam){
        if ($mainRule == 'lt') {
            if (!$ruleParam) {
                throw new \Exception("Limit parameter is missing here"); 
            }
            if ($mainRule <= intval(self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be less than '.$ruleParam);
            }
        }
    }

    private static function lteValidation($mainRule,$ruleParam){
        if ($mainRule == 'lte') {
            if (!$ruleParam) {
                throw new \Exception("Limit parameter is missing here"); 
            }
            if ($mainRule < intval(self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be less than or equal '.$ruleParam);
            }
        }
    }

    private static function fileValidation($mainRule){
        if ($mainRule == 'file') {
            if (!is_file(is_array(self::$inputValue) ? self::$inputValue['tmp_name'] : '')) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be a valid file');
            }
        }
    }

    private static function imageValidation($mainRule){
        if ($mainRule == 'image' && self::$inputValue['tmp_name']) {
            $a = getimagesize(is_array(self::$inputValue) ? self::$inputValue['tmp_name'] : 'no-path');
            $image_type = $a[2];
            
            if (!in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field is must be a valid image');
            }
        }
    }

    private static function mimesValidation($mainRule,$ruleParam){
        if ($mainRule == 'mimes' && self::$inputValue['tmp_name']) {
            if (!$ruleParam) {
                throw new \Exception("Mimes parameters is missing here"); 
            }
            $path = $_FILES[self::$fieldName]['name'] ?? '';
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (!in_array($ext,explode(',',$ruleParam))) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' file\'s extension is not a valid extension');
            }
        }
    }

    private static function sizeValidation($mainRule,$ruleParam){
        if ($mainRule == 'size' && self::$inputValue['tmp_name']) {
            if (!$ruleParam) {
                throw new \Exception("Size parameters is missing here"); 
            }
            $size = $_FILES['image']['size'] ?? 0;
            $size = $size / 1024;
            if ($size > $ruleParam || !$size) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' File size could not be greater than '.$ruleParam.' KiloBite');
            }
        }
    }

    private static function inValidation($mainRule,$ruleParam){
        if ($mainRule == 'in') {
            if (!$ruleParam) {
                throw new \Exception("Parameters is missing here"); 
            }
            if (!in_array(self::$inputValue,explode(',',$ruleParam))) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' filed value is not valid');
            }
        }
    }

    private static function requiredIfValidation($mainRule,$ruleParam){
        if ($mainRule == 'required_if') {
            if (!$ruleParam) {
                throw new \Exception("Required if parameters is missing here"); 
            }
            $requestValue = @hyiplab_request()->$ruleParam;
            $hasValue = false;
            if ($requestValue) {
                $hasValue = true;
            }
            if (isset($_FILES[$ruleParam]) && $_FILES[$ruleParam] && !$_FILES[$ruleParam]['tmp_name']) {
                $hasValue = false;
            }
            if ($hasValue && !self::$inputValue) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' filed is required when '.hyiplab_key_to_title($ruleParam).' is exists');
            }
        }
    }

    private static function regexValidation($mainRule,$ruleParam){
        if ($mainRule == 'regex') {
            if (!$ruleParam) {
                throw new \Exception("Regex parameters is missing here"); 
            }
            if (!preg_match($ruleParam,self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field value is not valid');
            }
        }
    }

    private static function urlValidation($mainRule){
        if ($mainRule == 'url') {
            if (!wp_http_validate_url(self::$inputValue)) {
                $validateRuleKey = self::$fieldName.'.'.$mainRule;
                self::$validationRules[$validateRuleKey] = self::getMessage($validateRuleKey,hyiplab_key_to_title(self::$fieldName).' field value is not a valid URL');
            }
        }
    }

    private static function getMessage($validateRuleKey,$message){
        $customMessage = @self::$customMessages[$validateRuleKey];
        if ($customMessage) {
            $message = $customMessage;
        }
        return $message;
    }

    private static function makeArray($str){
        return explode('|',$str);
    }

    private static function getMainRule($rule){
        return explode(':',$rule)[0];
    }

    private static function getRuleParam($rule){
        return @explode(':',$rule)[1];
    }

    private static function validateRule($rule){
        $rules = self::$rules;
        if (!in_array($rule,$rules)) {
            throw new \Exception("$rule is an invalid rule");
        }
    }
}